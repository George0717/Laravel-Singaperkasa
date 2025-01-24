<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Melakukan autentikasi
        $request->authenticate();

        // Regenerasi session
        $request->session()->regenerate();

        // Cek role pengguna dan arahkan ke halaman yang sesuai
        if (Auth::user()->role === 'super-admin') {
            return redirect()->route('superAdmin.SalesOrders.dashboard');
        }
       else if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.SalesOrders.dashboard');
        }
        else 
        {
            return redirect()->route('SalesOrders.dashboard');
        }
       

        // Jika bukan super-admin, arahkan ke halaman default yang ditentukan
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logout dan invalidate session
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
