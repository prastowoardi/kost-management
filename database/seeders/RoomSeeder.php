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
            ['room_number' => '1', 'type' => 'singlenoac', 'price' => 850000, 'capacity' => 1, 'size' => 12, 'status' => 'available', 'description' => 'Kamar ukuran 3x4m'],
            ['room_number' => '2', 'type' => 'singlenoac', 'price' => 850000, 'capacity' => 1, 'size' => 12, 'status' => 'available', 'description' => 'Kamar ukuran 3x4m'],
            ['room_number' => '3', 'type' => 'singlenoac', 'price' => 850000, 'capacity' => 1, 'size' => 12, 'status' => 'available', 'description' => 'Kamar ukuran 3x4m'],
            ['room_number' => '4', 'type' => 'singlenoac', 'price' => 850000, 'capacity' => 1, 'size' => 12, 'status' => 'available', 'description' => 'Kamar ukuran 3x4m'],
        ];

        $roomFacilities = Facility::where('type', 'room')->pluck('id')->toArray();

        $count = 0;
        foreach ($rooms as $roomData) {
            $room = Room::updateOrCreate(
                ['room_number' => $roomData['room_number']],
                $roomData                                  
            );
            $count++;
        
            if (!empty($roomFacilities)) {
                $room->facilities()->sync($roomFacilities);
            }
        }

        echo "✅ " . $count . " kamar berhasil di-sync (dibuat/diupdate)!\n";
    }
}