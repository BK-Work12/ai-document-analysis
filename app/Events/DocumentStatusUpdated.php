<?php

namespace App\Events;

use App\Models\Document;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentStatusUpdated
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public function __construct(
        public Document $document,
        public string $previousStatus,
        public string $newStatus,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->document->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'document.status.updated';
    }
}
