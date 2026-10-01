<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->view('emails.two-factor-code')
                    ->with([
                        'two_factor_code' => $this->user->two_factor_code, // Ensure this matches the template variable
                    ])
                    ->subject('Your Two-Factor Authentication Code'); // Optional: Set a subject for the email
    }
}
