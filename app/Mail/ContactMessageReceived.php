<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $userMessage;

    public function __construct(
        public string $name,
        public string $email,
        string $userMessage,
        public ?string $phone = null,
    ) {
        $this->userMessage = $userMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Contact Message – Albertina Nigeria',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}