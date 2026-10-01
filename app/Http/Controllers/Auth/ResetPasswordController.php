<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/login';

    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectTo)
            ->with('status', trans($response))
            ->with('success', 'Your password has been reset successfully. Please log in.');
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        return back()->withErrors(['email' => trans($response)])
                     ->with('error', 'Invalid credentials. Please check your email and password and try again.');
    }
}