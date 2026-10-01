<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\TwoFactorCodeMail;
use App\Models\Setting;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Handle a successful authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        // Determine whether 2FA is required for this login
        $forceAdminTwoFactor = Setting::get('require_2fa_admin') === '1';
        $forceUsersTwoFactor = Setting::get('require_2fa_users') === '1';
        $isAdmin = $user->role === 'admin';

        $shouldTwoFactor = $user->two_factor_enabled
            || ($isAdmin && $forceAdminTwoFactor)
            || (!$isAdmin && $forceUsersTwoFactor);

        if ($shouldTwoFactor) {
            try {
                // Generate a new 2FA code and set its expiration
                $user->two_factor_code = rand(100000, 999999);
                $user->two_factor_expires_at = Carbon::now()->addMinutes(10);
                $user->save();
                
                // Send the 2FA code to the user's email
                Mail::to($user->email)->send(new TwoFactorCodeMail($user));
                
                // Success message for 2FA code sent
                session()->flash('success', 'A verification code has been sent to your email. Please check your inbox.');
                
                // Redirect to the 2FA verification page
                return redirect()->route('2fa');
                
            } catch (\Exception $e) {
                // Handle email sending failure
                session()->flash('error', 'Failed to send verification code. Please try again.');
                auth()->logout();
                return redirect()->route('login');
            }
        }

        // Success message for regular login
        session()->flash('success', 'Welcome back, ' . $user->name . '! You have been successfully logged in.');

        // Redirect to the page the user was trying to reach, or fall back to the role's dashboard
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user->role === 'supplier') {
            return redirect()->intended(route('supplier.dashboard'));
        } elseif ($user->role === 'affiliate') {
            return redirect()->intended(route('affiliate.dashboard'));
        } elseif ($user->role === 'manager') {
            return redirect()->intended(route('manager.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Handle a failed authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        session()->flash('error', 'Invalid credentials. Please check your email and password and try again.');
        
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => trans('auth.failed'),
            ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        session()->flash('success', 'You have been successfully logged out.');
        
        return redirect('/');
    }
}