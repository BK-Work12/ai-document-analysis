<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Base mailable class with spam-prevention defaults
 * All marketing/notification emails should extend this
 */
abstract class BaseMailable extends Mailable
{
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            replyTo: config('mail.from.address'),
        );
    }

    /**
     * Add List-Unsubscribe header for GDPR compliance
     * Override in subclasses if needed
     */
    protected function addUnsubscribeHeader($user)
    {
        if ($user && $user->email_unsubscribe_token) {
            return route('email.unsubscribe', $user->email_unsubscribe_token);
        }
        return null;
    }
}
