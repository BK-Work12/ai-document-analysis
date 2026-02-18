<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentCorrectionNeededMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $docType,
        public string $feedback,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Document Correction Required',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.correction-needed',
        );
    }
}
