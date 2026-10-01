<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderCancellation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancellationRequestedUser extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public OrderCancellation $cancellation
    ) {}

   public function envelope(): Envelope
{
    return new Envelope(subject: 'Your Order Has Been Cancelled – Order #' . $this->order->order_number);
}

    public function content(): Content
    {
        return new Content(markdown: 'emails.orders.cancellation-user');
    }
}