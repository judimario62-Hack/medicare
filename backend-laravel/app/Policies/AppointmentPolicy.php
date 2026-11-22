<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_DOCTOR, User::ROLE_STAFF, User::ROLE_PATIENT], true);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_STAFF], true)
            || ($user->role === User::ROLE_DOCTOR && $appointment->doctor?->user_id === $user->id)
            || ($user->role === User::ROLE_PATIENT && $appointment->patient?->user_id === $user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_STAFF], true)
            || ($user->role === User::ROLE_PATIENT && $user->patient()->exists());
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_STAFF], true);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_STAFF], true);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Appointment $appointment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Appointment $appointment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can confirm the appointment.
     */
    public function confirm(User $user, Appointment $appointment): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_STAFF], true)
            && in_array($appointment->status, ['scheduled', 'pending'], true);
    }

    /**
     * Determine whether the user can view the agenda.
     */
    public function viewAgenda(User $user): bool
    {
        return $user->role === User::ROLE_DOCTOR;
    }

    /**
     * Determine whether the user can consult the appointment.
     */
    public function consult(User $user, Appointment $appointment): bool
    {
        return $user->role === User::ROLE_DOCTOR && $appointment->doctor?->user_id === $user->id;
    }
}
