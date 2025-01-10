<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'super-admin') {
            return redirect()->route('superAdmin.dashboard'); // Arahkan ke dashboard super-admin
        }

        return $next($request); // Lanjutkan ke request berikutnya
    }
}
