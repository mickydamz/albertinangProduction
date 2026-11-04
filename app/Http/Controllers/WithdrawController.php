<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\Withdrawal;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class WithdrawController extends Controller
{
    public function index()
    {
        // Fetch available payment methods (Bank or Crypto)
        $paymentMethods = PaymentMethod::whereIn('name', ['bank', 'crypto'])->get();

        return view('withdraw.index', compact('paymentMethods'));
    }
    public function submit(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:1000',
            'withdrawal_details' => 'required|string',
        ]);

        // Get the current user's ID
        $userId = auth()->id();

        // Retrieve the user's profile
        $user = auth()->user();

        // Define the required fields for profile completion
        $requiredFields = [
            'name', 'email', 'location', 'description', 'password', 'role', 'city', 'postal_code',
            'shipping_address', 'account_balance', 'phone_no', 'avatar'
        ];

        // Count the filled fields, including idverification status
        $filledFields = 0;
        foreach ($requiredFields as $field) {
            if (!empty($user->$field)) {
                $filledFields++;
            }
        }

        // Check if ID verification is completed (status === 1)
        if ($user->idverification && $user->idverification->status === 1) {
            $filledFields++; // Count ID verification as a completed field
        }

        // Calculate the percentage of filled fields
        $totalFields = count($requiredFields) + 1; // Include idverification in the total count
        $completionPercentage = ($filledFields / $totalFields) * 100;

        // Set the withdrawal limit based on the completion percentage
        if ($completionPercentage >= 100) {
            $withdrawalLimit = 100000; // Fully completed profile - $100,000 limit
        } elseif ($completionPercentage >= 60) {
            $withdrawalLimit = 10000; // 60% completed profile - $10,000 limit
        } else {
            $withdrawalLimit = 500; // Less than 60% - $500 limit
        }

        // Check the total withdrawal amount in the last 48 hours
        $totalWithdrawnInLast48Hours = Withdrawal::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours(48))  // Filter for withdrawals in the last 48 hours
            ->sum('amount');  // Sum of the withdrawal amounts

        // Check if the requested withdrawal exceeds the limit
        if ($totalWithdrawnInLast48Hours + $request->amount > $withdrawalLimit) {
            return redirect()->back()->withErrors(['amount' => 'You have reached your maximum withdrawal limit for the last 48 hours.']);
        }

        // Proceed with creating the withdrawal
        $withdrawal = new Withdrawal();
        $withdrawal->user_id = $userId;
        $withdrawal->payment_method_id = $request->payment_method_id;
        $withdrawal->amount = $request->amount;
        $withdrawal->withdrawal_details = $request->withdrawal_details;
        $withdrawal->status = '0'; // Example status
        $withdrawal->save();

        return redirect()->back()->with('success', 'Withdrawal request submitted successfully!');
    }


    public function userWithdrawals()
    {
        // Fetch withdrawals only for the logged-in user
        $withdrawals = Withdrawal::where('user_id', auth()->id())->get();

        return view('user.withdraw.index', compact('withdrawals'));
    }
}
