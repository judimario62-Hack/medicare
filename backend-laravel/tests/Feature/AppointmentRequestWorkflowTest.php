<?php

namespace Tests\Feature;

use App\Models\Alert;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentRequestWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_edit_their_own_profile(): void
    {
        $patientUser = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $patient = Patient::query()->create([
            'user_id' => $patientUser->id,
            'first_name' => 'Jean',
            'last_name' => 'Patient',
        ]);

        $this->actingAs($patientUser)
            ->get(route('patient-record.edit'))
            ->assertOk()
            ->assertSee('Modifier le dossier');

        $this->actingAs($patientUser)
            ->put(route('patient-record.update'), [
                'first_name' => 'Jean-Pierre',
                'last_name' => 'Patient',
                'date_of_birth' => null,
                'phone' => '0123456789',
                'address' => 'Adresse de test',
            ])
            ->assertRedirect(route('patient-record.show'));

        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'first_name' => 'Jean-Pierre',
            'phone' => '0123456789',
        ]);
    }

    public function test_dashboard_links_respect_role_specific_access(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('href="'.route('appointments.index').'"', false)
            ->assertDontSee('href="'.route('appointments.agenda').'"', false)
            ->assertDontSee('href="'.route('patient-record.show').'"', false);

        $this->get(route('admin.index'))
            ->assertOk()
            ->assertSee('href="'.route('appointments.index').'"', false)
            ->assertDontSee('href="'.route('appointments.agenda').'"', false);

        $patient = User::factory()->create(['role' => User::ROLE_PATIENT]);
        Patient::factory()->create(['user_id' => $patient->id]);

        $this->actingAs($patient)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('href="'.route('patient-record.show').'"', false);

        $this->get(route('patient-record.show'))->assertOk();
    }

    public function test_patient_without_a_profile_can_open_the_appointment_request_form(): void
    {
        $patientUser = User::factory()->create(['role' => User::ROLE_PATIENT]);

        $this->actingAs($patientUser)
            ->get(route('appointments.create'))
            ->assertOk()
            ->assertSee('Demander un rendez-vous')
            ->assertSee('action="'.route('appointments.store').'"', false);

        $this->assertDatabaseHas('patients', ['user_id' => $patientUser->id]);
        $this->assertSame('/appointments', parse_url(route('appointments.store'), PHP_URL_PATH));
        $this->assertSame('/api/appointments', parse_url(route('api.appointments.store'), PHP_URL_PATH));
    }

    public function test_patient_request_staff_confirmation_and_consultation_completion_notify_the_right_users(): void
    {
        $patientUser = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $otherPatient = Patient::factory()->create();
        $doctor = Doctor::factory()->create();
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $room = Room::factory()->create(['is_active' => true]);
        $startsAt = now()->addDays(2)->setTime(10, 0);

        $response = $this->actingAs($patientUser)->postJson('/api/appointments', [
            'patient_id' => $otherPatient->id,
            'doctor_id' => $doctor->id,
            'room_id' => $room->id,
            'consultation_type' => 'clinic',
            'starts_at' => $startsAt->toDateTimeString(),
            'ends_at' => $startsAt->copy()->addMinutes(30)->toDateTimeString(),
            'status' => 'confirmed',
            'reason' => 'Demande synthétique de test',
        ]);

        $response->assertCreated();

        $patient = $patientUser->patient()->firstOrFail();
        $response->assertJsonPath('patient_id', $patient->id)
            ->assertJsonPath('status', 'pending');

        $appointment = Appointment::query()->findOrFail($response->json('id'));

        $this->assertDatabaseHas('alerts', [
            'user_id' => $patientUser->id,
            'appointment_id' => $appointment->id,
            'type' => 'appointment_pending',
        ]);
        $this->assertDatabaseHas('alerts', [
            'user_id' => $staff->id,
            'appointment_id' => $appointment->id,
            'type' => 'appointment_pending_staff',
        ]);

        Appointment::factory()->create(['patient_id' => $otherPatient->id]);

        $this->getJson('/api/appointments')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $appointment->id);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Demander un rendez-vous');

        $this->actingAs($staff)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Confirmer')
            ->assertSee(route('appointments.confirm', $appointment), false);

        $this->patch(route('appointments.confirm', $appointment))
            ->assertRedirect(route('appointments.index'));

        $this->assertDatabaseHas('alerts', [
            'user_id' => $patientUser->id,
            'appointment_id' => $appointment->id,
            'type' => 'appointment_confirmed_patient',
        ]);
        $this->assertDatabaseHas('alerts', [
            'user_id' => $staff->id,
            'appointment_id' => $appointment->id,
            'type' => 'appointment_confirmed_staff',
        ]);

        $this->actingAs($doctor->user)
            ->post(route('appointments.consultation.store', $appointment), [
                'subjective' => 'Évaluation synthétique de test',
                'assessment' => 'Consultation enregistrée',
            ])
            ->assertRedirect(route('appointments.consultation', $appointment));

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('alerts', [
            'user_id' => $patientUser->id,
            'appointment_id' => $appointment->id,
            'type' => 'appointment_completed_patient',
        ]);
        $this->assertDatabaseHas('alerts', [
            'user_id' => $staff->id,
            'appointment_id' => $appointment->id,
            'type' => 'appointment_completed_staff',
        ]);

        $this->actingAs($patientUser)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('En attente')
            ->assertSee('Confirmé')
            ->assertSee('Consultation terminée');

        $this->assertSame(
            0,
            Alert::query()->where('user_id', $patientUser->id)->whereNull('read_at')->count(),
        );

        $this->actingAs($staff)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('À valider')
            ->assertSee('Confirmé')
            ->assertSee('Consultation terminée');
    }
}
