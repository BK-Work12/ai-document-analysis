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
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

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
            // Verify we have extracted text
            if (!$this->document->extracted_text) {
                throw new Exception('Document has no extracted text. Run TextExtractJob first.');
            }

            // Mark as processing
            $this->document->update([
                'analysis_status' => 'processing',
                'analysis_started_at' => now(),
            ]);

            // Analyze the extracted text
            $result = $analysisService->analyzeDocument(
                $this->document->extracted_text,
                $this->document->original_filename,
                $this->document->doc_type
            );

            if (!$result['success']) {
                throw new Exception($result['error'] ?? 'Unknown error during analysis');
            }

            // Extract key information from analysis
            $analysisResult = $result['analysis_result'];
            $riskFlags = $analysisResult['risk_flags'] ?? [];
            $missingFields = $analysisResult['missing_fields'] ?? [];
            $validationChecks = $analysisResult['validation_checks'] ?? [];
            $failedChecks = $this->extractFailedValidationChecks($validationChecks);
            $classification = $analysisResult['classification'] ?? $this->document->doc_type;
            $confidenceScore = $result['confidence_score'] ?? 0;

            // Store analysis results
            $this->document->update([
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

            // Auto-request correction if issues detected
            $this->autoRequestCorrection($statusService, $classification, $riskFlags, $missingFields, $failedChecks);

            \Illuminate\Support\Facades\Log::info('DocumentAnalysisJob completed', [
                'document_id' => $this->document->id,
                'confidence_score' => $confidenceScore,
                'risk_flags_count' => count($riskFlags),
            ]);

        } catch (Exception $e) {
            $this->document->update([
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
                $severity = $flag['severity'] ?? 'medium';
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
        if ($confidenceScore >= 85 && empty($riskFlags)) {
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
        $classificationMismatch = $classification && $classification !== $this->document->doc_type;

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
        $lines = [];

        if ($classificationMismatch) {
            $lines[] = "Document uploaded as '{$this->document->doc_type}' but is actually a {$classification}.";
        }

        if (!empty($missingFields)) {
            foreach ($missingFields as $field) {
                $summary = $this->formatFeedbackItem($field);
                if ($summary !== '') {
                    $lines[] = "{$summary} are required but not provided in correct upload slot";
                }
            }
        }

        $issueItems = [];
        foreach ($failedChecks as $check) {
            $formatted = $this->formatFeedbackItem($check);
            if ($formatted !== '') {
                $issueItems[] = $formatted;
            }
        }
        foreach ($riskFlags as $flag) {
            $formatted = $this->formatFeedbackItem($flag);
            if ($formatted !== '') {
                $issueItems[] = $formatted;
            }
        }
        if (!empty($issueItems)) {
            $lines[] = "Issues detected:";
            foreach ($issueItems as $item) {
                $lines[] = "- " . $item;
            }
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
        $this->document->update([
            'analysis_status' => 'failed',
            'analysis_error' => $exception->getMessage(),
            'analysis_completed_at' => now(),
        ]);

        \Illuminate\Support\Facades\Log::error('DocumentAnalysisJob permanently failed', [
            'document_id' => $this->document->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
