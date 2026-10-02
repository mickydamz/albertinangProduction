<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderRefunded extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public ?string $refundStatus = null) {}

    public function progress(): array
    {
        return $this->refundStatus ? \App\Support\RefundProgress::forStatus($this->refundStatus) : (\App\Support\RefundProgress::forOrder($this->order) ?? \App\Support\RefundProgress::forStatus('unknown'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->progress()['label'] . ' for Order ' . $this->order->order_number . ' – Albertina Nigeria',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-refunded',
            with: [
                'order' => $this->order->load('items', 'user'),
                'refundProgress' => $this->progress(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}