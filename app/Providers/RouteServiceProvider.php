<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by the Auth controller to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        // Default limiter for the `api` middleware group. The stock 60/min is too
        // tight for read endpoints like the geo/states lookup that the registration
        // form hits as the user browses countries — a 429 there makes the states
        // dropdown appear to vanish. Keyed by IP for guests (no user on register).
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(300)->by($request->user()?->id ?: $request->ip());
        });

        // Both checkout endpoints require auth, so we always have a user ID.
        // Keying by user ID (not IP) avoids blocking real customers on Nigerian
        // mobile networks where many users share a single carrier NAT address.
        RateLimiter::for('checkout-save', function (Request $request) {
            return Limit::perMinute(20)->by('checkout-save:user:' . $request->user()?->id ?? $request->ip());
        });

        RateLimiter::for('checkout-confirm', function (Request $request) {
            return Limit::perMinute(10)->by('checkout-confirm:user:' . $request->user()?->id ?? $request->ip());
        });
    }

    /**
     * Get the redirect path for authenticated users.
     *
     * @return string
     */
    public static function redirectTo()
    {
        // Get the currently authenticated user
        if (Auth::check()) {
            $user = Auth::user();
    
            // If user has 2FA enabled, redirect to the 2FA page
            if ($user->two_factor_code) {
                return route('2fa');
            }
    
            // Role-based redirection logic
            if ($user->role === 'admin') {
                return route('admin.dashboard');
            } elseif ($user->role === 'supplier') {
                return route('supplier.dashboard');
            } elseif ($user->role === 'affiliate') {
                return route('affiliate.dashboard');
            }
    
            // Default redirection for other users
            return route('dashboard');
        }
    
        // Default fallback for non-logged-in users
        return self::HOME;
    }
    
}
