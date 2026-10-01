<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Not logged in → redirect to login, never a 403
        if (!auth()->check()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login')->with('error', 'Please log in to continue.');
        }

        // Logged in but wrong role → hard stop
        if (auth()->user()->role !== $role) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthorized.'], 403)
                : abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}