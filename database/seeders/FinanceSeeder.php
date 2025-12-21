<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Finance;
use Carbon\Carbon;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $finances = [
            [
                'type' => 'income',
                'category' => 'Lainnya',
                'transaction_date' => '2025-09-25',
                'amount' => 50000000.00,
                'description' => 'Tabungan bapak',
                'notes' => null,
            ],
            [
                'type' => 'income',
                'category' => 'Lainnya',
                'transaction_date' => '2025-11-12',
                'amount' => 50000000.00,
                'description' => 'Kredit BPD',
                'notes' => null,
            ],
            [
                'type' => 'income',
                'category' => 'Deposit',
                'transaction_date' => '2025-11-14',
                'amount' => 95000000.00,
                'description' => 'Kredit BPD',
                'notes' => null,
            ],
            [
                'type' => 'income',
                'category' => 'Deposit',
                'transaction_date' => '2025-12-12',
                'amount' => 168000.00,
                'description' => 'Ambil tabungan ibuk',
                'notes' => null,
            ],
            [
                'type' => 'expense',
                'category' => 'Lainnya',
                'transaction_date' => '2025-09-27',
                'amount' => 13275000.00,
                'description' => 'Comitmen fee CIPTA RUANG',
                'notes' => null,
            ],
            [
                'type' => 'expense',
                'category' => 'Lainnya',
                'transaction_date' => '2025-11-16',
                'amount' => 3780000.00,
                'description' => '4 kasur royal medicare',
                'notes' => 'Di Santi Mebel Godean',
            ],
            [
                'type' => 'expense',
                'category' => 'Lainnya',
                'transaction_date' => '2025-11-18',
                'amount' => 43925000.00,
                'description' => 'Pembayaran termin 1',
                'notes' => null,
            ],
            [
                'type' => 'expense',
                'category' => 'Lainnya',
                'transaction_date' => '2025-12-12',
                'amount' => 168000.00,
                'description' => '4 Sprei 120x200',
                'notes' => 'Di Shopee ibuk',
            ],
            [
                'type' => 'expense',
                'category' => 'Lainnya',
                'transaction_date' => '2025-12-13',
                'amount' => 2000000.00,
                'description' => 'DP 4 lemari + 4 dipan + 3 bantal + 3 guling',
                'notes' => 'Di Margo Murah Jl. Magelang',
            ],
            [
                'type' => 'expense',
                'category' => 'Lainnya',
                'transaction_date' => '2025-12-14',
                'amount' => 57200000.00,
                'description' => 'Pembayaran termin 2',
                'notes' => null,
            ],
            [
                'type' => 'expense',
                'category' => 'Lainnya',
                'transaction_date' => '2025-12-14',
                'amount' => 3400000.00,
                'description' => "Pembelian 4 closet 'Tidal 30cm' (include jet washer+naple T)",
                'notes' => 'Di QhomeMart Ringroad Timur',
            ],
            [
                'type' => 'income',
                'category' => 'Deposit',
                'transaction_date' => '2025-11-16',
                'amount' => 3780000.00,
                'description' => 'Tabungan mas',
                'notes' => null,
            ],
        ];

        $count = 0;
        foreach ($finances as $data) {
            Finance::updateOrCreate(
                [
                    // Kolom untuk pengecekan
                    'description' => $data['description'],
                    'transaction_date' => $data['transaction_date'],
                ],
                [
                    // Kolom yang akan diisi atau diperbarui
                    'type' => $data['type'],
                    'category' => $data['category'],
                    'amount' => $data['amount'],
                    'notes' => $data['notes'],
                ]
            );
            $count++;
        }

        echo "✅ " . $count . " data finance berhasil di-sync (dibuat/diupdate)!\n";
    }
}