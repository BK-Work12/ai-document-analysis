<?php

namespace App\Jobs;

use App\Events\DocumentStatusUpdated;
use App\Models\Document;
use App\Services\DocumentStatusService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateDocumentStatusJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Document $document,
    ) {}

    public function handle(DocumentStatusService $statusService): void
    {
        $previousStatus = $this->document->status;
        
        // Check and update document status
        $statusService->checkAndUpdateDocumentStatus($this->document);
        
        // Refresh to get updated status
        $this->document->refresh();
        
        // If status changed, dispatch event
        if ($this->document->status !== $previousStatus) {
            DocumentStatusUpdated::dispatch(
                $this->document,
                $previousStatus,
                $this->document->status
            );
        }
    }
}
