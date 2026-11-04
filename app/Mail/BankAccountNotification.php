<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BankAccountNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $title;   // Dynamic subject
    public $details; // The details of the notification
    public $warning;   //

    /**
     * Create a new message instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        // Assign values to the properties
        $this->title = $data['title'];  // Dynamically set the title
        $this->details = $data['details'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->title) // Use the dynamic title as the subject
                    ->view('emails.bank_account_notification')
                    ->with([
                        'details' => $this->details,
                    ]);
    }
}
