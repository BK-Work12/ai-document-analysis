<?php

namespace App\Jobs;

use App\Models\EmailSuppression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HandleSESBounce implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $bounceData
    ) {}

    public function handle(): void
    {
        // Handle permanent bounces
        if (isset($this->bounceData['bounceType']) && $this->bounceData['bounceType'] === 'Permanent') {
            foreach ($this->bounceData['bounceSubType'] === 'Suppressed' ? [] : $this->bounceData['bouncedRecipients'] ?? [] as $recipient) {
                EmailSuppression::suppressBounce(
                    $recipient['emailAddress'],
                    $this->bounceData['bounceSubType'] ?? 'Permanent'
                );
            }
        }

        // Handle transient bounces (optional - usually retry)
        // For now, only suppress permanent bounces
    }
}
