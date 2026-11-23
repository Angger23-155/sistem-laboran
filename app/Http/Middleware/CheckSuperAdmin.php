<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika belum login, atau sudah login tapi BUKAN superadmin -> Pindahkan ke dashboard
        if (auth()->check() && auth()->user()->role !== 'superadmin') {
            return redirect('/dashboard')->withErrors(['Akses ditolak. Hanya untuk SuperAdmin.']);
        }

        return $next($request);
    }
}