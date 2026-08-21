<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'room_id' => RoomFactory::new(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'id_card' => fake()->unique()->numerify('################'),
            'address' => fake()->address(),
            'entry_date' => now()->subMonths(3),
            'status' => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function forRoom(Room $room): static
    {
        return $this->state(fn (array $attributes) => [
            'room_id' => $room->id,
        ]);
    }
}
