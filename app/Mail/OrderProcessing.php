<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderProcessing extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Order ' . $this->order->order_number . ' is Being Processed – Albertina Nigeria',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-processing',
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