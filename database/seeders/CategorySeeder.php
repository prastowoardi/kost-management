<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Kategori Pemasukan
            ['name' => 'Pembayaran Sewa', 'type' => 'income'],
            ['name' => 'Deposit', 'type' => 'income'],
            ['name' => 'Denda Keterlambatan', 'type' => 'income'],
            ['name' => 'Pemasukan Lainnya', 'type' => 'income'],

            // Kategori Pengeluaran
            ['name' => 'Gaji Karyawan', 'type' => 'expense'],
            ['name' => 'Listrik', 'type' => 'expense'],
            ['name' => 'Air', 'type' => 'expense'],
            ['name' => 'Internet', 'type' => 'expense'],
            ['name' => 'Perawatan Bangunan', 'type' => 'expense'],
            ['name' => 'Perbaikan Fasilitas', 'type' => 'expense'],
            ['name' => 'Kebersihan', 'type' => 'expense'],
            ['name' => 'Keamanan', 'type' => 'expense'],
            ['name' => 'Pajak', 'type' => 'expense'],
            ['name' => 'Pengeluaran Lainnya', 'type' => 'expense'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category['name']],
                $category
            );
        }
    }
}