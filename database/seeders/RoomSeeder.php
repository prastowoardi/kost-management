<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Facility;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['room_number' => '101', 'type' => 'single', 'price' => 850000, 'capacity' => 1, 'size' => 12, 'status' => 'available', 'description' => 'Kamar single lantai 1'],
            ['room_number' => '102', 'type' => 'single', 'price' => 850000, 'capacity' => 1, 'size' => 12, 'status' => 'available', 'description' => 'Kamar single lantai 1'],
            ['room_number' => '103', 'type' => 'double', 'price' => 1000000, 'capacity' => 2, 'size' => 18, 'status' => 'available', 'description' => 'Kamar double lantai 1'],
            ['room_number' => '104', 'type' => 'double', 'price' => 1000000, 'capacity' => 2, 'size' => 18, 'status' => 'available', 'description' => 'Kamar double lantai 1'],
            ['room_number' => '201', 'type' => 'single', 'price' => 1600000, 'capacity' => 1, 'size' => 15, 'status' => 'available', 'description' => 'Kamar single lantai 2'],
            ['room_number' => '202', 'type' => 'single', 'price' => 1600000, 'capacity' => 1, 'size' => 15, 'status' => 'available', 'description' => 'Kamar single lantai 2'],
        ];

        $roomFacilities = Facility::where('type', 'room')->pluck('id')->toArray();

        foreach ($rooms as $roomData) {
            $room = Room::create($roomData);
            // Attach facilities to room
            if (!empty($roomFacilities)) {
                $room->facilities()->attach($roomFacilities);
            }
        }

        echo "✅ " . count($rooms) . " kamar berhasil dibuat!\n";
    }
}