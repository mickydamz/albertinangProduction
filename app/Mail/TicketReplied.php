<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplied extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket      $ticket,
        public TicketReply $reply,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: ' . $this->ticket->subject . ' – AlbertinaNG',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-replied',
            with: [
                'ticket' => $this->ticket->load('user'),
                'reply'  => $this->reply->load('user'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}