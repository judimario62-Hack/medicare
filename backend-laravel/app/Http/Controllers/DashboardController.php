<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Room;
use App\Models\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $role = Auth::user()?->role;

        if ($role === 'doctor') {
            return $this->doctor();
        }

        if ($role === 'patient') {
            return $this->patient();
        }

        if ($role === 'staff') {
            return $this->staff();
        }

        return $this->adminOverview();
    }

    public function doctor(): View
    {
        return view('dashboard.doctor');
    }

    public function patient(): View
    {
        $user = Auth::user();
        $patient = $user?->patient;
        $appointments = $patient
            ? Appointment::query()
                ->with('doctor.user')
                ->where('patient_id', $patient->id)
                ->whereIn('status', ['scheduled', 'pending', 'confirmed'])
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->limit(5)
                ->get()
            : collect();

        return view('dashboard.patient', [
            'appointments' => $appointments,
            'appointmentsTotal' => $patient?->appointments()->count() ?? 0,
            'completedAppointments' => $patient?->appointments()->where('status', 'completed')->count() ?? 0,
            'unreadAlerts' => Alert::query()->where('user_id', $user?->id)->whereNull('read_at')->count(),
        ]);
    }

    public function staff(): View
    {
        $pendingStatuses = ['scheduled', 'pending'];

        $pendingAppointments = Appointment::query()
            ->with(['patient', 'doctor.user'])
            ->whereIn('status', $pendingStatuses)
            ->orderBy('starts_at')
            ->limit(5)
            ->get();

        return view('dashboard.staff', [
            'appointmentsToday' => Appointment::query()->whereDate('starts_at', today())->count(),
            'patientsTotal' => Patient::query()->count(),
            'pendingCount' => Appointment::query()->whereIn('status', $pendingStatuses)->count(),
            'pendingAppointments' => $pendingAppointments,
            'unreadAlerts' => Alert::query()->where('user_id', Auth::id())->whereNull('read_at')->count(),
        ]);
    }

    public function adminOverview(): View
    {
        $period = request()->query('period', '30d');

        $upcomingAppointments = Appointment::query()
            ->with(['patient', 'doctor.user'])
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(3)
            ->get()
            ->map(function ($appointment) {
                return (object) [
                    'title' => trim(($appointment->patient?->first_name ?? '') . ' ' . ($appointment->patient?->last_name ?? '')),
                    'date' => $appointment->starts_at,
                    'status' => $appointment->status,
                ];
            });

        $recentConsultations = Appointment::query()
            ->with(['patient', 'doctor.user', 'consultation'])
            ->whereNotNull('starts_at')
            ->orderByDesc('starts_at')
            ->limit(3)
            ->get()
            ->map(function ($appointment) {
                return (object) [
                    'patient' => (object) ['name' => trim(($appointment->patient?->first_name ?? '') . ' ' . ($appointment->patient?->last_name ?? ''))],
                    'date' => $appointment->starts_at,
                    'status' => $appointment->status === 'completed' ? 'confirmed' : ($appointment->status === 'scheduled' ? 'pending' : 'review'),
                ];
            });

        return view('dashboard.index', [
            'stats' => [
                'appointments_today' => Appointment::query()->whereDate('starts_at', today())->count(),
                'patients_total' => Patient::query()->count(),
                'doctors_total' => Doctor::query()->count(),
                'rooms_total' => Room::query()->count(),
            ],
            'upcomingAppointments' => $upcomingAppointments,
            'recentConsultations' => $recentConsultations,
            'rooms' => Room::query()->where('is_active', true)->orderBy('name')->limit(6)->get(),
            'equipment' => Equipment::query()->with('room')->orderBy('name')->limit(8)->get(),
        ]);
    }

    public function secretariat(): View
    {
        $appointments = Appointment::query()
            ->with(['patient', 'doctor.user', 'room'])
            ->orderBy('starts_at')
            ->limit(8)
            ->get();

        $toConfirm = Appointment::query()
            ->with(['patient', 'doctor.user'])
            ->whereIn('status', ['scheduled', 'pending'])
            ->orderBy('starts_at')
            ->limit(5)
            ->get();

        $tasks = [
            ['title' => 'Confirmer les consultations du matin', 'time' => '08:30', 'priority' => 'Haute'],
            ['title' => 'Rappeler les patients sans confirmation', 'time' => '10:00', 'priority' => 'Moyenne'],
            ['title' => 'Vérifier les dossiers à transmettre', 'time' => '12:15', 'priority' => 'Urgente'],
            ['title' => 'Mettre à jour les visites de suivi', 'time' => '15:45', 'priority' => 'Moyenne'],
        ];

        $calls = [
            ['name' => 'Sophie Martin', 'reason' => 'Confirmation rendez-vous', 'time' => '09:15'],
            ['name' => 'Jean Dupont', 'reason' => 'Suivi administratif', 'time' => '11:00'],
            ['name' => 'Maya Ndayisenga', 'reason' => 'Rappel dossier', 'time' => '14:30'],
        ];

        return view('dashboard.secretariat', compact('appointments', 'toConfirm', 'tasks', 'calls'));
    }

    public function notifications(): View
    {
        $alerts = Alert::query()
            ->with(['patient', 'appointment'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        Alert::query()
            ->where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('dashboard.notifications', compact('alerts'));
    }
}
