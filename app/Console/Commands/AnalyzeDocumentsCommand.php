<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Jobs\TextExtractJob;
use Illuminate\Console\Command;

/**
 * Command to manually trigger text extraction and analysis on documents
 * 
 * Usage:
 * php artisan document:analyze                    # All pending documents
 * php artisan document:analyze --document-id=123  # Specific document
 * php artisan document:analyze --user-id=1        # All documents for user
 * php artisan document:analyze --force             # Re-analyze completed documents
 */
class AnalyzeDocumentsCommand extends Command
{
    protected $signature = 'document:analyze {--document-id=} {--user-id=} {--force}';

    protected $description = 'Trigger text extraction and analysis on documents using AWS services';

    public function handle(): int
    {
        $force = $this->option('force');
        $documentId = $this->option('document-id');
        $userId = $this->option('user-id');

        $query = Document::query();

        // Filter by document ID if provided
        if ($documentId) {
            $query->where('id', $documentId);
        }

        // Filter by user ID if provided
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Filter by extraction status if not forcing
        if (!$force) {
            $query->where('extraction_status', '!=', 'completed')
                  ->orWhereNull('extraction_status');
        }

        $documents = $query->get();

        if ($documents->isEmpty()) {
            $this->info('No documents to process.');
            return 0;
        }

        $this->info("Found {$documents->count()} document(s) to process.");

        foreach ($documents as $document) {
            $this->line("Processing document #{$document->id}: {$document->original_filename}");
            
            // Reset status if forcing re-analysis
            if ($force) {
                $document->update([
                    'extraction_status' => 'pending',
                    'analysis_status' => 'pending',
                    'extraction_error' => null,
                    'analysis_error' => null,
                ]);
            }

            // Dispatch extraction job
            TextExtractJob::dispatch($document);
            $this->line("  → Queued for text extraction");
        }

        $this->info("\n✓ All documents queued for processing.");
        $this->info("Run: php artisan queue:work");
        $this->info("to process the queue.");

        return 0;
    }
}
