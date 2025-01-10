<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super-admin');  // Hanya Super Admin yang bisa mengakses
    }

    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index()
    {
        $users = User::all();  // Mengambil semua pengguna
        return view('superAdmin.users.index', compact('users'));
    }

    /**
     * Menampilkan halaman form untuk membuat pengguna baru.
     */
    public function create()
    {
        return view('superAdmin.users.create');
    }

    /**
     * Menyimpan pengguna baru ke dalam database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super-admin,admin,sales',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('superAdmin.users.index')->with('success', 'User created successfully!');
    }

    /**
     * Menampilkan halaman form untuk mengedit pengguna.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);  // Mengambil data pengguna berdasarkan ID
        return view('superAdmin.users.edit', compact('user'));
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:super-admin,admin,sales',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->role = $request->role;
        $user->save();

        return redirect()->route('superAdmin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        // Mencari pengguna berdasarkan ID
        $user = User::findOrFail($id);

        // Menghapus pengguna
        $user->delete();

        // Redirect ke daftar pengguna dengan pesan sukses
        return redirect()->route('superAdmin.users.index')->with('success', 'User deleted successfully!');
    }
}
