<?php

namespace App\Listeners;

use App\Events\DocumentUploaded;
use App\Jobs\TextExtractJob;
use App\Jobs\UpdateDocumentStatusJob;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Process Document Upload Listener
 * 
 * Triggered when a document is uploaded.
 * Initiates the text extraction and analysis pipeline.
 * 
 * Pipeline:
 * 1. TextExtractJob - Extract text via AWS TextExtract
 * 2. DocumentAnalysisJob - Analyze via AWS Bedrock (dispatched from TextExtractJob)
 * 3. UpdateDocumentStatusJob - Update overall document status
 */
class ProcessDocumentUpload implements ShouldQueue
{
    public function handle(DocumentUploaded $event): void
    {
        // Dispatch text extraction job (will trigger analysis job upon completion)
        TextExtractJob::dispatch($event->document);

        // Also dispatch status update job in parallel
        UpdateDocumentStatusJob::dispatch($event->document);
    }
}

