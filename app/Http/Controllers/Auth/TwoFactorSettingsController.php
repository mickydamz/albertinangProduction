<?php



namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TwoFactorSettingsController extends Controller
{
    // Show the 2FA settings page
    public function show()
    {
        return view('account.two-factor-settings');
    }

    // Enable 2FA
    public function enable(Request $request)
    {
        $user = $request->user();

        if (!$user->two_factor_code) {
            $user->two_factor_code =  rand(100000, 999999);
            $user->two_factor_expires_at = Carbon::now()->addMinutes(10);
            $user->save();

            Mail::to($user->email)->send(new TwoFactorCodeMail($user));
        }

        return back()->with('status', 'Two-factor authentication has been enabled.');
    }

    // Disable 2FA
    public function disable(Request $request)
    {
        $user = $request->user();
        $user->resetTwoFactorCode();
        // $user->reset2FA();
        return back()->with('status', 'Two-factor authentication has been disabled.');
    }
}
