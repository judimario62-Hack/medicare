<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Appointment;
use App\Models\User;

class AppointmentNotificationService
{
    public function created(Appointment $appointment): void
    {
        $this->notifyForCurrentStatus($appointment);
    }

    public function statusChanged(Appointment $appointment, string $previousStatus): void
    {
        if ($previousStatus !== $appointment->status) {
            $this->notifyForCurrentStatus($appointment);
        }
    }

    private function notifyForCurrentStatus(Appointment $appointment): void
    {
        $patientName = trim(($appointment->patient?->first_name ?? '').' '.($appointment->patient?->last_name ?? ''));
        $doctorUserId = $appointment->doctor?->user_id;
        $patientUserId = $appointment->patient?->user_id;

        if (in_array($appointment->status, ['scheduled', 'pending'], true)) {
            $this->notify(
                $doctorUserId,
                $appointment,
                'appointment_booked',
                'Nouvelle demande de rendez-vous',
                'Une demande de rendez-vous est enregistrée pour '.$patientName.'. Elle attend la validation du secrétariat.',
            );
            $this->notify(
                $patientUserId,
                $appointment,
                'appointment_pending',
                'Demande de rendez-vous en attente',
                'Votre demande a été transmise au secrétariat. Vous recevrez une notification après sa validation.',
            );
            $this->notifyManagers(
                $appointment,
                'appointment_pending_staff',
                'Rendez-vous à confirmer',
                'Une demande de rendez-vous de '.$patientName.' attend votre validation.',
            );

            return;
        }

        if ($appointment->status === 'confirmed') {
            $this->notify(
                $doctorUserId,
                $appointment,
                'appointment_confirmed',
                'Rendez-vous confirmé',
                'Le rendez-vous de '.$patientName.' a été confirmé par le secrétariat.',
            );
            $this->notify(
                $patientUserId,
                $appointment,
                'appointment_confirmed_patient',
                'Confirmation reçue',
                'Votre rendez-vous a été confirmé. Merci de vous présenter à l’heure prévue.',
            );
            $this->notifyManagers(
                $appointment,
                'appointment_confirmed_staff',
                'Rendez-vous confirmé',
                'Le rendez-vous de '.$patientName.' a été confirmé.',
            );

            return;
        }

        if ($appointment->status === 'cancelled') {
            $this->notify(
                $doctorUserId,
                $appointment,
                'appointment_rejected',
                'Rendez-vous refusé',
                'La demande de rendez-vous de '.$patientName.' a été refusée.',
            );
            $this->notify(
                $patientUserId,
                $appointment,
                'appointment_rejected_patient',
                'Rendez-vous refusé',
                'Votre demande a été refusée. Vous pouvez demander un autre créneau ou contacter le cabinet.',
            );
            $this->notifyManagers(
                $appointment,
                'appointment_rejected_staff',
                'Demande refusée',
                'La demande de rendez-vous de '.$patientName.' a été refusée.',
            );

            return;
        }

        if ($appointment->status === 'completed') {
            $this->notify(
                $patientUserId,
                $appointment,
                'appointment_completed_patient',
                'Consultation terminée',
                'Votre consultation est terminée. Consultez votre espace patient pour retrouver vos rendez-vous.',
            );
            $this->notifyManagers(
                $appointment,
                'appointment_completed_staff',
                'Consultation terminée',
                'La consultation de '.$patientName.' est terminée.',
            );
        }
    }

    private function notifyManagers(Appointment $appointment, string $type, string $title, string $message): void
    {
        $managerIds = User::query()
            ->whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])
            ->pluck('id');

        foreach ($managerIds as $managerId) {
            $this->notify($managerId, $appointment, $type, $title, $message);
        }
    }

    private function notify(?int $userId, Appointment $appointment, string $type, string $title, string $message): void
    {
        if ($userId === null) {
            return;
        }

        Alert::query()->create([
            'user_id' => $userId,
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'scheduled_at' => now(),
        ]);
    }
}
