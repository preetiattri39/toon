<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the 'super-admin' role exists, if not, create it
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
    
        // Check if the 'Super Admin' user already exists
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@gmail.com'], // Check if an admin with this email exists
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // Hash the password before saving
            ]
        );

        // Assign the 'super-admin' role to the admin if not already assigned
        if (!$admin->hasRole('super-admin')) {
            $admin->assignRole($superAdminRole);
        }
    }
}
