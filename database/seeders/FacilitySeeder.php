<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            // Room facilities
            ['name' => 'AC', 'type' => 'room', 'quantity' => 1, 'condition' => 'good', 'description' => 'Air Conditioner'],
            ['name' => 'Kasur', 'type' => 'room', 'quantity' => 1, 'condition' => 'good', 'description' => 'Tempat tidur single'],
            ['name' => 'Lemari', 'type' => 'room', 'quantity' => 1, 'condition' => 'good', 'description' => 'Lemari pakaian'],
            ['name' => 'Meja Belajar', 'type' => 'room', 'quantity' => 1, 'condition' => 'good', 'description' => 'Meja dan kursi belajar'],
            ['name' => 'Kamar Mandi Dalam', 'type' => 'room', 'quantity' => 1, 'condition' => 'good', 'description' => 'Kamar mandi pribadi'],
            ['name' => 'Wi-Fi', 'type' => 'room', 'quantity' => 1, 'condition' => 'good', 'description' => 'Internet wireless'],
            
            // Common facilities
            ['name' => 'Parkir Motor', 'type' => 'common', 'quantity' => 20, 'condition' => 'good', 'description' => 'Area parkir motor'],
            ['name' => 'Dapur Bersama', 'type' => 'common', 'quantity' => 1, 'condition' => 'good', 'description' => 'Dapur untuk memasak'],
            ['name' => 'CCTV', 'type' => 'common', 'quantity' => 8, 'condition' => 'good', 'description' => 'Kamera keamanan'],
        ];

        foreach ($facilities as $facility) {
            Facility::create($facility);
        }

        echo "✅ " . count($facilities) . " fasilitas berhasil dibuat!\n";
    }
}