<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\User;

class PatientProfileService
{
    public function ensureFor(User $user): Patient
    {
        if ($user->role !== User::ROLE_PATIENT) {
            throw new \InvalidArgumentException('Un dossier patient ne peut être créé que pour un compte patient.');
        }

        $nameParts = preg_split('/\s+/', trim($user->name), 2) ?: [];

        return Patient::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $nameParts[0] ?? 'Patient',
                'last_name' => $nameParts[1] ?? '',
                'phone' => null,
                'address' => null,
            ],
        );
    }
}
