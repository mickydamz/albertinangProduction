<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Deposit;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use net\authorize\api\constants\ANetEnvironment;

class AuthorizeNetController extends Controller
{
    /**
     * Show the checkout page.
     */
    public function checkout()
    {
        return view('checkout');
    }

    /**
     * Handle the transaction request.
     */
    public function test(Request $request): RedirectResponse
    {
        // Retrieve the payment amount and card details from the request
        $unitAmount = $request->input('unit_amount');
        $amount = number_format($unitAmount, 2, '.', ''); // Format amount to two decimal places

        // Validate input (you can uncomment this if you want to validate the card details)
        // $request->validate([
        //     'card_number' => 'required|numeric|digits:16',
        //     'expiration_date' => 'required|date_format:m/y',
        //     'cvv' => 'required|numeric|digits:3',
        // ]);

        $cardNumber = $request->input('card_number');
        $expirationDate = $request->input('expiration_date');
        $cvv = $request->input('cvv');

        // Set up the Merchant Authentication object
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(env('AUTHORIZENET_API_LOGIN_ID')); // API login ID
        $merchantAuthentication->setTransactionKey(env('AUTHORIZENET_TRANSACTION_KEY')); // Transaction key

        // Set up the CreditCard object
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);

        // Ensure expiration date is in MMYYYY format (e.g., "012025" for January 2025)
        $expirationDateFormatted = \DateTime::createFromFormat('m/y', $expirationDate);
        $creditCard->setExpirationDate($expirationDateFormatted->format('mY'));

        $creditCard->setCardCode($cvv);

        // Set up the PaymentType object
        $payment = new AnetAPI\PaymentType();
        $payment->setCreditCard($creditCard);

        // Set up the Order object
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber(rand(100000, 999999)); // Generate a random invoice number
        $order->setDescription('Test Deposit');

        // Set up the TransactionRequest object
        $transactionRequest = new AnetAPI\TransactionRequestType();
        $transactionRequest->setTransactionType('AUTH_CAPTURE'); // Use string directly
        $transactionRequest->setAmount($amount);
        $transactionRequest->setPayment($payment);
        $transactionRequest->setOrder($order);

        // Set up the complete Transaction Request object
        $createTransactionRequest = new AnetAPI\CreateTransactionRequest();
        $createTransactionRequest->setMerchantAuthentication($merchantAuthentication);
        $createTransactionRequest->setRefId("ref" . time()); // Unique reference ID
        $createTransactionRequest->setTransactionRequest($transactionRequest);

        // Execute the transaction
        $controller = new AnetController\CreateTransactionController($createTransactionRequest);
        $response = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX); // Use SANDBOX for testing, switch to PRODUCTION for live

        // Handle the response
        if ($response != null && $response->getMessages()->getResultCode() == 'Ok') {
            // If the transaction is successful, create a deposit record and update the user's balance
            $this->createDeposit($amount);

            return redirect()->route('deposit.index')->with('success', 'Deposit successful!');
        } else {
            // If the transaction fails, get the error message and redirect to checkout
            $errorMessage = $response->getMessages()->getMessage()[0]->getText();
            return redirect()->route('checkout')->with('error', 'Payment failed: ' . $errorMessage);
        }
    }

    /**
     * Create a deposit record after successful payment.
     */
    private function createDeposit($amount)
    {
        DB::beginTransaction();

        try {
            // Create the deposit record in the database
            $deposit = Deposit::create([
                'user_id' => Auth::id(),
                'payment_method_id' => 1, // Assuming Authorize.Net is method ID 1
                'country_id' => 1, // You can set country dynamically if needed
                'crypto_currency' => 'USD',
                'amount' => $amount,
                'transaction_picture' => null, // Optional, if you store transaction receipts
                'status' => 1, // Payment successful
            ]);

            // Update the user's account balance
            $user = Auth::user();
            $user->account_balance += $deposit->amount;
            $user->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            \Log::error('Deposit creation failed: ' . $e->getMessage());
            throw $e; // Rethrow the exception to let it propagate
        }
    }

    /**
     * Handle the success page (redirect after payment).
     */
    public function success()
    {
        return view('success');
    }
}
