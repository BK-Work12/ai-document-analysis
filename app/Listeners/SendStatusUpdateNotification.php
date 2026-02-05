<?php

namespace App\Listeners;

use App\Events\DocumentStatusUpdated;
use App\Jobs\SendEmailNotificationJob;

class SendStatusUpdateNotification
{
    public function handle(DocumentStatusUpdated $event): void
    {
        // Dispatch email job asynchronously
        SendEmailNotificationJob::dispatch($event);
    }
}
