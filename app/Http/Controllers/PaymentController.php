<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;  // Assuming you have a Transaction model to store transaction details
use App\Models\PendingCheckout;
use App\Services\StripeOrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;  // To make API calls to Paystack
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    /**
     * Create a Stripe PaymentIntent for an already-saved checkout.
     *
     * The browser sends only the PendingCheckout reference (created by
     * OrderController::saveCheckout, which prices everything from the database).
     * The charge amount is computed here, server-side, from that trusted NGN
     * total — never taken from the request — so the customer cannot dictate what
     * they pay. The reference is stamped into the intent's metadata so the
     * fulfilment step can prove the payment belongs to this order.
     */
    public function createPaymentIntent(Request $request)
    {
        $secret = config('services.stripe.secret');

        // Fail cleanly instead of throwing a raw Stripe error at the customer
        // when keys are missing or still the shipped placeholder.
        if (!StripeOrderService::keyConfigured($secret)) {
            Log::error('Stripe createPaymentIntent called but STRIPE_SECRET_KEY is not configured.');
            return response()->json([
                'error' => 'Card payments are temporarily unavailable. Please use Paystack or try again later.',
            ], 503);
        }

        $validated = $request->validate([
            'reference' => 'required|string|max:255',
        ]);

        // The reference must point at a real, unfulfilled checkout owned by this
        // session — otherwise there is nothing legitimate to charge for.
        $pending = PendingCheckout::where('reference', $validated['reference'])
            ->whereNull('fulfilled_at')
            ->first();

        if (!$pending || (Auth::id() && $pending->user_id && $pending->user_id !== Auth::id())) {
            return response()->json(['error' => 'Checkout session not found. Please start again.'], 422);
        }

        $amountCents = app(StripeOrderService::class)->expectedUsdCents((float) $pending->total_ngn);

        Stripe::setApiKey($secret);

        try {
            $paymentIntent = PaymentIntent::create([
                'amount'   => $amountCents,
                'currency' => 'usd',
                'metadata' => [
                    'reference' => $pending->reference,
                    'user_id'   => (string) ($pending->user_id ?? Auth::id() ?? ''),
                ],
            ], ['idempotency_key' => 'checkout-' . $pending->reference]);
            $pending->update(['gateway' => 'stripe', 'payment_intent_id' => $paymentIntent->id, 'gateway_amount' => $amountCents]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'reference'    => $pending->reference,
                'total_ngn'    => $pending->total_ngn,
                'total_usd'    => $amountCents / 100,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe createPaymentIntent failed: ' . $e->getMessage());
            return response()->json(['error' => 'Could not start the card payment. Please try again.'], 500);
        }
    }


    public function initiatePayment(Request $request)
    {
        // Validate the incoming payment amount
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'email' => 'required|email'
        ]);

        // Get payment details from the request
        $amount = $request->input('amount');
        $email = $request->input('email');

        // Generate a unique reference for the transaction
        $reference = 'paystack_' . uniqid();

        // Store the transaction details in the database
        $transaction = new Transaction();
        $transaction->email = $email;
        $transaction->amount = $amount;
        $transaction->reference = $reference;
        $transaction->status = 'pending'; // Status will be pending until verified
        $transaction->save();

        // Initiate payment on Paystack by using the reference and amount
        return response()->json([
            'status' => 'success',
            'message' => 'Payment initiated successfully',
            'reference' => $reference
        ]);
    }

    /**
     * Verify the payment using Paystack API
     */
    public function verifyPaystackPayment(Request $request)
    {
        $reference = $request->input('reference');

        // Ensure the reference is not empty
        if (!$reference) {
            return response()->json(['status' => 'failed', 'message' => 'No reference provided'], 400);
        }

        // Call Paystack API to verify the transaction
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY')
        ])->get("https://api.paystack.co/transaction/verify/{$reference}");

        // Get the response data
        $data = $response->json();

        if ($data['status'] && $data['data']['status'] === 'success') {
            // Payment was successful, update the transaction status
            $transaction = Transaction::where('reference', $reference)->first();

            if ($transaction) {
                $transaction->status = 'successful';  // Set status to successful
                $transaction->save();
                
                // You can trigger actions here, like sending a receipt email, etc.
                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment successfully verified'
                ]);
            }

            return response()->json([
                'status' => 'failed',
                'message' => 'Transaction not found'
            ], 404);
        } else {
            // Payment verification failed
            return response()->json([
                'status' => 'failed',
                'message' => 'Payment verification failed'
            ], 400);
        }
    }
}
