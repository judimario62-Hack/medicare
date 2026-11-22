<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'name' => 'Salle ' . fake()->unique()->randomLetter() . fake()->randomDigit(),
            'location' => fake()->randomElement(['Etage 1', 'Etage 2', 'Cabinet A', 'Cabinet B']),
            'capacity' => fake()->numberBetween(1, 8),
            'is_active' => true,
        ];
    }
}
