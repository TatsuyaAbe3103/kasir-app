<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserCanAudit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->canAudit()) {
            return redirect()->route('pos.index')->with('error', 'Akses ditolak! Halaman audit stok hanya dapat diakses oleh Auditor atau Admin.');
        }

        return $next($request);
    }
}
