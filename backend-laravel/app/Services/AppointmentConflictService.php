<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class AppointmentConflictService
{
    /** @var array<int, string> */
    private const BLOCKING_STATUSES = ['scheduled', 'pending', 'confirmed', 'checked_in'];

    /** @param array<string, mixed> $data */
    public function ensureAvailable(array $data, ?Appointment $ignoredAppointment = null): void
    {
        if (! in_array($data['status'], self::BLOCKING_STATUSES, true)) {
            return;
        }

        $errors = [];

        if ($this->overlappingAppointments($data, $ignoredAppointment)
            ->where('doctor_id', $data['doctor_id'])
            ->exists()) {
            $errors['starts_at'] = 'Ce médecin a déjà un rendez-vous sur ce créneau.';
        }

        if ($data['room_id'] !== null && $this->overlappingAppointments($data, $ignoredAppointment)
            ->where('room_id', $data['room_id'])
            ->exists()) {
            $errors['room_id'] = 'Cette salle est déjà réservée sur ce créneau.';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    /** @param array<string, mixed> $data */
    private function overlappingAppointments(array $data, ?Appointment $ignoredAppointment): Builder
    {
        return Appointment::query()
            ->whereIn('status', self::BLOCKING_STATUSES)
            ->where('starts_at', '<', $data['ends_at'])
            ->where('ends_at', '>', $data['starts_at'])
            ->when($ignoredAppointment, fn (Builder $query) => $query->whereKeyNot($ignoredAppointment->getKey()));
    }
}
