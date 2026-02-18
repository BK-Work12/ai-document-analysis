<?php

namespace App\Providers;

use App\Events\DocumentStatusUpdated;
use App\Events\DocumentUploaded;
use App\Listeners\ProcessDocumentUpload;
use App\Listeners\SendStatusUpdateNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        DocumentUploaded::class => [
            ProcessDocumentUpload::class,
        ],
        DocumentStatusUpdated::class => [
            SendStatusUpdateNotification::class,
        ],
    ];
}
