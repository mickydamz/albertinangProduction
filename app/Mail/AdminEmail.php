<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;   // Dynamic subject
    public $messager; // The message of the notification

    /**
     * Create a new message instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->title = $data['title'];  // Dynamically set the title
        $this->messager = $data['messager'];  // Dynamically set the message
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->title)  // Set the subject dynamically
                    ->view('emails.admin-email')  // View that renders the email body
                    ->with([  
                        'title' => $this->title,  // Pass the title to the view
                        'messager' => $this->messager,  // Pass the message to the view
                    ]);
    }
}
