<?php
namespace App\Mail;
use App\Models\OrderReturn;
use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
class ReturnStageUpdated extends Mailable
{
    use Queueable, SerializesModels;
    public function __construct(public OrderReturn $return, public string $label, public ?string $stageMessage) {}
    public function build() { return $this->subject($this->label.' — Order '.$this->return->order->order_number)->view('emails.return-stage')->with(['order' => $this->return->order, 'statusLabel' => $this->label]); }
}
