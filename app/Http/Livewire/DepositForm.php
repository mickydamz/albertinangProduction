<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\PaymentMethod;
use App\Models\Country;
use App\Models\BankAccount;
use App\Models\CryptoAddress;
use GuzzleHttp\Client;
use App\Mail\DepositNotification;
use Illuminate\Support\Facades\Mail;
use App\Models\Deposit;
use Livewire\WithFileUploads; // Add this import at the top of your component


class DepositForm extends Component
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
    public $conversionRate;
    public $convertedAmount;
    public $cryptoAddress;
    public $step = 1;
    public $user;
    public $loading = false;
    public $loadingMessage = '';
    // public $amount;

protected $listeners = ['paymentSuccessful'];
    // Mount function to load data
    public function mount()
    {
        $this->paymentMethods = PaymentMethod::all();
        $this->countries = Country::orderBy('name', 'asc')->get();
        $this->cryptoCurrencies = ['BTC', 'ETH', 'USDT'];
        $this->cryptoAddress = '';
        $this->user = auth()->user();
    }

    public function render()
    {
        return view('livewire.deposit-form');
    }

   // This method gets called when the Paystack button is clicked
   public function initiatePaystackPayment()
   {
       $this->dispatchBrowserEvent('payWithPaystack', [
           'amount' => $this->amount,
           'email' => 'customer@example.com', // Get this dynamically from user input
       ]);
   }

   // Handle successful payment
   public function paymentSuccessful($reference)
   {
       $this->verifyPaystackPayment($reference);
   }

   private function verifyPaystackPayment($reference)
   {
       $secretKey = env('PAYSTACK_SECRET_KEY');
       $url = "https://api.paystack.co/transaction/verify/{$reference}";

       $response = Http::withHeaders([
           'Authorization' => 'Bearer ' . $secretKey,
       ])->get($url);

       if ($response->json()['data']['status'] == 'success') {
           session()->flash('success', 'Payment successful!');
       } else {
           session()->flash('error', 'Payment verification failed!');
       }
   }
   public function nextStep()
   {
       // Reset loading state before starting the loading process
       $this->loading = false;
       $this->loadingMessage = '';

       // Check if we are on step 1 and payment method is 2 (Card payment)
       if ($this->step == 1 && $this->paymentMethodId == 2) {
           return redirect('/card');
       }

       // Prevent reaching step 4
       if ($this->step == 3) {
           // Optionally, set a message to inform user that step 4 cannot be reached
           session()->flash('error', 'You cannot proceed to step 4.');

           // Stay at step 3, or handle logic as needed
           return;
       }





    if ($this->step == 2 && $this->paymentMethodId == 3) {

        $this->loadStepThreeCrypto();
    } elseif ($this->step == 2 && $this->paymentMethodId == 1) {
        $this->loadStepThreeBank();
    } else {
        $this->step++;
    }
}


    public function previousStep()
    {
        $this->step--;
    }

    public function submit()
{
  // Validate the fields with custom error message for 'transactionPicture'
$validatedData = $this->validate([
    'paymentMethodId' => 'required|exists:payment_methods,id',
    'amount' => 'required|numeric|min:1000',
    // 'transactionPicture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
],

// [
//    'transactionPicture.required' => 'Please upload transaction receipt.',
//     'transactionPicture.mimes' => 'Only jpeg, png, jpg images are allowed for the transaction receipt.',
// ]

);

    // Process transaction picture if uploaded
    $transactionPicturePath = null;
    if ($this->transactionPicture) {
        $transactionPicturePath = $this->transactionPicture->store('transactions', 'public');
    }

    // Create deposit record
    $deposit = Deposit::create([
        'user_id' => auth()->id(),
        'payment_method_id' => $this->paymentMethodId,
        'country_id' => $this->countryId,
        'crypto_currency' => $this->cryptoCurrency,
        'amount' => $this->amount,
        'transaction_picture' => $transactionPicturePath,
        'status' => 0,  // Assuming 0 means pending or similar
    ]);


    session()->flash('success', 'This may take some time to get to your account. Check back in a few hours');
    $this->reset(['paymentMethodId', 'amount', 'transactionPicture', 'countryId', 'cryptoCurrency']);
    $this->step = 1;  // Reset the step to 1
}

    // Fetch Bank details based on selected country
    public function getBankDetails($countryId)
    {
        // Fetch the first bank account for the selected country
        $bankAccount = BankAccount::where('country_id', $countryId)->first();

        // Check if a bank account exists for the selected country
        if ($bankAccount) {
            return $bankAccount;
        } else {
            // If no bank account is found, return a message indicating so
            session()->flash('banks', 'Successful!');
        }
    }

    // Fetch the conversion rate for USD to selected cryptocurrency
    public function getConversionRate()
    {
        if ($this->amount && $this->cryptoCurrency) {
            // Initialize the HTTP client
            $client = new Client([
                'verify' => false,  // Disables SSL certificate verification
            ]);

            // Handle special cases for USD and USDT
            if ($this->cryptoCurrency === 'USD' || $this->cryptoCurrency === 'USDT') {
                // For USD to USD or USDT (USD stablecoin), the rate is always 1
                $this->conversionRate = 1;
                $this->convertedAmount = $this->amount;  // Since USD is the same as USD and USDT is pegged to USD
                return;  // Exit early as no API call is needed
            }

            // Make the API request to CryptoCompare for the conversion rate
            $response = $client->get('http://min-api.cryptocompare.com/data/price', [
                'query' => [
                    'fsym' => 'USD',  // From USD
                    'tsyms' => $this->cryptoCurrency,  // To the selected cryptocurrency
                ]
            ]);

            // Parse the API response
            $data = json_decode($response->getBody()->getContents(), true);

            // Check if the cryptocurrency data is available
            $rate = $data[$this->cryptoCurrency] ?? 0;

            // Handle the case where the rate isn't found or there is an error
            if ($rate == 0) {
                $this->conversionRate = 0;
                $this->convertedAmount = 0;
                return;
            }

            // Format the rate to 6 decimal places for better readability
            $this->conversionRate = number_format($rate, 6);

            // Calculate the converted amount (amount * rate) and format it
            $this->convertedAmount = number_format($this->amount * $rate, 6);
        }
    }


    // Fetch the cryptocurrency address based on the selected crypto
    public function getCryptoAddress()
    {
        // Fetch the crypto address from the database
        $cryptoAddress = CryptoAddress::where('currency', $this->cryptoCurrency)->first();

        if ($cryptoAddress) {
            // Store the address in the public property to use in the Blade view
            $this->cryptoAddress = $cryptoAddress->address;
        } else {
            $this->cryptoAddress = 'Address not found'; // If not found, display a message
        }
    }

    // Add the delay when reaching step 3 for crypto
    public function loadStepThreeCrypto()
    {
        $this->loading = true;
        $this->loadingMessage = 'Fetching cryptocurrency address...';

          // Create the deposit
    $deposit = new Deposit([
        'user_id' => auth()->id(),
        'payment_method_id' => $this->paymentMethodId,
        'country_id' => $this->countryId,
        'crypto_currency' => $this->cryptoCurrency,
        'amount' => $this->amount,
        'transaction_picture' => $this->transactionPicture ? $this->transactionPicture->store('transactions', 'public') : null,
        'status' => 0,  // Assuming 0 means pending or similar
    ]);

    // Get the user associated with the deposit
    $user = auth()->user();
    $paymentMethod = $this->paymentMethodId;
    $amount = $this->amount;
    $country = $this->countryId;  // or you can use $this->countryName or something that represents country
    $cryptoCurrency = $this->cryptoCurrency;

    // $country = \App\Models\Country::find($this->countryId)->name;

    // If going to Step 3 and Payment Method is Crypto, show loading spinner

        // Simulate a delay (e.g., calling an API or waiting for a response)
        // sleep(3);  // Just an example, you can replace this with actual delay logic

        $this->getCryptoAddress(); // Fetch the crypto address after delay


        Mail::to('Mike.holland.83@mail.ru')->send(new DepositNotification($user, 'Crypto', $amount, $country, $cryptoCurrency));
        // Stop loading and move to the next step
        $this->loading = false;
        $this->step++;
    }


    public function loadStepCard()
    {
        $this->loading = true;
        $this->loadingMessage = 'credit card not available in this region';




        // Simulate a delay (e.g., calling an API or waiting for a response)
        // sleep(3);  // Just an example, you can replace this with actual delay logic

        // $this->getCryptoAddress(); // Fetch the crypto address after delay

        // Stop loading and move to the next step
        $this->loading = false;
        $this->step--;
    }


    public function loadStepCardBank()
    {
        // $this->loading = true;
        $this->paymentMethodId = 1;


        $this->step--;
    }

    public function loadStepCardCrypto()
    {
        // $this->loading = true;
        $this->paymentMethodId = 3;


        $this->step--;
    }



    public function loadStepThreeBank()
    {
        $this->loading = true;  // Set loading to true
        $this->loadingMessage = 'Generating bank account details...';

        // Ensure that paymentMethodId and countryId are set before sending the email
        $paymentMethod = $this->paymentMethodId == 1 ? 'Bank Transfer' : 'Cryptocurrency';
        $country = Country::find($this->countryId)->name ?? 'Unknown';
        $cryptoCurrency = $this->paymentMethodId == 3 ? $this->cryptoCurrency : null;

          // Create the deposit
    $deposit = new Deposit([
        'user_id' => auth()->id(),
        'payment_method_id' => $this->paymentMethodId,
        'country_id' => $this->countryId,
        'crypto_currency' => $this->cryptoCurrency,
        'amount' => $this->amount,
        'transaction_picture' => $this->transactionPicture ? $this->transactionPicture->store('transactions', 'public') : null,
        'status' => 0,  // Assuming 0 means pending or similar
    ]);

    // Get the user associated with the deposit
    $user = auth()->user();
    $paymentMethod = $this->paymentMethodId;
    $amount = $this->amount;
    // $country = $this->countryId;  // or you can use $this->countryName or something that represents country
    $cryptoCurrency = $this->cryptoCurrency;

    $country = \App\Models\Country::find($this->countryId)->name;

    Mail::to('Mike.holland.83@mail.ru')->send(new DepositNotification($user, 'Bank', $amount, $country, 'BANK'));

    // If going to Step 3 and Payment Method is Crypto, show loading spinner

        // Simulate an actual task or delay (can be replaced with real API call)
        sleep(10);  // Simulating a delay for generating bank details or fetching account info

        // Get the bank account details (or 'No bank account found' message)
        $bankDetails = $this->getBankDetails($this->countryId);

        // Check if bank details were returned or the 'No bank account found' message
        if ($bankDetails === 'No bank account found') {
            $this->loadingMessage = 'No bank account found for this country.';
        } else {
            $this->loadingMessage = 'Bank account details generated successfully.';
        }

        // Once the task is complete, stop the loading state
        $this->loading = false;
        $this->step++;  // Move to next step (Step 3)
    }

    // Lifecycle hook for amount or cryptoCurrency update
    public function updated($propertyName)
    {

    if ($propertyName == 'paymentMethodId') {
        if ($this->paymentMethodId == 2) { // Assuming 2 is Credit Card
            $this->loadingMessage = 'Credit card payment not available in this region, please use another bank or crypto payment.';
            $this->loading = true;  // Show loading state to display the message
        } else {
            // Reset the message when other payment methods are selected
            $this->loadingMessage = '';
            $this->loading = false;  // Stop the loading state
        }
    }

    // Other logic for amount or cryptoCurrency updates
    if (in_array($propertyName, ['amount', 'cryptoCurrency'])) {
        $this->getConversionRate(); // Update conversion rate
        if ($this->cryptoCurrency) {
            $this->getCryptoAddress(); // Fetch crypto address when cryptocurrency is selected
        }
    }

    }

    public function updatedCountryId($countryId)
{
    // Check if banks exist for the selected country
    $bankAccount = BankAccount::where('country_id', $countryId)->first();

    if (!$bankAccount) {
        // No bank account found, set a session flash or public property
        // session()->flash('banks', 'No banks found for the selected country.');
    } else {
        // Proceed with bank details
        // You can fetch and store details for further use if needed
        session()->forget('banks'); // Remove any previous session message
    }
}

}
