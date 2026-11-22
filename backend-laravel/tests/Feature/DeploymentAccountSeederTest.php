<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeploymentAccountSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_configured_staff_accounts_are_created_idempotently(): void
    {
        config([
            'services.bootstrap_accounts' => [
                'admin' => [
                    'name' => 'Admin Demo',
                    'email' => 'admin@example.test',
                    'password' => 'AdminSecret123!',
                ],
                'doctor' => [
                    'name' => 'Doctor Demo',
                    'email' => 'doctor@example.test',
                    'password' => 'DoctorSecret123!',
                ],
                'staff' => [
                    'name' => 'Staff Demo',
                    'email' => 'staff@example.test',
                    'password' => 'StaffSecret123!',
                ],
            ],
        ]);

        app(DatabaseSeeder::class)->run();
        app(DatabaseSeeder::class)->run();

        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.test',
            'role' => User::ROLE_ADMIN,
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'staff@example.test',
            'role' => User::ROLE_STAFF,
        ]);

        $doctorUser = User::query()->where('email', 'doctor@example.test')->firstOrFail();
        $this->assertSame(User::ROLE_DOCTOR, $doctorUser->role);
        $this->assertTrue(Hash::check('DoctorSecret123!', $doctorUser->password));
        $this->assertDatabaseHas('doctors', ['user_id' => $doctorUser->id]);
        $this->assertSame(1, Doctor::query()->count());
    }
}
