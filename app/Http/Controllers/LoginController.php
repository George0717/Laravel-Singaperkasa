<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected function authenticated(Request $request, $user)
{
    if ($user->role === 'super-admin') {
        return redirect()->route('superAdmin.SalesOrders.dashboard');
    }
    else if ($user->role === 'admin') {
        return redirect()->route('admin.SalesOrders.dashboard');
    }

    // Default redirect for other users
    return redirect()->route('/');
}
}
