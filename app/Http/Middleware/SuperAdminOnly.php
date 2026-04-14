<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminOnly
{
    private const SUPER_ADMIN_EMAIL = 'admin@asesco.com';

    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check() || auth()->user()->email !== self::SUPER_ADMIN_EMAIL) {
            abort(403, 'Acceso restringido.');
        }

        return $next($request);
    }
}
