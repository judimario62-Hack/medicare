<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('+1 day', '+10 days');
        $endsAt = (clone $startsAt)->modify('+30 minutes');

        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
            'room_id' => Room::factory(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => fake()->randomElement(['scheduled', 'confirmed', 'checked_in', 'completed', 'cancelled', 'no_show']),
            'reason' => fake()->sentence(),
        ];
    }
}
