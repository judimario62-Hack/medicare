<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffAppointmentConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_confirm_pending_appointments_from_their_dashboard(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create();
        $appointment = Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'status' => 'scheduled',
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addMinutes(30),
        ]);

        $this->actingAs($staff)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Demandes à confirmer')
            ->assertSee(route('appointments.confirm', $appointment), false);

        $this->patch(route('appointments.confirm', $appointment))
            ->assertRedirect(route('appointments.index'));

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed',
        ]);

        $this->patch(route('appointments.confirm', $appointment))
            ->assertForbidden();
    }

    public function test_cancelled_appointments_are_not_shown_in_the_validation_queue(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $cancelledAppointment = Appointment::factory()->create(['status' => 'cancelled']);
        Appointment::factory()->create(['status' => 'scheduled']);

        $this->actingAs($staff)
            ->get(route('appointments.pending'))
            ->assertOk()
            ->assertSee('Demandes à valider')
            ->assertDontSee(route('appointments.show', $cancelledAppointment), false);
    }
}
