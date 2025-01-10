<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role 'super-admin' ada, jika belum buat
        $role = Role::firstOrCreate(['name' => 'super-admin']);

        // Pastikan user 'superadmin@example.com' ada, jika belum buat
        $user = User::firstOrCreate(
            ['email' => 'super@example.com'],
            [
                'name' => 'Super Admin',
                'email' => 'super@example.com',
                'password' => bcrypt('12345678'), // Gantilah password dengan yang aman
            ]
        );
 
        // Assign role 'super-admin' ke user
        $user->assignRole('super-admin');
    }
}
