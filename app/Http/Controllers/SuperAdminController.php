<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    /**
     * Menampilkan dashboard Super Admin.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::all(); // Menampilkan semua pengguna
        return view('superadmin.dashboard', compact('users'));
    }

    /**
     * Menampilkan daftar pengguna.
     *
     * @return \Illuminate\View\View
     */
    public function users()
    {
        $users = User::all(); // Mengambil semua pengguna
        return view('superadmin.users.index', compact('users'));
    }

    /**
     * Menampilkan formulir untuk membuat pengguna baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('superadmin.users.create'); // Formulir untuk membuat pengguna baru
    }

    /**
     * Menyimpan pengguna baru setelah memeriksa batas maksimum.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Tentukan batas maksimum pengguna
        $maxUsers = 4;
        $currentUserCount = User::count();

        // Periksa jika jumlah pengguna sudah mencapai batas maksimum
        if ($currentUserCount >= $maxUsers) {
            return redirect()->route('superadmin.users.create')
                             ->with('error', 'User limit reached. Cannot create more users.');
        }

        // Validasi data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,user,super-admin',
        ]);

        // Membuat pengguna baru
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('superadmin.users')->with('success', 'User created successfully.');
    }

    /**
     * Menampilkan formulir untuk mengedit pengguna.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id); // Menampilkan pengguna berdasarkan ID
        return view('superadmin.users.edit', compact('user')); // Menampilkan form edit
    }

    /**
     * Memperbarui pengguna.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id); // Mengambil data pengguna berdasarkan ID

        // Validasi data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:admin,user,super-admin',
        ]);

        // Update data pengguna
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Redirect dengan pesan sukses
        return redirect()->route('superadmin.users')->with('success', 'User updated successfully.');
    }

    /**
     * Menghapus pengguna.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('superadmin.users')->with('success', 'User deleted successfully.');
    }

   
}
