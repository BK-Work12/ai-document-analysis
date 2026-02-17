<?php

namespace App\Notifications;

use App\Models\DocumentMessage;
use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChatMessageNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public DocumentMessage $message,
        public Document $document,
        public User $sender,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Only send if user has opted in
        if (!$notifiable->receives_notifications) {
            return [];
        }
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $chatUrl = $notifiable->isAdmin()
            ? route('admin.chats.show', [$this->document->user_id, $this->document->id])
            : route('chats.show', $this->document->id);
        $unsubscribeUrl = route('email.unsubscribe', $notifiable->ensureEmailUnsubscribeToken());

        return (new MailMessage)
            ->subject("New Chat Message from {$this->sender->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have a new message from {$this->sender->name} regarding the document: {$this->document->original_filename}")
            ->line("Message: \"{$this->message->message}\"")
            ->action('View Chat', $chatUrl)
            ->line('Thank you for using our platform!')
            ->withSymfonyMessage(function ($message) use ($unsubscribeUrl) {
                $message->getHeaders()->addTextHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>');
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $chatUrl = $notifiable->isAdmin()
            ? route('admin.chats.show', [$this->document->user_id, $this->document->id])
            : route('chats.show', $this->document->id);

        return [
            'title' => 'New Message from ' . $this->sender->name,
            'message' => $this->message->message,
            'document_id' => $this->document->id,
            'user_id' => $this->document->user_id,
            'sender_name' => $this->sender->name,
            'document_filename' => $this->document->original_filename,
            'url' => $chatUrl,
        ];
    }
}
