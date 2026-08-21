<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    private static int $sequence = 100;

    public function definition(): array
    {
        return [
            'room_number' => (string) self::$sequence++,
            'type' => fake()->randomElement(['singlenoac', 'singleac', 'shared']),
            'price' => fake()->randomElement([750000, 1000000, 1500000]),
            'status' => 'available',
            'description' => fake()->sentence(),
            'capacity' => 1,
        ];
    }

    public function occupied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'occupied',
        ]);
    }
}
