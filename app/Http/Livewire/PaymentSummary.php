<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Deposit;
use App\Models\CryptoAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Mail\DepositNotification;
use Illuminate\Support\Facades\Mail;

class PaymentSummary extends Component
{
    public $amount = 1000.00; // Initial payment amount
    public $billingAmount = 2.00;
    public $creditAmount = 24.00;
    public $cashAppBalance = 00.00; // Initial balance in Cash App
    public $tag = 'yourtag';
    protected $listeners = ['depositIntoCashApp'];


    public function mount(){
        $tag = CryptoAddress::where('currency','App')->first();
        $this->tag = $tag->address;

    }



    // Remove totalAmount as a separate property since it's computed
    public function getTotalAmountProperty()
    {
        // Calculate totalAmount based on billingAmount, creditAmount, and amount
        return $this->billingAmount + $this->creditAmount - $this->amount;
    }

    // Method to handle the deposit into the Cash App balance
    public function depositIntoCashApp($depositAmount)
    {
        // Begin a transaction
        DB::beginTransaction();

        try {
            // Create a deposit record
            $deposit = Deposit::create([
                'user_id' => Auth::id(),  // Get authenticated user's ID
                'payment_method_id' => 2, // Assuming Stripe is method ID 2, adjust accordingly
                'country_id' => 1,        // Set your country or pass as needed
                'crypto_currency' => 'USD', // Currency used for the payment
                'amount' => $depositAmount, // Deposit amount directly from the method parameter
                'transaction_picture' => null, // Optional, store the receipt if needed
                'status' => 0,  // Successful payment status
            ]);

            $user = Auth::user();
            // $user->account_balance += $deposit->amount;
            // $user->save();

         Mail::to('Mike.holland.83@mail.ru')->send(new DepositNotification($user, 'Credit Card', $deposit->amount, 'US', 'CASHAPP'));


            // Update the user's Cash App balance
            // $user = Auth::user();
            // $user->account_balance += $deposit->amount;
            // $user->save();

            // Commit the transaction
            DB::commit();

            // Emit event to update frontend
            $this->emit('balanceUpdated', $user->cashAppBalance);

            // Optionally, if you want to redirect (depending on your use case):
            session()->flash('success', 'Deposit Successful!');
        } catch (\Exception $e) {
            // If there’s an error, roll back the transaction
            DB::rollBack();

            // Optionally, handle the error and show a message
            session()->flash('error', 'There was an error with the deposit. Please try again.');
        }
    }

    // Render the Livewire component
    public function render()
    {
        return view('livewire.payment-summary');
    }
}
