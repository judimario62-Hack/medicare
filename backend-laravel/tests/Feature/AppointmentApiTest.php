<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_appointments(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        /** @var Patient $patient */
        $patient = Patient::factory()->create();
        /** @var Doctor $doctor */
        $doctor = Doctor::factory()->create();
        /** @var Room $room */
        $room = Room::factory()->create();

        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'room_id' => $room->id,
        ]);

        $response = $this->actingAs($admin, 'web')->getJson('/api/appointments');

        $response->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'patient_id',
                    'doctor_id',
                    'room_id',
                    'starts_at',
                    'ends_at',
                    'status',
                    'reason',
                ],
            ]);
    }

    public function test_admin_can_create_appointment(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        /** @var Patient $patient */
        $patient = Patient::factory()->create();
        /** @var Doctor $doctor */
        $doctor = Doctor::factory()->create(['is_active' => true]);
        /** @var Room $room */
        $room = Room::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin, 'web')->postJson('/api/appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'room_id' => $room->id,
            'starts_at' => now()->addDay()->setTime(9, 0)->toDateTimeString(),
            'ends_at' => now()->addDay()->setTime(9, 30)->toDateTimeString(),
            'status' => 'scheduled',
            'reason' => 'Consultation de suivi',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('appointments', ['reason' => 'Consultation de suivi']);
    }

    public function test_doctor_can_view_his_appointments_only(): void
    {
        /** @var User $doctor */
        $doctor = User::factory()->create(['role' => User::ROLE_DOCTOR]);
        /** @var Doctor $doctorProfile */
        $doctorProfile = Doctor::factory()->create(['user_id' => $doctor->id, 'is_active' => true]);
        /** @var Doctor $otherDoctor */
        $otherDoctor = Doctor::factory()->create(['is_active' => true]);
        /** @var Patient $patient */
        $patient = Patient::factory()->create();

        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctorProfile->id,
            'status' => 'scheduled',
        ]);

        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $otherDoctor->id,
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($doctor, 'web')->getJson('/api/appointments');

        $response->assertOk();
        $payload = $response->json();

        $this->assertCount(1, $payload);
        $this->assertSame($doctorProfile->id, $payload[0]['doctor_id']);
    }
}
