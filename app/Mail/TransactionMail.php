<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionMail extends Mailable
{
    use Queueable, SerializesModels;

    // Data to be passed to the mailable
    public $user;
    public $paymentMethod;
    public $total_amount;
    public $country;
    public $cryptoCurrency;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @param  string  $paymentMethod
     * @param  float  $total_amount
     * @param  string  $country
     * @param  string  $cryptoCurrency
     * @return void
     */
    public function __construct($user, $paymentMethod, $total_amount, $country = null, $cryptoCurrency = null)
    {
        $this->user = $user;
        $this->paymentMethod = $paymentMethod;
        $this->total_amount = $total_amount;
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
        return $this->subject('Transaction Confirmation')
                    ->view('emails.transaction')
                    ->with([
                        'userName' => $this->user->name,
                        'userEmail' => $this->user->email,
                        'userCountry' => $this->user->country,
                        'paymentMethod' => $this->paymentMethod,
                        'total_amount' => $this->total_amount,
                        'country' => $this->country,
                        'cryptoCurrency' => $this->cryptoCurrency,
                    ]);
    }
}
