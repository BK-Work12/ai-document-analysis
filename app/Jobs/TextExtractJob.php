<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\AI\DocumentAnalysisService;
use App\Services\AI\TextExtractService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Exception;

/**
 * Text Extract Job
 * 
 * Async job to extract text from uploaded documents using AWS TextExtract
 * 
 * Flow:
 * 1. Retrieve document from S3
 * 2. Call TextExtract service
 * 3. Extract text and metadata
 * 4. Store in database
 * 5. Dispatch DocumentAnalysisJob for next step
 */
class TextExtractJob implements ShouldQueue
{
    use Queueable;

    /**
     * Cached list of columns for documents table.
     */
    protected static ?array $documentColumns = null;

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
    public function handle(TextExtractService $textExtractService, DocumentAnalysisService $analysisService): void
    {
        try {
            // Mark as processing
            $this->updateDocument([
                'extraction_status' => 'processing',
                'extraction_started_at' => now(),
            ]);

            // Skip unsupported formats (Textract supports PDF/JPG/PNG/TIFF)
            $mimeType = $this->document->detected_mime;
            $supported = [
                'application/pdf',
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/tiff',
                'image/tif',
            ];

            // Get storage disk and key
            $s3Key = $this->document->s3_key;
            $useLocal = env('USE_LOCAL_STORAGE', true);

            if ($this->isWordDocumentMime($mimeType)) {
                $this->processWordDocumentWithoutTextract($analysisService, $mimeType, $s3Key, $useLocal);
                return;
            }

            if ($mimeType && !in_array(strtolower($mimeType), $supported, true)) {
                if ($this->isClaudeVisionCompatibleMime($mimeType)) {
                    $reason = 'OCR skipped: this image type is compatible with Claude Vision and will be analyzed directly without Textract OCR.';

                    $this->updateDocument([
                        'extraction_status' => 'completed',
                        'extraction_error' => $reason,
                        'extraction_completed_at' => now(),
                        'text_extraction_metadata' => [
                            'skipped' => true,
                            'reason' => $reason,
                            'mime_type' => strtolower($mimeType),
                            'mode' => 'direct_claude_vision',
                        ],
                    ]);

                    DocumentAnalysisJob::dispatch($this->document);
                    return;
                }

                $feedback = $this->buildIncompatibleFormatReason($mimeType, $useLocal ?? env('USE_LOCAL_STORAGE', true));

                if ($this->canDirectExtractWithClaude($mimeType)) {
                    $fallback = $this->tryDirectClaudeTextExtraction($analysisService, $s3Key, $mimeType, $useLocal);

                    if ($fallback['success']) {
                        return;
                    }

                    $feedback .= ' Claude fallback failed: ' . ($fallback['error'] ?? 'unknown error') . '.';
                }

                $this->dispatchFallbackTextAnalysis(
                    $feedback,
                    'unsupported_format',
                    true
                );
                return;
            }

            if ($useLocal) {
                // Local storage: use bytes-based Textract (<= 5MB)
                $fileSize = Storage::disk('local')->size($s3Key);

                if ($fileSize >= 5 * 1024 * 1024) {
                    throw new Exception('Local storage file too large for Textract bytes. Configure S3 or reduce file size.');
                }

                $bytes = Storage::disk('local')->get($s3Key);
                $result = $textExtractService->detectDocumentTextBytes($bytes);
            } else {
                // S3 storage: use S3-based Textract
                $s3Bucket = config('filesystems.disks.s3.bucket');

                // Determine file size to decide sync vs async
                $fileSize = Storage::disk('s3')->size($s3Key);

                // For files smaller than 5MB, use synchronous extraction
                // For larger files, use async (would need SNS for notifications)
                if ($fileSize < 5 * 1024 * 1024) {
                    $result = $textExtractService->detectDocumentText($s3Bucket, $s3Key);
                } else {
                    $result = $textExtractService->startDocumentAnalysis(
                        $s3Bucket,
                        $s3Key,
                        env('AWS_SNS_TOPIC_ARN'),
                        env('AWS_ROLE_ARN')
                    );

                    // For async, we'll handle completion via SNS notification
                    if ($result['success']) {
                        $this->updateDocument([
                            'extraction_status' => 'processing',
                            'text_extraction_metadata' => [
                                'job_id' => $result['job_id'],
                                'async' => true,
                            ],
                        ]);

                        // Job will be completed when SNS notification arrives
                        return;
                    }
                }
            }

            if (!$result['success']) {
                $error = $result['error'] ?? 'Unknown error during text extraction';

                if (str_contains(strtolower($error), 'unsupported document format')) {
                    $feedback = $this->buildIncompatibleFormatReason($this->document->detected_mime, $useLocal ?? env('USE_LOCAL_STORAGE', true));

                    if ($this->canDirectExtractWithClaude($this->document->detected_mime)) {
                        $fallback = $this->tryDirectClaudeTextExtraction($analysisService, $s3Key, $this->document->detected_mime, $useLocal);

                        if ($fallback['success']) {
                            return;
                        }

                        $feedback .= ' Claude fallback failed: ' . ($fallback['error'] ?? 'unknown error') . '.';
                    }

                    $this->dispatchFallbackTextAnalysis(
                        $feedback,
                        'textract_unsupported',
                        true
                    );
                    return;
                }

                if ($this->canFallbackToDirectAnalysis($this->document->detected_mime)) {
                    $this->updateDocument([
                        'extraction_status' => 'failed',
                        'extraction_error' => $error,
                        'extraction_completed_at' => now(),
                        'text_extraction_metadata' => [
                            'fallback_to_direct_analysis' => true,
                            'reason' => $error,
                        ],
                    ]);

                    DocumentAnalysisJob::dispatch($this->document);
                    return;
                }

                throw new Exception($error);
            }

            // Store extracted text and metadata
            $this->updateDocument([
                'extracted_text' => $result['extracted_text'],
                'extraction_status' => 'completed',
                'extraction_completed_at' => now(),
                'text_extraction_metadata' => $result['metadata'] ?? [],
            ]);

            // Dispatch next job for Bedrock analysis
            DocumentAnalysisJob::dispatch($this->document);

        } catch (Exception $e) {
            if ($this->canFallbackToDirectAnalysis($this->document->detected_mime)) {
                $this->updateDocument([
                    'extraction_status' => 'failed',
                    'extraction_error' => $e->getMessage(),
                    'extraction_completed_at' => now(),
                    'text_extraction_metadata' => [
                        'fallback_to_direct_analysis' => true,
                        'reason' => $e->getMessage(),
                    ],
                ]);

                DocumentAnalysisJob::dispatch($this->document);
                return;
            }

            $this->dispatchFallbackTextAnalysis(
                $e->getMessage(),
                'textract_exception',
                false
            );
            return;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(Exception $exception): void
    {
        $feedback = 'We could not process this document automatically. Please re-upload a clearer or supported file and try again.';

        $this->updateDocument([
            'status' => 'needs_correction',
            'extraction_status' => 'failed',
            'extraction_error' => $exception->getMessage(),
            'correction_feedback' => $feedback,
            'correction_requested_at' => now(),
            'extraction_completed_at' => now(),
        ]);

        \Illuminate\Support\Facades\Log::error('TextExtractJob permanently failed', [
            'document_id' => $this->document->id,
            'error' => $exception->getMessage(),
        ]);
    }

    protected function canFallbackToDirectAnalysis(?string $mimeType): bool
    {
        if (!is_string($mimeType) || trim($mimeType) === '') {
            return false;
        }

        return in_array(strtolower($mimeType), [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp',
            'image/gif',
        ], true);
    }

    protected function canDirectExtractWithClaude(?string $mimeType): bool
    {
        if (!is_string($mimeType) || trim($mimeType) === '') {
            return false;
        }

        return in_array(strtolower($mimeType), [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'text/markdown',
            'text/html',
            'text/csv',
            'application/csv',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ], true);
    }

    protected function tryDirectClaudeTextExtraction(
        DocumentAnalysisService $analysisService,
        string $s3Key,
        ?string $mimeType,
        bool $useLocal
    ): array {
        try {
            $disk = $useLocal ? 'local' : 's3';

            if (!Storage::disk($disk)->exists($s3Key)) {
                return ['success' => false, 'error' => 'Document file not found in storage for Claude fallback.'];
            }

            $bytes = Storage::disk($disk)->get($s3Key);

            $result = $analysisService->extractTextFromDocument(
                $bytes,
                (string) $mimeType,
                (string) ($this->document->original_filename ?? 'uploaded-document.pdf')
            );

            if (!$result['success']) {
                return ['success' => false, 'error' => $result['error'] ?? 'Claude document extraction failed.'];
            }

            $text = trim((string) ($result['text'] ?? ''));
            if ($text === '') {
                return ['success' => false, 'error' => 'Claude document extraction returned empty text.'];
            }

            $this->updateDocument([
                'extracted_text' => $text,
                'extraction_status' => 'completed',
                'extraction_error' => 'Textract skipped: extracted via Claude direct document OCR fallback.',
                'extraction_completed_at' => now(),
                'text_extraction_metadata' => [
                    'skipped' => true,
                    'reason' => 'Textract unsupported; extracted via Claude direct document input.',
                    'mime_type' => strtolower((string) $mimeType),
                    'mode' => 'direct_claude_document_ocr',
                    'claude' => $result['metadata'] ?? [],
                ],
            ]);

            DocumentAnalysisJob::dispatch($this->document);

            return ['success' => true];
        } catch (Exception $exception) {
            return ['success' => false, 'error' => $exception->getMessage()];
        }
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

    /**
     * Textract-incompatible but Claude-vision-compatible image formats.
     */
    protected function isClaudeVisionCompatibleMime(?string $mimeType): bool
    {
        if (!is_string($mimeType) || trim($mimeType) === '') {
            return false;
        }

        return in_array(strtolower($mimeType), [
            'image/webp',
            'image/gif',
        ], true);
    }

    /**
     * Human-readable extraction failure reason for incompatible files.
     */
    protected function buildIncompatibleFormatReason(?string $mimeType, ?bool $isLocalMode = null): string
    {
        $detected = is_string($mimeType) && trim($mimeType) !== ''
            ? strtolower($mimeType)
            : 'unknown';

        $isLocal = $isLocalMode ?? env('USE_LOCAL_STORAGE', true);

        if ($isLocal && in_array($detected, ['application/pdf', 'image/tiff', 'image/tif'], true)) {
            return "Textract could not process this {$detected} file in local-storage mode. Current OCR path uses byte-based processing, which is limited for this format. Enable S3 processing (set USE_LOCAL_STORAGE=false with AWS S3 configured) or re-upload as JPG/PNG.";
        }

        return "Textract cannot process this file format ({$detected}). Supported OCR formats are PDF, JPG, PNG, and TIFF. Please re-upload in a supported format.";
    }

    protected function isWordDocumentMime(?string $mimeType): bool
    {
        if (!is_string($mimeType) || trim($mimeType) === '') {
            return false;
        }

        return in_array(strtolower($mimeType), [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword',
        ], true);
    }

    protected function processWordDocumentWithoutTextract(
        DocumentAnalysisService $analysisService,
        ?string $mimeType,
        string $s3Key,
        bool $useLocal
    ): void
    {
        $normalizedMime = is_string($mimeType) ? strtolower($mimeType) : '';

        if ($normalizedMime === 'application/msword') {
            $fallback = $this->tryDirectClaudeTextExtraction($analysisService, $s3Key, $mimeType, $useLocal);
            if ($fallback['success']) {
                return;
            }

            $feedback = 'Legacy .doc extraction failed. Claude direct-document fallback failed: ' . ($fallback['error'] ?? 'unknown error');

            $this->dispatchFallbackTextAnalysis($feedback, 'doc_fallback_failed', true);

            return;
        }

        $disk = $useLocal ? 'local' : 's3';
        $bytes = Storage::disk($disk)->get($s3Key);
        $text = $this->extractTextFromDocxBytes($bytes);

        if (trim($text) === '') {
            $fallback = $this->tryDirectClaudeTextExtraction($analysisService, $s3Key, $mimeType, $useLocal);
            if ($fallback['success']) {
                return;
            }

            $feedback = 'DOCX local extraction failed and Claude fallback could not extract text: ' . ($fallback['error'] ?? 'unknown error');

            $this->dispatchFallbackTextAnalysis($feedback, 'docx_fallback_failed', true);

            return;
        }

        $this->updateDocument([
            'extraction_status' => 'completed',
            'extraction_error' => 'Textract skipped: DOCX processed via direct text extraction for AI analysis.',
            'extracted_text' => $text,
            'extraction_completed_at' => now(),
            'text_extraction_metadata' => [
                'skipped' => true,
                'reason' => 'DOCX file processed without Textract.',
                'mime_type' => $normalizedMime,
                'mode' => 'direct_docx_text',
            ],
        ]);

        DocumentAnalysisJob::dispatch($this->document);
    }

    protected function extractTextFromDocxBytes(string $bytes): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'docx_');

        if ($tempFile === false) {
            throw new Exception('Unable to create temporary file for DOCX extraction.');
        }

        try {
            file_put_contents($tempFile, $bytes);

            $zip = new \ZipArchive();
            if ($zip->open($tempFile) !== true) {
                throw new Exception('Unable to open DOCX archive.');
            }

            $xml = $zip->getFromName('word/document.xml');
            $zip->close();

            if (!is_string($xml) || trim($xml) === '') {
                throw new Exception('DOCX does not contain readable document text.');
            }

            $document = simplexml_load_string($xml);
            if ($document === false) {
                throw new Exception('Unable to parse DOCX XML content.');
            }

            $document->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $nodes = $document->xpath('//w:t');

            if (!is_array($nodes) || $nodes === []) {
                return '';
            }

            $parts = [];
            foreach ($nodes as $node) {
                $value = trim((string) $node);
                if ($value !== '') {
                    $parts[] = $value;
                }
            }

            return implode("\n", $parts);
        } finally {
            @unlink($tempFile);
        }
    }

    protected function dispatchFallbackTextAnalysis(string $reason, string $mode, bool $markCorrection): void
    {
        $context = $this->buildFallbackAnalysisText($reason);

        $attributes = [
            'status' => $markCorrection ? 'needs_correction' : 'pending',
            'extraction_status' => 'failed',
            'extraction_error' => $reason,
            'extracted_text' => $context,
            'extraction_completed_at' => now(),
            'text_extraction_metadata' => [
                'skipped' => true,
                'mode' => $mode,
                'reason' => $reason,
                'fallback_text_analysis' => true,
            ],
        ];

        if ($markCorrection) {
            $attributes['correction_feedback'] = $reason;
            $attributes['correction_requested_at'] = now();
        }

        $this->updateDocument($attributes);

        DocumentAnalysisJob::dispatch($this->document);
    }

    protected function buildFallbackAnalysisText(string $reason): string
    {
        $filename = (string)($this->document->original_filename ?? 'unknown');
        $docType = (string)($this->document->doc_type ?? 'unknown');
        $mime = (string)($this->document->detected_mime ?? 'unknown');

        return implode("\n", [
            'OCR extraction was unsuccessful, but analysis should still proceed using available metadata.',
            "Filename: {$filename}",
            "Uploaded As: {$docType}",
            "Detected MIME: {$mime}",
            "Extraction Failure: {$reason}",
            'Please classify document type, identify likely missing information, and provide conservative risk flags due to unavailable OCR text.',
        ]);
    }
}
