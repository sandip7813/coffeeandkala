<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public bool $activated,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->activated
                ? 'Your Coffee & Kala account has been reactivated'
                : 'Your Coffee & Kala account has been deactivated',
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.account-status',
            text: 'emails.account-status-text',
        );
    }
}
