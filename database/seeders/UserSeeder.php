<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Account
        User::updateOrCreate(
            ['email' => 'admin@kos.com'],
            [
                'name' => 'Admin Kos',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Staff Account
        User::updateOrCreate(
            ['email' => 'staff@kos.com'],
            [
                'name' => 'Staff Kos',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Tenant Account
        User::updateOrCreate(
            ['email' => 'tenant@kos.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role' => 'tenant',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}