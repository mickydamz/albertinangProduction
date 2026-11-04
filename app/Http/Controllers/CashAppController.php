<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Deposit;
use Illuminate\Support\Facades\Auth;

class CashAppController extends Controller
{
    /**
     * Show the CashApp deposit form.
     *
     * @return View|Factory|Application
     */
    public function showCashAppForm(): View|Factory|Application
    {
        return view('cashapp-deposit');
    }

    /**
     * Handle the CashApp deposit payment and create a deposit record.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function cashAppDeposit(Request $request): RedirectResponse
    {
        // Validate the form input
        $request->validate([
            'unit_amount' => 'required|numeric|min:1',

        ]);

        // Fetch the amount and payment confirmation number
        $amount = $request->input('unit_amount');
        // $paymentConfirmation = $request->input('payment_confirmation');

        // Simulate the confirmation of the CashApp payment (in real cases, you'd verify the payment manually)
        // Assume a simple validation here; you can modify it to suit your business logic
        // if ($paymentConfirmation !== 'valid-payment-id') {
        //     return redirect()->route('cashapp-deposit.form')->with('error', 'Payment confirmation is invalid. Please try again.');
        // }

        // Create a deposit record in the database
        try {
            // Begin database transaction
            \DB::beginTransaction();

            $deposit = Deposit::create([
                'user_id' => Auth::id(),
                'payment_method_id' => 3, // Assuming CashApp has method ID 3
                'country_id' => 1, // Set your country or pass as needed
                'crypto_currency' => 'USD', // Currency used for the payment
                'amount' => $amount, // Payment amount
                'transaction_picture' => null, // Optionally, you can allow the user to upload a picture/receipt
                'status' => 1, // Payment is confirmed and successful
            ]);

            // Update user's account balance
            $user = Auth::user();
            $user->account_balance += $deposit->amount;
            $user->save();

            // Commit the transaction
            \DB::commit();

            // Redirect to the success page
            return redirect()->route('deposit.index')->with('success', 'Deposit Successful!');
        } catch (\Exception $e) {
            // If something goes wrong, roll back the transaction
            \DB::rollBack();
            return redirect()->route('cashapp-deposit.form')->with('error', 'There was an error processing your deposit.');
        }
    }

    /**
     * Display a success page after deposit.
     *
     * @return View|Factory|Application
     */
    public function success(): View|Factory|Application
    {
        return view('success');
    }
}
