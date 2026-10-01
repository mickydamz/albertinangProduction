<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Not logged in → send to login, not a 403
        if (!auth()->check()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login')->with('error', 'Please log in to continue.');
        }

        // Logged in but wrong role → unauthorized
        if (!in_array(auth()->user()->role, ['manager', 'admin'])) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthorized. Manager access only.'], 403)
                : abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}