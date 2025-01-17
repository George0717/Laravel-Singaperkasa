<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Cek peran pengguna
            $role = Auth::user()->role;

            if ($role == 'super-admin') {
                return redirect()->route('superAdmin.SalesOrders.dashboard');
            } elseif ($role == 'admin') {
                return redirect()->route('admin.SalesOrders.dashboard');
            }
        }

        return $next($request);
    }
}

