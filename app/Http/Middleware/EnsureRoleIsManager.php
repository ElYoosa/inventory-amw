<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleIsManager
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'manager') {
            abort(403, 'Akses ditolak. Hanya manager yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
