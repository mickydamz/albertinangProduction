<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class UserAccountController extends Controller
{
    public function showFundAccountForm()
    {
        return view('user.fundAccount');
    }

    public function fundAccount(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        // Assume we have a User model with an 'account_balance' field
        $user = Auth::user();
        $user->account_balance += $request->amount;
        $user->save();

        return redirect()->route('user.dashboard')->with('success', 'Account funded successfully.');
    }
}
