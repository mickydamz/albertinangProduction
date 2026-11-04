<?php

namespace App\Http\Livewire;

use App\Models\BankAccount;
use App\Models\PaymentMethod;
use App\Models\Country;
use Livewire\Component;

class BankSelection extends Component
{
    public $paymentMethods;
    public $countries = [];
    public $bankAccounts = [];
    public $selectedPaymentMethod = null;
    public $selectedCountry = null;

    public function mount()
    {
        $this->paymentMethods = PaymentMethod::where('is_active', true)->get();
    }

    public function updatedSelectedPaymentMethod($paymentMethodId)
    {
        // Fetch countries only if the selected payment method is 'Bank'
        $paymentMethod = PaymentMethod::find($paymentMethodId);
        if ($paymentMethod && $paymentMethod->type === 'Bank') {
            $this->countries = Country::all();
        } else {
            $this->countries = [];
            $this->selectedCountry = null;
            $this->bankAccounts = [];
        }
    }

    public function updatedSelectedCountry($countryId)
    {
        // Fetch bank accounts based on selected country
        if ($countryId) {
            $this->bankAccounts = BankAccount::where('country', $countryId)
                                             ->where('is_active', true)
                                             ->get();
        } else {
            $this->bankAccounts = [];
        }
    }

    public function render()
    {
        return view('livewire.bank-selection');
    }
}
