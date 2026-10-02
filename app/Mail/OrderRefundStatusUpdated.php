<?php
namespace App\Mail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content, Envelope};
use Illuminate\Queue\SerializesModels;

class OrderRefundStatusUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public function __construct(public Order $order, public string $refundStatus) {}
    public function envelope(): Envelope { return (new OrderRefunded($this->order,$this->refundStatus))->envelope(); }
    public function content(): Content { return (new OrderRefunded($this->order,$this->refundStatus))->content(); }
}
