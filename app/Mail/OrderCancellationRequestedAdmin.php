<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderCancellation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancellationRequestedAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public OrderCancellation $cancellation
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New Cancellation Request – Order #' . $this->order->order_number);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.orders.cancellation-admin');
    }
}