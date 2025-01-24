<?php

// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin');  // Pastikan role super-admin bisa mengakses
    }
    public function index()
    {
        $users = User::all();
     
        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);
        $this->logActivity('Created', User::class, $user->id, 'User Dibuat.');

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:6',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'super-admin') {
            return back()->with('error', 'Super Admin cannot be deleted.');
        }

        $user->delete();
        $this->logActivity('Deleted', User::class, $user->id, 'User Dihapus.', $user->toArray());
        return redirect()->route('users.index');
    }

    protected function logActivity($action, $modelType, $modelId, $description = null)
    {
        \App\Models\ActivityLog::create([
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'user_id' => auth()->id(),
            'description' => $details['description'] ?? null,
            'old_data' => json_encode($details['old_data'] ?? null),
            'new_data' => json_encode($details['new_data'] ?? null),
        ]);
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        // Restore details
        foreach ($user->details()->withTrashed()->get() as $detail) {
            $detail->restore();
        }

        $this->logActivity('restored', User::class, $user->id, 'Sales Order restored.');

        return redirect()->route('superAdmin.SalesOrders.index')
            ->with('success', 'Sales Order restored successfully.');
    }
}
