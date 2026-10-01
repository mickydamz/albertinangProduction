<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;

class TwoFactorController extends Controller
{
    // Show the 2FA verification form
    public function show()
    {
        return view('auth.two-factor');
    }

    // Verify the 2FA code
    // public function verify(Request $request)
    // {
    //     $request->validate([
    //         'two_factor_code' => 'required',
    //     ]);

    //     $user = auth()->user();

    //     if ($user->two_factor_code === $request->two_factor_code && $user->two_factor_expires_at->isFuture()) {
    //         // Clear the two-factor code and expiration time after successful verification
    //         $user->resetTwoFactorCode();

    //         return redirect()->intended('/home');
    //     }

    //     return back()->withErrors(['two_factor_code' => 'The provided two-factor code is incorrect or has expired.']);
    // }

   
public function verify(Request $request)
{
    $request->validate([
        'two_factor_code' => 'required',
    ]);

    $user = auth()->user();

    // Convert the two_factor_expires_at field to a Carbon instance
    $expiresAt = Carbon::parse($user->two_factor_expires_at);

    // Check if the code is correct and if the expiration time is in the future
    if ((string) $user->two_factor_code === (string) $request->two_factor_code && $expiresAt->isFuture()) {
        $user->resetTwoFactorCode();

        session()->flash('success', 'Identity verified. Welcome back, ' . $user->name . '!');

        if ($user->role === 'admin')     return redirect()->intended(route('admin.dashboard'));
        if ($user->role === 'supplier')  return redirect()->intended(route('supplier.dashboard'));
        if ($user->role === 'affiliate') return redirect()->intended(route('affiliate.dashboard'));
        if ($user->role === 'manager')   return redirect()->intended(route('manager.dashboard'));
        return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors(['two_factor_code' => 'The provided two-factor code is incorrect or has expired.']);
}

public function resend(Request $request)
{
    $user = auth()->user();
    // Generate a new 2FA code and expiration time
    $user->two_factor_code = rand(100000, 999999);  // Example 6-digit code
    $user->two_factor_expires_at = now()->addMinutes(10);  // Code expires in 10 minutes

    $user->save();
    // Send the 2FA code to the user again
    Mail::to($user->email)->send(new TwoFactorCodeMail($user));

    return back()->with('status', 'A new 2FA code has been sent to your email.');
}

}
