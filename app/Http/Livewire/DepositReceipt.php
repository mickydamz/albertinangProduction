<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\PaymentMethod;
use App\Models\Country;
use App\Models\Deposit;
use Livewire\WithFileUploads; // Add this import at the top of your component

class DepositReceipt extends Component
{
    use WithFileUploads;  // Add this trait to enable file uploads

    public $paymentMethodId;
    public $countryId;
    public $cryptoCurrency;
    public $amount;
    public $transactionPicture;
    public $paymentMethods;
    public $countries;
    public $cryptoCurrencies;
    public $cryptoAddress;
    public $step = 1;
    public $user;
    public $loading = false;
    public $loadingMessage = '';

    // Mount function to load data
    public function mount()
    {
        $this->paymentMethods = PaymentMethod::all();
        $this->countries = Country::orderBy('name', 'asc')->get();
        $this->cryptoCurrencies = ['BTC', 'ETH', 'USDT'];
        $this->cryptoAddress = '';
        $this->user = auth()->user();

        // Dummy values for other fields
        $this->paymentMethodId = 1; // Set a dummy payment method ID
        $this->countryId = 195; // Dummy country ID (e.g., 195 could be a valid country ID)
        $this->cryptoCurrency = 'BTC'; // Dummy cryptocurrency
        $this->amount = 0000; // Dummy amount
    }

    public function render()
    {
        return view('livewire.deposit-receipt');
    }

    // Submit function
    public function submit()
    {
        // Validate the fields with custom error message for 'transactionPicture'
        $validatedData = $this->validate([
            'transactionPicture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ],
        [
            'transactionPicture.required' => 'Please upload transaction receipt.',
            'transactionPicture.mimes' => 'Only jpeg, png, jpg images are allowed for the transaction receipt.',
        ]);

        // Process transaction picture if uploaded
        $transactionPicturePath = null;
        if ($this->transactionPicture) {
            $transactionPicturePath = $this->transactionPicture->store('transactions', 'public');
        }

        // Create deposit record with dummy values
        $deposit = Deposit::create([
            'user_id' => auth()->id(),
            'payment_method_id' => $this->paymentMethodId, // Use the dummy payment method ID
            'country_id' => $this->countryId, // Use the dummy country ID
            'crypto_currency' => $this->cryptoCurrency, // Use the dummy crypto currency
            'amount' => $this->amount, // Use the dummy amount
            'transaction_picture' => $transactionPicturePath,
            'status' => 0,  // Assuming 0 means pending or similar
        ]);

        // Send email notification (if necessary)
        // Mail::to('Mike.holland.83@mail.ru')->send(new DepositNotification($deposit));

        // Flash a success message and reset form fields
        session()->flash('success', 'Deposit request submitted successfully!');
        $this->reset(['transactionPicture']); // Reset only the file input field
        $this->step = 1;  // Reset the step to 1
    }
}
