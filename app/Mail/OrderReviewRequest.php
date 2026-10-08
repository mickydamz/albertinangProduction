<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderReviewRequest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'How was your order? Leave a review – AlbertinaNG',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-review-request',
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