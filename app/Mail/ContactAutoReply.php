<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAutoReply extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $userMessage;

    public function __construct(
        public string $name,
        public string $email,
        string $userMessage,
    ) {
        $this->userMessage = $userMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your message – Albertina Nigeria',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-autoreply',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}