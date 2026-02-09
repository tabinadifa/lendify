<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Ensure the authenticated user has one of the required roles.
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles, true)) {
            abort(403, 'Akses ditolak');
        }

        return $next($request);
    }
}

    