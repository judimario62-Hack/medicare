<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPatientRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_always_creates_a_patient_account(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Patient Téléphone',
            'email' => 'patient.phone@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => User::ROLE_ADMIN,
        ]);

        $response->assertRedirect(route('dashboard'));

        $user = User::query()->where('email', 'patient.phone@example.test')->firstOrFail();

        $this->assertSame(User::ROLE_PATIENT, $user->role);
        $this->assertDatabaseHas('patients', ['user_id' => $user->id]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_page_does_not_offer_privileged_roles(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertDontSee('name="role"', false)
            ->assertDontSee('Administrateur', false)
            ->assertDontSee('Médecin / Doctor', false)
            ->assertDontSee('Secrétariat / Staff', false);
    }
}
