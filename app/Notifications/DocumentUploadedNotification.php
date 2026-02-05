<?php

namespace App\Notifications;

use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentUploadedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Document $document,
        public User $sender,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $docType = ucwords(str_replace('_', ' ', $this->document->doc_type));

        return (new MailMessage)
            ->subject("New document upload from {$this->sender->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->sender->name} uploaded a document: {$this->document->original_filename}")
            ->line("Document type: {$docType}")
            ->action('View Client', route('admin.clients.show', $this->document->user_id))
            ->line('Thank you for using our platform!');
    }

    public function toDatabase(object $notifiable): array
    {
        $docType = ucwords(str_replace('_', ' ', $this->document->doc_type));

        return [
            'title' => 'New document uploaded',
            'message' => "{$this->sender->name} uploaded {$this->document->original_filename} ({$docType}).",
            'document_id' => $this->document->id,
            'user_id' => $this->document->user_id,
            'sender_name' => $this->sender->name,
            'document_filename' => $this->document->original_filename,
            'url' => route('admin.clients.show', $this->document->user_id),
        ];
    }
}
