<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancellationRejected extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on Your Cancellation Request for Order ' . $this->order->order_number . ' – Albertina Nigeria',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-cancellation-rejected',
            with: [
                'order' => $this->order->load('items', 'user'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
