<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus user lama jika ada
        User::where('email', 'admin@kos.com')->delete();
        
        // Buat user admin baru
        User::create([
            'name' => 'Admin Kos',
            'email' => 'admin@kos.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        echo "✅ User admin@kos.com berhasil dibuat!\n";
        echo "   Email: admin@kos.com\n";
        echo "   Password: password\n";
    }
}