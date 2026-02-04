<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, $next, ...$roles)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            abort(403, 'Akses ditolak');
        }

        return $next($request);
    }
}

    