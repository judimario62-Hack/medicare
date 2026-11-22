<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            'admin' => User::ROLE_ADMIN,
            'doctor' => User::ROLE_DOCTOR,
            'staff' => User::ROLE_STAFF,
        ];

        foreach (config('services.bootstrap_accounts', []) as $accountType => $account) {
            if (! isset($roles[$accountType])
                || blank($account['name'] ?? null)
                || blank($account['email'] ?? null)
                || blank($account['password'] ?? null)) {
                continue;
            }

            $user = User::query()->firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($account['password']),
                    'role' => $roles[$accountType],
                ]
            );

            if ($roles[$accountType] === User::ROLE_DOCTOR) {
                Doctor::query()->firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'specialty' => 'Médecine générale',
                        'license_number' => 'DOC-ADMIN',
                        'phone' => null,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
