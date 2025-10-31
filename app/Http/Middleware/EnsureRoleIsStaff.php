<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleIsStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'staff') {
            abort(403, 'Akses ditolak. Hanya staff yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
