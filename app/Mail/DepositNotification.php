<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DepositNotification extends Mailable
{
    use Queueable, SerializesModels;

    // Data to be passed to the mailable
    public $user;
    public $paymentMethod;
    public $amount;
    public $country;
    public $cryptoCurrency;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @param  string  $paymentMethod
     * @param  float  $amount
     * @param  string  $country
     * @param  string  $cryptoCurrency
     * @return void
     */
    public function __construct($user, $paymentMethod, $amount, $country = null, $cryptoCurrency = null)
    {
        $this->user = $user;
        $this->paymentMethod = $paymentMethod;
        $this->amount = $amount;
        $this->country = $country;
        $this->cryptoCurrency = $cryptoCurrency;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Deposit Confirmation')
                    ->view('emails.deposit-notification')
                    ->with([
                        'userName' => $this->user->name,
                        'userEmail' => $this->user->email,
                        'userCountry' => $this->user->country,
                        'paymentMethod' => $this->paymentMethod,
                        'amount' => $this->amount,
                        'country' => $this->country,
                        'cryptoCurrency' => $this->cryptoCurrency,
                    ]);
    }
}
