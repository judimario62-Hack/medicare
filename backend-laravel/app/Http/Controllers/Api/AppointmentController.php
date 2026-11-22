<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\User;
use App\Services\AppointmentConflictService;
use App\Services\AppointmentNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AppointmentController extends Controller
{
    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', Appointment::class);

        $user = Auth::user();

        $appointments = Appointment::query()
            ->with(['patient', 'doctor.user', 'room'])
            ->when(
                $user?->role === User::ROLE_DOCTOR,
                fn ($query) => $query->whereHas('doctor', fn ($doctorQuery) => $doctorQuery->where('user_id', $user?->id)),
            )
            ->when(
                $user?->role === User::ROLE_PATIENT,
                fn ($query) => $query->whereHas('patient', fn ($patientQuery) => $patientQuery->where('user_id', $user?->id)),
            )
            ->orderBy('starts_at')
            ->get();

        return response()->json($appointments);
    }

    public function store(
        StoreAppointmentRequest $request,
        AppointmentConflictService $conflicts,
        AppointmentNotificationService $notifications,
    ): JsonResponse
    {
        Gate::authorize('create', Appointment::class);

        $data = $request->validated();
        $data['consultation_type'] = $data['consultation_type'] ?? 'clinic';
        if ($data['consultation_type'] === 'home') {
            $data['room_id'] = null;
        }

        $data['status'] = $data['status'] ?? 'scheduled';
        $conflicts->ensureAvailable($data);

        $appointment = Appointment::query()->create($data);
        $notifications->created($appointment);
        $appointment->load(['patient', 'doctor.user', 'room']);

        return response()->json($appointment, 201);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        Gate::authorize('view', $appointment);

        $appointment->load(['patient', 'doctor.user', 'room']);

        return response()->json($appointment);
    }

    public function update(
        UpdateAppointmentRequest $request,
        Appointment $appointment,
        AppointmentConflictService $conflicts,
        AppointmentNotificationService $notifications,
    ): JsonResponse {
        Gate::authorize('update', $appointment);

        $data = $request->validated();
        $data['consultation_type'] = $data['consultation_type'] ?? ($appointment->consultation_type ?? 'clinic');
        if ($data['consultation_type'] === 'home') {
            $data['room_id'] = null;
        }

        $conflicts->ensureAvailable($data, $appointment);

        $previousStatus = $appointment->status;
        $appointment->update($data);
        $notifications->statusChanged($appointment, $previousStatus);
        $appointment->load(['patient', 'doctor.user', 'room']);

        return response()->json($appointment);
    }
}
