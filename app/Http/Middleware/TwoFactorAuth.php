<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TwoFactorAuth
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user      = Auth::user();
            $expiresAt = Carbon::parse($user->two_factor_expires_at);

            if ($user->two_factor_code && $expiresAt->isFuture()) {
                if (!$request->routeIs('2fa') &&
                    !$request->routeIs('2fa.verify') &&
                    !$request->routeIs('resend-2fa') &&
                    !$request->routeIs('logout')) {
                    return redirect()->route('2fa');
                }
            }
        }

        return $next($request);
    }
}
