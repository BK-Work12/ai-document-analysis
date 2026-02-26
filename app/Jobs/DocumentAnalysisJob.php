<?php

namespace App\Jobs;

use App\Events\DocumentStatusUpdated;
use App\Models\Document;
use App\Models\User;
use App\Notifications\ChatMessageNotification;
use App\Services\AI\DocumentAnalysisService;
use App\Services\DocumentStatusService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Exception;
use Throwable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Document Analysis Job
 * 
 * Async job to analyze extracted text using AWS Bedrock
 * 
 * Flow:
 * 1. Retrieve extracted text from document
 * 2. Call Bedrock via DocumentAnalysisService
 * 3. Parse and store analysis results
 * 4. Extract risk flags and missing fields
 * 5. Update document status
 */
class DocumentAnalysisJob implements ShouldQueue
{
    use Queueable;

    /**
     * Cached list of columns for documents table.
     */
    protected static ?array $documentColumns = null;

    /**
     * The number of times the job may be attempted.
     * Only retry on actual transient failures (e.g., AWS service timeout).
     */
    public int $tries = 2;

    /**
     * The maximum number of seconds to wait before retrying the job.
     */
    public int $backoff = 300; // 5 minutes

    public function __construct(
        public Document $document,
    ) {}

    /**
     * Execute the job
     */
    public function handle(DocumentAnalysisService $analysisService, DocumentStatusService $statusService): void
    {
        try {
            // Refresh model to avoid stale queued model state
            $this->document->refresh();

            if (in_array($this->document->extraction_status, ['pending', 'processing'], true)) {
                $this->release(20);
                return;
            }

            $hasExtractedText = $this->hasExtractedText();
            $canAnalyzeDirect = $this->canAnalyzeDirectWithClaude();

            if (!$hasExtractedText && !$canAnalyzeDirect) {
                throw new Exception('Document must be extracted before analysis. Extraction failed and file is not Claude vision compatible.');
            }

            // Mark as processing
            $this->updateDocument([
                'analysis_status' => 'processing',
                'analysis_started_at' => now(),
            ]);

            // Analyze either extracted text or the original image directly via Claude vision
            if ($hasExtractedText) {
                $result = $analysisService->analyzeDocument(
                    $this->document->extracted_text,
                    $this->document->original_filename,
                    $this->document->doc_type
                );
            } else {
                $result = $analysisService->analyzeDocumentFromImage(
                    $this->getDocumentBytesForClaude(),
                    (string)$this->document->detected_mime,
                    $this->document->original_filename,
                    $this->document->doc_type
                );
            }

            if (!$result['success']) {
                throw new Exception($result['error'] ?? 'Unknown error during analysis');
            }

            // Extract key information from analysis
            $analysisResult = $result['analysis_result'];
            $riskFlags = $analysisResult['risk_flags'] ?? [];
            $missingFields = $analysisResult['missing_fields'] ?? [];
            $validationChecks = $analysisResult['validation_checks'] ?? [];
            $extractedData = $analysisResult['extracted_data'] ?? [];
            $failedChecks = $this->extractFailedValidationChecks($validationChecks);
            $classification = $analysisResult['classification'] ?? $this->document->doc_type;
            $classification = substr($classification, 0, 255);
            $confidenceScore = $result['confidence_score'] ?? 0;

            // Store analysis results
            $this->updateDocument([
                'analysis_result' => json_encode($analysisResult),
                'analysis_status' => 'completed',
                'analysis_completed_at' => now(),
                'analysis_metadata' => $result['metadata'],
                'classified_doc_type' => $classification,
                'risk_flags' => $riskFlags ? json_encode($riskFlags) : null,
                'missing_fields' => $missingFields ? json_encode($missingFields) : null,
                'confidence_score' => $confidenceScore,
            ]);

            // Create flags in database if risk flags were detected
            if (!empty($riskFlags)) {
                $this->createDocumentFlags($riskFlags);
            }

            // Auto-tag document based on analysis
            $this->autoTagDocument($classification, $confidenceScore);

            // Create auto-review
            $this->createAutoReview($analysisResult, $confidenceScore, $riskFlags);

            // Auto-request correction if issues detected, otherwise auto-approve only when confidence is > 85
            $analysisInsufficient = $this->isAnalysisInsufficient(
                $classification,
                $confidenceScore,
                $extractedData,
                $validationChecks
            );

            $normalizedClassifiedType = $this->normalizeDocTypeForComparison($classification);
            $normalizedExpectedType = $this->normalizeDocTypeForComparison((string) $this->document->doc_type);
            $classificationMismatch = $normalizedClassifiedType !== ''
                && $normalizedExpectedType !== ''
                && $normalizedClassifiedType !== $normalizedExpectedType;
            $hasRiskFlags = !empty($riskFlags);
            $hasMissingFields = !empty($missingFields);
            $hasFailedChecks = !empty($failedChecks);

            $hasCorrectionIssues = $classificationMismatch
                || $hasRiskFlags
                || $hasMissingFields
                || $hasFailedChecks;

            $canAutoApprove = $this->canAutoApprove(
                $classificationMismatch,
                $hasRiskFlags,
                $hasMissingFields,
                $hasFailedChecks,
                $analysisInsufficient,
                $confidenceScore,
                $extractedData,
                $validationChecks
            );
            
            if ($hasCorrectionIssues) {
                $this->autoRequestCorrection($statusService, $classification, $riskFlags, $missingFields, $failedChecks);
            } elseif ($canAutoApprove) {
                // No correction issues and confidence is strong enough - approve directly.
                $previousStatus = $this->document->status;
                $this->updateDocument(['status' => 'approved']);
                DocumentStatusUpdated::dispatch($this->document->refresh(), $previousStatus, 'approved');
            }

            \Illuminate\Support\Facades\Log::info('DocumentAnalysisJob completed', [
                'document_id' => $this->document->id,
                'status' => $this->document->status,
                'confidence_score' => $confidenceScore,
                'risk_flags_count' => count($riskFlags),
                'analysis_insufficient' => $analysisInsufficient,
                'has_correction_issues' => $hasCorrectionIssues,
                'can_auto_approve' => $canAutoApprove,
            ]);

        } catch (Exception $e) {
            $this->updateDocument([
                'analysis_status' => 'failed',
                'analysis_error' => $e->getMessage(),
                'analysis_completed_at' => now(),
            ]);

            \Illuminate\Support\Facades\Log::error('DocumentAnalysisJob failed', [
                'document_id' => $this->document->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function hasExtractedText(): bool
    {
        return is_string($this->document->extracted_text)
            && trim($this->document->extracted_text) !== '';
    }

    protected function canAnalyzeDirectWithClaude(): bool
    {
        $mime = is_string($this->document->detected_mime)
            ? strtolower($this->document->detected_mime)
            : '';

        return in_array($mime, [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp',
            'image/gif',
        ], true);
    }

    protected function getDocumentBytesForClaude(): string
    {
        $s3Key = $this->document->s3_key;
        if (!is_string($s3Key) || trim($s3Key) === '') {
            throw new Exception('Cannot analyze directly: missing document storage key.');
        }

        $useLocal = env('USE_LOCAL_STORAGE', true);
        $disk = $useLocal ? 'local' : 's3';

        if (!Storage::disk($disk)->exists($s3Key)) {
            throw new Exception('Cannot analyze directly: document file not found in storage.');
        }

        return Storage::disk($disk)->get($s3Key);
    }

    protected function isAnalysisInsufficient(
        string $classification,
        float $confidenceScore,
        array $extractedData,
        array $validationChecks
    ): bool {
        if (trim($classification) === '') {
            return true;
        }

        if ($confidenceScore <= 0) {
            return true;
        }

        if (empty($extractedData) && empty($validationChecks)) {
            return true;
        }

        return false;
    }

    protected function canAutoApprove(
        bool $classificationMismatch,
        bool $hasRiskFlags,
        bool $hasMissingFields,
        bool $hasFailedChecks,
        bool $analysisInsufficient,
        float $confidenceScore,
        array $extractedData,
        array $validationChecks
    ): bool {
        if ($classificationMismatch || $hasRiskFlags || $hasMissingFields || $hasFailedChecks || $analysisInsufficient) {
            return false;
        }

        // Require strong confidence for fully automatic approval.
        if ($confidenceScore <= 85) {
            return false;
        }

        // Require evidence that review actually checked content.
        if (empty($extractedData) || empty($validationChecks)) {
            return false;
        }

        return true;
    }

    /**
     * Create DocumentFlag records for each risk flag
     * 
     * @param array $riskFlags
     * @return void
     */
    protected function createDocumentFlags(array $riskFlags): void
    {
        foreach ($riskFlags as $flag) {
            if (is_array($flag)) {
                $flagType = $flag['type'] ?? 'risk';
                $description = $flag['description'] ?? $flag['message'] ?? '';
                $severity = $this->normalizeFlagSeverity($flag['severity'] ?? 'medium');
            } else {
                $flagType = 'risk';
                $description = (string)$flag;
                $severity = 'medium';
            }

            $this->document->flags()->create([
                'flag_type' => $flagType,
                'description' => $description,
                'severity' => $severity,
                'flagged_at' => now(),
            ]);
        }
    }

    protected function normalizeFlagSeverity(mixed $rawSeverity): string
    {
        $value = strtolower(trim((string) $rawSeverity));

        if ($value === '') {
            return 'medium';
        }

        return match ($value) {
            'low', 'minor', 'info', 'informational' => 'low',
            'high', 'critical', 'severe', 'urgent', 'blocker' => 'high',
            default => 'medium',
        };
    }

    /**
     * Auto-tag document based on classification and confidence
     * 
     * @param string $classification
     * @param float $confidenceScore
     * @return void
     */
    protected function autoTagDocument(string $classification, float $confidenceScore): void
    {
        // Get tags from database
        $classificationTag = \App\Models\Tag::where('slug', Str::slug($classification))->first();
        if ($classificationTag) {
            $this->document->tags()->attach($classificationTag->id, [
                'reason' => 'Auto-classified via AI analysis',
                'confidence' => $this->getConfidenceLevel($confidenceScore),
                'manual' => false,
                'tagged_at' => now(),
            ]);
        }

        // Add confidence tag
        $confidenceTag = \App\Models\Tag::where('category', 'confidence')
            ->where('name', 'like', '%' . $this->getConfidenceLevel($confidenceScore) . '%')
            ->first();
        if ($confidenceTag) {
            $this->document->tags()->attach($confidenceTag->id, [
                'reason' => 'Confidence score: ' . $confidenceScore . '%',
                'confidence' => 'high',
                'manual' => false,
                'tagged_at' => now(),
            ]);
        }

        // Add status tag
        $statusTag = \App\Models\Tag::where('category', 'status')
            ->where('name', 'Auto-Reviewed')
            ->first();
        if ($statusTag) {
            $this->document->tags()->attach($statusTag->id, [
                'reason' => 'Automatic AI review completed',
                'confidence' => 'high',
                'manual' => false,
                'tagged_at' => now(),
            ]);
        }
    }

    /**
     * Get confidence level label
     * 
     * @param float $score
     * @return string
     */
    protected function getConfidenceLevel(float $score): string
    {
        if ($score >= 85) {
            return 'high';
        } elseif ($score >= 70) {
            return 'medium';
        }
        return 'low';
    }

    /**
     * Create auto-review record
     * 
     * @param array $analysisResult
     * @param float $confidenceScore
     * @param array $riskFlags
     * @return void
     */
    protected function createAutoReview(array $analysisResult, float $confidenceScore, array $riskFlags): void
    {
        // Determine review status based on confidence and flags
        $reviewStatus = 'pending';
        if ($confidenceScore > 85 && empty($riskFlags)) {
            $reviewStatus = 'approved';
        } elseif (!empty($riskFlags)) {
            $reviewStatus = 'needs_revision';
        }

        \App\Models\DocumentReview::create([
            'document_id' => $this->document->id,
            'review_status' => $reviewStatus,
            'auto_review_results' => $analysisResult,
            'quality_score' => (int)$confidenceScore,
            'auto_reviewed_at' => now(),
            'review_notes' => $this->generateReviewNotes($confidenceScore, $riskFlags),
        ]);
    }

    /**
     * Generate review notes from analysis
     * 
     * @param float $confidenceScore
     * @param array $riskFlags
     * @return string
     */
    protected function generateReviewNotes(float $confidenceScore, array $riskFlags): string
    {
        $notes = "Auto-review completed with " . $confidenceScore . "% confidence.\n";

        if (empty($riskFlags)) {
            $notes .= "No risk flags identified.";
        } else {
            $notes .= "Risk flags identified:\n";
            foreach ($riskFlags as $flag) {
                if (is_array($flag)) {
                    $notes .= "- " . ($flag['description'] ?? $flag['message'] ?? '') . "\n";
                } else {
                    $notes .= "- " . $flag . "\n";
                }
            }
        }

        return $notes;
    }

    /**
     * Auto-send correction request when analysis detects issues
     *
     * @param DocumentStatusService $statusService
     * @param string $classification
     * @param array $riskFlags
     * @param array $missingFields
     * @return void
     */
    protected function autoRequestCorrection(
        DocumentStatusService $statusService,
        string $classification,
        array $riskFlags,
        array $missingFields,
        array $failedChecks
    ): void {
        $hasRiskFlags = !empty($riskFlags);
        $hasMissingFields = !empty($missingFields);
        $hasFailedChecks = !empty($failedChecks);
        $classificationMismatch = $this->normalizeDocTypeForComparison($classification) !== ''
            && $this->normalizeDocTypeForComparison($classification) !== $this->normalizeDocTypeForComparison((string) $this->document->doc_type);

        if (!$hasRiskFlags && !$hasMissingFields && !$hasFailedChecks && !$classificationMismatch) {
            return;
        }

        if ($this->document->status === 'needs_correction' && $this->document->correction_requested_at) {
            return;
        }

        $feedback = $this->buildCorrectionFeedback(
            $classification,
            $riskFlags,
            $missingFields,
            $failedChecks,
            $classificationMismatch
        );

        $previousStatus = $this->document->status;
        $statusService->markForCorrection($this->document, $feedback);

        // Send correction message to client chat
        $admin = User::where('role', 'admin')->orderBy('id')->first();
        if ($admin) {
            $message = $this->document->messages()->create([
                'user_id' => $admin->id,
                'message' => "**Correction Request:**\n\n" . $feedback,
            ]);

            $this->document->user->notify(new ChatMessageNotification(
                $message,
                $this->document,
                $admin
            ));
        }

        // Dispatch status update event for notifications
        DocumentStatusUpdated::dispatch($this->document->refresh(), $previousStatus, 'needs_correction');
    }

    /**
     * Build correction feedback text from analysis
     *
     * @param string $classification
     * @param array $riskFlags
     * @param array $missingFields
     * @param bool $classificationMismatch
     * @return string
     */
    protected function buildCorrectionFeedback(
        string $classification,
        array $riskFlags,
        array $missingFields,
        array $failedChecks,
        bool $classificationMismatch
    ): string {
        $lines = ['Please update this upload:'];

        if ($classificationMismatch) {
            $lines[] = "- This file looks like " . $this->toPlainLabel($classification)
                . ", but it was uploaded as " . $this->toPlainLabel((string) $this->document->doc_type) . ".";
        }

        foreach ($missingFields as $field) {
            $summary = $this->toPlainFeedback($this->formatFeedbackItem($field));
            if ($summary !== '') {
                $lines[] = "- Please upload " . rtrim($summary, '.') . ".";
            }
        }

        $issueItems = [];
        foreach ([$failedChecks, $riskFlags] as $group) {
            foreach ($group as $item) {
                $formatted = $this->toPlainFeedback($this->formatFeedbackItem($item));
                if ($formatted !== '') {
                    $issueItems[] = $formatted;
                }
            }
        }

        $issueItems = array_values(array_unique($issueItems));
        $issueItems = array_slice($issueItems, 0, 3);

        foreach ($issueItems as $item) {
            $lines[] = "- " . rtrim($item, '.') . ".";
        }

        if (count($lines) === 1) {
            $lines[] = "- Please review the file and upload the correct document.";
        }

        return implode("\n", $lines);
    }

    /**
     * Extract failed validation checks from analysis
     *
     * @param array $validationChecks
     * @return array
     */
    protected function extractFailedValidationChecks(array $validationChecks): array
    {
        $failed = [];

        foreach ($validationChecks as $check) {
            if (is_array($check)) {
                $status = $check['status'] ?? null;
                $passed = $check['passed'] ?? null;
                $message = $check['message'] ?? $check['check'] ?? $check['rule'] ?? $check['description'] ?? null;

                $isFailed = ($status && strtolower((string)$status) !== 'pass') || ($passed === false);
                if ($isFailed) {
                    $failed[] = $message ?? json_encode($check);
                }
            } elseif (is_string($check)) {
                $decoded = $this->tryDecodeJson($check);
                if (is_array($decoded)) {
                    if ($this->isListArray($decoded)) {
                        foreach ($decoded as $item) {
                            $failed[] = $item;
                        }
                    } else {
                        $failed[] = $decoded;
                    }
                } else {
                    $failed[] = $check;
                }
            }
        }

        return $failed;
    }

    /**
     * Format feedback item into readable text
     *
     * @param mixed $item
     * @return string
     */
    protected function formatFeedbackItem(mixed $item): string
    {
        if (is_string($item)) {
            $decoded = $this->tryDecodeJson($item);
            if (is_array($decoded)) {
                return $this->formatFeedbackItem($decoded);
            }

            return trim($item);
        }

        if (!is_array($item)) {
            return trim((string)$item);
        }

        if ($this->isListArray($item)) {
            $parts = [];
            foreach ($item as $listItem) {
                $parts[] = $this->formatFeedbackItem($listItem);
            }
            $parts = array_filter($parts);
            return $parts ? implode('; ', $parts) : '';
        }

        $summary = $item['description']
            ?? $item['message']
            ?? $item['flag']
            ?? $item['check']
            ?? $item['rule']
            ?? $item['name']
            ?? $item['field']
            ?? $item['item']
            ?? $item['doc_type']
            ?? null;

        if ($summary) {
            return trim((string)$summary);
        }

        return '';
    }

    protected function toPlainLabel(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return 'this document';
        }

        return (string) Str::of($trimmed)
            ->replace('_', ' ')
            ->replace('-', ' ')
            ->lower()
            ->trim();
    }

    protected function toPlainFeedback(string $text): string
    {
        $value = trim($text);
        if ($value === '') {
            return '';
        }

        $value = str_replace(['Issue detected:', 'Issues detected:'], '', $value);
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value, " \t\n\r\0\x0B.-");
    }

    protected function normalizeDocTypeForComparison(?string $value): string
    {
        $raw = strtolower(trim((string) $value));
        if ($raw === '') {
            return '';
        }

        if (preg_match('/financial\s*statements?.*year\s*([0-9]+)/i', $raw, $matches) === 1) {
            return 'financial_statements_year_' . $matches[1];
        }

        if (str_contains($raw, 'interim') && str_contains($raw, 'financial')) {
            return 'interim_financial_statements';
        }

        if (str_contains($raw, 'shareholder') && str_contains($raw, 'registry')) {
            return 'shareholder_registry';
        }

        if (str_contains($raw, 'bank') && str_contains($raw, 'statement')) {
            return 'bank_statements';
        }

        return (string) Str::of($raw)
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_');
    }

    /**
     * Try to decode JSON from a string
     *
     * @param string $value
     * @return array|null
     */
    protected function tryDecodeJson(string $value): ?array
    {
        $trimmed = trim($value);
        if ($trimmed === '' || !in_array($trimmed[0], ['{', '['], true)) {
            return null;
        }

        $decoded = json_decode($trimmed, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Determine if array is a list (numeric keys)
     *
     * @param array $value
     * @return bool
     */
    protected function isListArray(array $value): bool
    {
        if ($value === []) {
            return false;
        }

        return array_keys($value) === range(0, count($value) - 1);
    }

    /**
     * Handle job failure
     */
    public function failed(Throwable $exception): void
    {
        $feedback = 'Automated analysis could not be completed for this document. Please review the file quality and re-upload if needed.';

        $this->updateDocument([
            'status' => 'needs_correction',
            'analysis_status' => 'failed',
            'analysis_error' => $exception->getMessage(),
            'correction_feedback' => $feedback,
            'correction_requested_at' => now(),
            'analysis_completed_at' => now(),
        ]);

        \Illuminate\Support\Facades\Log::error('DocumentAnalysisJob permanently failed', [
            'document_id' => $this->document->id,
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Update only columns that exist on documents table.
     */
    protected function updateDocument(array $attributes): void
    {
        $columns = $this->getDocumentColumns();
        $filtered = array_intersect_key($attributes, array_flip($columns));

        if ($filtered !== []) {
            $this->document->update($filtered);
        }
    }

    /**
     * Get and cache documents table columns.
     */
    protected function getDocumentColumns(): array
    {
        if (self::$documentColumns === null) {
            self::$documentColumns = Schema::getColumnListing('documents');
        }

        return self::$documentColumns;
    }
}
