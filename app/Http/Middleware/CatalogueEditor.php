<?php
namespace App\Http\Middleware;
use Closure;
class CatalogueEditor {
    public function handle($request, Closure $next) {
        abort_unless(auth()->check() && auth()->user()->role === 'catalogue_editor'
            && auth()->user()->status !== 'banned', 403);
        return $next($request);
    }
}
