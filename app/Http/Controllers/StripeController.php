<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use App\Models\Deposit;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    /**
     * Display the checkout page.
     *
     * @return View|Factory|Application
     */
    public function checkout(): View|Factory|Application
    {
        return view('checkout');
    }

    /**
     * Create a test checkout session.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    public function test(Request $request): RedirectResponse
    {
        $unitAmount = $request->input('unit_amount') * 100; // Convert to the smallest currency unit
        // $currency = $request->input('currency', 'gbp'); // Default to GBP if no currency is selected

        Stripe::setApiKey(config('stripe.test.sk')); // Use the test key

        $session = Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'USD',
                        'product_data' => [
                            'name' => 'Deposit',
                        ],
                        'unit_amount' => $unitAmount,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('stripe.handleStripeSuccess') . '?session_id={CHECKOUT_SESSION_ID}', // Pass session_id
            'cancel_url' => route('checkout'),
        ]);

        return redirect()->away($session->url);
    }

    /**
     * Create a live checkout session.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    public function live(Request $request): RedirectResponse
    {
        $unitAmount = $request->input('unit_amount') * 100; // Convert to the smallest currency unit
        // $currency = $request->input('currency', 'gbp'); // Default to GBP if no currency is selected

        Stripe::setApiKey(config('stripe.live.sk')); // Use the live key

        $session = Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'USD',
                        'product_data' => [
                            'name' => 'Deposit',
                        ],
                        'unit_amount' => $unitAmount,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('stripe.handleStripeSuccess') . '?session_id={CHECKOUT_SESSION_ID}', // Pass session_id
            'cancel_url' => route('checkout'),
        ]);

        return redirect()->away($session->url);
    }

    /**
     * Handle the success response from Stripe and create the deposit.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function handleStripeSuccess(Request $request): RedirectResponse
{
    // Get the session ID from the query parameter
    $sessionId = $request->input('session_id');

    Stripe::setApiKey(config('stripe.test.sk')); // Use live key for production

    try {
        // Retrieve the session object from Stripe
        $session = Session::retrieve($sessionId);

        // Ensure the payment was successful
        if ($session->payment_status == 'paid') {
            // Begin a transaction to ensure atomicity
            \DB::beginTransaction();

            try {
                // Create a deposit record
                $deposit = Deposit::create([
                    'user_id' => Auth::id(),
                    'payment_method_id' => 2, // Assuming Stripe is method ID 2, adjust accordingly
                    'country_id' => 1, // Set your country or pass as needed
                    'crypto_currency' => 'USD', // Currency used for the payment
                    'amount' => $session->amount_total / 100, // Convert from cents to full currency amount
                    'transaction_picture' => null, // Optional, store the receipt if needed
                    'status' => 1, // Successful payment status
                ]);

                // Update the user's account balance
                $user = Auth::user();
                $user->account_balance += $deposit->amount;
                $user->save();

                // Commit the transaction
                \DB::commit();

                // Redirect to a success page
                return redirect()->route('deposit.index')->with('success', 'Deposit Successful!');
            } catch (\Exception $e) {
                // If something goes wrong, rollback the transaction
                \DB::rollBack();

                // Handle any errors related to deposit creation or balance update
                return redirect()->route('checkout')->with('error', 'There was an error processing your deposit.');
            }
        } else {
            // If payment is not successful, handle accordingly
            return redirect()->route('checkout')->with('error', 'Payment failed. Please try again.');
        }
    } catch (\Exception $e) {
        // Handle any errors (e.g., invalid session ID or Stripe issues)
        return redirect()->route('checkout')->with('error', 'There was an error processing your payment.');
    }
}

    /**
     * Display the success page after successful payment.
     *
     * @return View|Factory|Application
     */
    public function success(): View|Factory|Application
    {
        return view('success');
    }
}
