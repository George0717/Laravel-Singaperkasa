<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected function authenticated(Request $request, $user)
{
    if ($user->role === 'super-admin') {
        return redirect()->route('superAdmin.dashboard');
    }

    // Default redirect for other users
    return redirect()->route('/');
}
}
