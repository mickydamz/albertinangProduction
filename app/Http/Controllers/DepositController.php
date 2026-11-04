<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\Country;
use App\Models\BankAccount;
use App\Models\Deposit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
// use App\Models\Deposit;
use Livewire\WithFileUploads; // Add this import at the top of your component

class DepositController extends Controller
{
    public function index()
    {
        // Fetch available payment methods and countries for bank transfer
        $paymentMethods = PaymentMethod::all();
        $countries = Country::all();

        return view('deposit.index', compact('paymentMethods', 'countries'));
    }


    public function transactionPic()
    {
        // Fetch available payment methods and countries for bank transfer
        $paymentMethods = PaymentMethod::all();
        $countries = Country::all();

        return view('deposit.transaction-receipt', compact('paymentMethods', 'countries'));
    }

public function addMissingCountries()
{
    // List of all countries (can be dynamically retrieved from an API or static array)
    $allCountries = [
        'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda', 'Argentina',
        'Armenia', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados',
        'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana',
        'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cabo Verde', 'Cambodia', 'Cameroon',
        'Canada', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo',
        'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Democratic Republic of the Congo',
        'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea',
        'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Fiji', 'Finland', 'France', 'Gabon', 'Gambia', 'Georgia',
        'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti',
        'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel', 'Italy',
        'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Korea, North', 'Korea, South', 'Kuwait',
        'Kyrgyzstan', 'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania',
        'Luxembourg', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania',
        'Mauritius', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique',
        'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria',
        'North Macedonia', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Panama', 'Papua New Guinea', 'Paraguay',
        'Peru', 'Philippines', 'Poland', 'Portugal', 'Qatar', 'Romania', 'Russia', 'Rwanda', 'Saint Kitts and Nevis',
        'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia',
        'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands',
        'Somalia', 'South Africa', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Sweden', 'Switzerland',
        'Syria', 'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago',
        'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom',
        'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican City', 'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe'
    ];

    // Fetch existing countries in the database
    $existingCountries = DB::table('countries')->pluck('name')->toArray();

    // Filter the list to find countries that aren't already in the database
    $missingCountries = array_diff($allCountries, $existingCountries);

    // Insert missing countries into the database
    foreach ($missingCountries as $country) {
        DB::table('countries')->insert([
            'name' => $country,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    return 'Missing countries have been added!';
}


    public function submit(Request $request)
    {
        // Validate the request (as already done in your code)
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:1000',
            'transaction_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Process transaction picture if uploaded
        $transactionPicturePath = null;
        if ($request->hasFile('transaction_picture')) {
            $transactionPicturePath = $request->file('transaction_picture')->store('transactions', 'public');
        }

        // Create deposit record
        $deposit = Deposit::create([
            'user_id' => auth()->id(),
            'payment_method_id' => $request->payment_method_id,
            'country_id' => $request->country,
            'crypto_currency' => $request->crypto_currency,
            'amount' => $request->amount,
            'transaction_picture' => $transactionPicturePath,
            'status' => 0
        ]);

        return redirect()->route('deposit.index')->with('success', 'Your deposit request has been submitted successfully.');
    }

    public function getBankDetails($countryId)
    {
        // Fetch bank account details for a given country
        $bankAccount = BankAccount::where('country_id', $countryId)->get();

        if ($bankAccount->isEmpty()) {
            return response()->json(['message' => 'No bank details available'], 404);
        }

        return response()->json($bankAccount);
    }

    public function getCryptoAddress($cryptoCurrency)
    {
        // Logic to fetch or generate crypto address for selected cryptocurrency
        // Example response for mock crypto address
        $addresses = [
            'BTC' => '1BitcoinAddressExample',
            'ETH' => '0xEthereumAddressExample',
            'USDT' => 'TetherAddressExample123',
        ];

        $address = $addresses[$cryptoCurrency] ?? null;

        if (!$address) {
            return response()->json(['message' => 'No address found for the selected cryptocurrency'], 404);
        }

        return response()->json(['address' => $address]);
    }

    public function userDeposits()
{
    // Fetch deposits only for the logged-in user
    $deposits = Deposit::where('user_id', auth()->id())->latest()->get();

    return view('user.deposit.index', compact('deposits'));
}



}
