<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Alert;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Room;
use App\Models\User;
use App\Models\VitalSign;
use App\Services\AppointmentConflictService;
use App\Services\AppointmentNotificationService;
use App\Services\PatientProfileService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(): View
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
            ->paginate(20);

        $pageTitle = $user?->role === User::ROLE_PATIENT ? 'Mes rendez-vous' : 'Gestion des rendez-vous';
        $pageSubtitle = $user?->role === User::ROLE_PATIENT ? 'Suivez vos demandes et confirmations.' : 'Planification et suivi du cabinet.';

        return view('appointments.index', compact('appointments', 'pageTitle', 'pageSubtitle'));
    }

    public function create(PatientProfileService $patientProfiles): View
    {
        $user = Auth::user();

        if ($user?->role === User::ROLE_PATIENT) {
            $patientProfiles->ensureFor($user);
        }

        Gate::authorize('create', Appointment::class);

        return view('appointments.create', $this->formData());
    }

    public function store(
        StoreAppointmentRequest $request,
        AppointmentConflictService $conflicts,
        AppointmentNotificationService $notifications,
    ): RedirectResponse
    {
        Gate::authorize('create', Appointment::class);

        $data = $request->validated();
        $data['consultation_type'] = $data['consultation_type'] ?? 'clinic';
        if ($data['consultation_type'] === 'home') {
            $data['room_id'] = null;
            if (empty($data['home_address'])) {
                $patientRecord = Patient::query()->find($data['patient_id']);
                $data['home_address'] = $patientRecord?->address;
            }
        }

        $data['status'] = $data['status'] ?? 'scheduled';
        $conflicts->ensureAvailable($data);

        $appointment = Appointment::query()->create($data);
        $notifications->created($appointment);

        return redirect()->route('appointments.show', $appointment)->with('status', 'Le rendez-vous a été créé et est en attente de confirmation.');
    }

    public function show(Appointment $appointment): View
    {
        Gate::authorize('view', $appointment);

        $appointment->load(['patient', 'doctor.user', 'room']);

        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment): View
    {
        Gate::authorize('update', $appointment);

        return view('appointments.edit', array_merge(['appointment' => $appointment], $this->formData()));
    }

    public function update(
        UpdateAppointmentRequest $request,
        Appointment $appointment,
        AppointmentConflictService $conflicts,
        AppointmentNotificationService $notifications,
    ): RedirectResponse {
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

        return redirect()->route('appointments.show', $appointment)->with('status', 'Le rendez-vous a été mis à jour.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        Gate::authorize('delete', $appointment);

        $patientUserId = $appointment->patient?->user_id;
        $statusBeforeDelete = $appointment->status;

        $appointment->delete();

        if ($patientUserId) {
            Alert::query()->create([
                'user_id' => $patientUserId,
                'patient_id' => $appointment->patient_id,
                'appointment_id' => null,
                'type' => 'appointment_cancelled',
                'title' => 'Rendez-vous annulé',
                'message' => 'Votre rendez-vous a été annulé ou refusé par le secrétariat. Merci de contacter le cabinet pour reprogrammer.',
                'scheduled_at' => now(),
            ]);
        }

        return redirect()->route('appointments.index')->with('status', 'Le rendez-vous a été supprimé.');
    }

    public function confirm(Appointment $appointment, AppointmentNotificationService $notifications): RedirectResponse
    {
        Gate::authorize('confirm', $appointment);

        $previousStatus = $appointment->status;
        $appointment->update(['status' => 'confirmed']);
        $notifications->statusChanged($appointment, $previousStatus);

        return redirect()->route('appointments.index')->with('status', 'Le rendez-vous a été confirmé.');
    }

    public function reject(Appointment $appointment, AppointmentNotificationService $notifications): RedirectResponse
    {
        Gate::authorize('confirm', $appointment);

        $previousStatus = $appointment->status;
        $appointment->update(['status' => 'cancelled']);
        $notifications->statusChanged($appointment, $previousStatus);

        return redirect()->route('appointments.index')->with('status', 'Le rendez-vous a été refusé.');
    }

    public function agenda(): View
    {
        Gate::authorize('viewAgenda', Appointment::class);

        $doctor = Auth::user()?->doctor;

        $appointments = Appointment::query()
            ->with(['patient', 'doctor.user', 'room'])
            ->when($doctor, fn ($query) => $query->where('doctor_id', $doctor->id))
            ->orderBy('starts_at')
            ->get();

        return view('appointments.agenda', compact('appointments'));
    }

    public function consultation(Appointment $appointment): View
    {
        Gate::authorize('consult', $appointment);

        $appointment->load(['patient', 'doctor.user', 'room', 'consultation.vitalSigns']);

        return view('appointments.consultation', compact('appointment'));
    }

    public function storeConsultation(
        Request $request,
        Appointment $appointment,
        AppointmentNotificationService $notifications,
    ): RedirectResponse {
        Gate::authorize('consult', $appointment);

        $validated = $request->validate([
            'subjective' => ['nullable', 'string'],
            'objective' => ['nullable', 'string'],
            'assessment' => ['nullable', 'string'],
            'plan' => ['nullable', 'string'],
            'vital_signs' => ['nullable', 'array'],
            'vital_signs.*.type' => ['required_with:vital_signs', 'string'],
            'vital_signs.*.value' => ['required_with:vital_signs', 'string'],
            'vital_signs.*.unit' => ['nullable', 'string'],
            'vital_signs.*.notes' => ['nullable', 'string'],
        ]);

        $consultation = Consultation::query()->updateOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'subjective' => $validated['subjective'] ?? null,
                'objective' => $validated['objective'] ?? null,
                'assessment' => $validated['assessment'] ?? null,
                'plan' => $validated['plan'] ?? null,
            ]
        );

        if (! empty($validated['vital_signs'])) {
            foreach ($validated['vital_signs'] as $vital) {
                VitalSign::query()->create([
                    'patient_id' => $appointment->patient_id,
                    'consultation_id' => $consultation->id,
                    'recorded_by' => Auth::id(),
                    'type' => $vital['type'],
                    'value' => $vital['value'],
                    'unit' => $vital['unit'] ?? null,
                    'notes' => $vital['notes'] ?? null,
                    'measured_at' => now(),
                ]);
            }
        }

        $previousStatus = $appointment->status;
        $appointment->update(['status' => 'completed']);
        $notifications->statusChanged($appointment, $previousStatus);

        return redirect()->route('appointments.consultation', $appointment)->with('status', 'La consultation a été enregistrée.');
    }

    public function prescriptionPdf(Appointment $appointment)
    {
        Gate::authorize('consult', $appointment);

        $appointment->load(['patient', 'doctor.user', 'consultation']);

        $data = [
            'patient' => $appointment->patient,
            'doctor' => $appointment->doctor?->user,
            'appointment' => $appointment,
            'summary' => $appointment->consultation?->assessment ?? 'Consultation médicale.',
            'plan' => $appointment->consultation?->plan ?? 'Suivi médical à prévoir.',
        ];

        $pdf = Pdf::loadView('appointments.prescription-pdf', $data);

        return $pdf->download('ordonnance-' . $appointment->id . '.pdf');
    }

    /** @return array<string, \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>> */
    private function formData(): array
    {
        $user = Auth::user();

        return [
            'patients' => Patient::query()->orderBy('last_name')->orderBy('first_name')->get(),
            'doctors' => Doctor::query()->with('user')->where('is_active', true)->orderBy('id')->get(),
            'rooms' => Room::query()->where('is_active', true)->orderBy('name')->get(),
            'currentPatient' => $user?->role === User::ROLE_PATIENT ? $user->patient : null,
        ];
    }

    public function pendingForValidation(): View
    {
        Gate::authorize('viewAny', Appointment::class);

        $appointments = Appointment::query()
            ->with(['patient', 'doctor.user', 'room'])
            ->whereIn('status', ['scheduled', 'pending'])
            ->orderBy('starts_at')
            ->paginate(20);

        return view('appointments.pending', compact('appointments'));
    }
}
