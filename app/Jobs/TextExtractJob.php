<?php

namespace App\Jobs;

use App\Models\Document;
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
    public function handle(TextExtractService $textExtractService): void
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

                $this->updateDocument([
                    'extraction_status' => 'failed',
                    'extraction_error' => $this->buildIncompatibleFormatReason($mimeType),
                    'extraction_completed_at' => now(),
                ]);

                return;
            }

            // Get storage disk and key
            $s3Key = $this->document->s3_key;
            $useLocal = env('USE_LOCAL_STORAGE', true);

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
                    $this->updateDocument([
                        'extraction_status' => 'failed',
                        'extraction_error' => $this->buildIncompatibleFormatReason($this->document->detected_mime),
                        'extraction_completed_at' => now(),
                    ]);

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
            $this->updateDocument([
                'extraction_status' => 'failed',
                'extraction_error' => $e->getMessage(),
                'extraction_completed_at' => now(),
            ]);

            // Log the error
            \Illuminate\Support\Facades\Log::error('TextExtractJob failed', [
                'document_id' => $this->document->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(Exception $exception): void
    {
        $this->updateDocument([
            'extraction_status' => 'failed',
            'extraction_error' => $exception->getMessage(),
            'extraction_completed_at' => now(),
        ]);

        \Illuminate\Support\Facades\Log::error('TextExtractJob permanently failed', [
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
    protected function buildIncompatibleFormatReason(?string $mimeType): string
    {
        $detected = is_string($mimeType) && trim($mimeType) !== ''
            ? strtolower($mimeType)
            : 'unknown';

        return "Textract cannot process this file format ({$detected}). Supported OCR formats are PDF, JPG, PNG, and TIFF. Please re-upload in a supported format.";
    }
}
