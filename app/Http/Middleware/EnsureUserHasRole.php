<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    // Handle an incoming request and verify the user's role.
    public function handle(Request $request, Closure $next, string ...$roles): Response {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! in_array($request->user()->role->value, $roles, true)) {
            abort(403, 'Unauthorized access to this section.');
        }

        return $next($request);
    }
}