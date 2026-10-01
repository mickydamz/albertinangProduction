<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Requires a verified email ONLY when the admin has enabled email verification
 * in Settings (email_verification_enabled). When the toggle is off (default),
 * this is a no-op so nothing changes for existing behaviour.
 */
class EnsureEmailIsVerifiedIfEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Setting::get('email_verification_enabled', '0') !== '1') {
            return $next($request);
        }

        $user = $request->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Your email address is not verified.'], 403)
                : redirect()->route('verification.notice')
                    ->with('error', 'Please verify your email address to continue.');
        }

        return $next($request);
    }
}
