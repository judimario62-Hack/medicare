<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Patient::class);

        return view('patients.index', [
            'patients' => Patient::query()
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Patient::class);

        return view('patients.create');
    }

    public function store(StorePatientRequest $request): RedirectResponse
    {
        Gate::authorize('create', Patient::class);

        $data = $request->validated();
        $avatar = $data['avatar'] ?? null;
        unset($data['avatar']);
        if ($avatar) {
            $data['avatar_path'] = $avatar->store('avatars/patients', 'public');
        }

        $patient = Patient::query()->create($data);

        return redirect()
            ->route('patients.show', $patient)
            ->with('status', 'Le dossier patient a été créé.');
    }

    public function show(Patient $patient): View
    {
        Gate::authorize('view', $patient);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        Gate::authorize('update', $patient);

        return view('patients.edit', compact('patient'));
    }

    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        Gate::authorize('update', $patient);

        $data = $request->validated();
        $avatar = $data['avatar'] ?? null;
        unset($data['avatar']);
        if ($avatar) {
            if ($patient->avatar_path) {
                Storage::disk('public')->delete($patient->avatar_path);
            }
            $data['avatar_path'] = $avatar->store('avatars/patients', 'public');
        }

        $patient->update($data);

        return redirect()
            ->route('patients.show', $patient)
            ->with('status', 'Le dossier patient a été mis à jour.');
    }

    public function myRecord(): View
    {
        $patient = Patient::query()
            ->where('user_id', Auth::id())
            ->firstOrFail();

        Gate::authorize('view', $patient);

        return view('patients.show', compact('patient'));
    }

    public function editMyRecord(): View
    {
        $patient = Patient::query()
            ->where('user_id', Auth::id())
            ->firstOrFail();

        Gate::authorize('update', $patient);

        return view('patients.edit', compact('patient'));
    }

    public function updateMyRecord(UpdatePatientRequest $request): RedirectResponse
    {
        $patient = Patient::query()
            ->where('user_id', Auth::id())
            ->firstOrFail();

        Gate::authorize('update', $patient);

        $data = $request->validated();
        $avatar = $data['avatar'] ?? null;
        unset($data['avatar']);

        if ($avatar) {
            if ($patient->avatar_path) {
                Storage::disk('public')->delete($patient->avatar_path);
            }
            $data['avatar_path'] = $avatar->store('avatars/patients', 'public');
        }

        $patient->update($data);

        return redirect()
            ->route('patient-record.show')
            ->with('status', 'Votre dossier patient a été mis à jour.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        Gate::authorize('delete', $patient);

        if ($patient->avatar_path) {
            Storage::disk('public')->delete($patient->avatar_path);
        }
        if ($patient->user?->avatar_path) {
            Storage::disk('public')->delete($patient->user->avatar_path);
        }
        $patient->delete();

        return redirect()->route('patients.index')->with('status', 'Le dossier patient a été supprimé.');
    }

    public function medicalFile(Patient $patient): View
    {
        Gate::authorize('view', $patient);

        $patient->load(['appointments.doctor.user', 'appointments.consultation.vitalSigns']);

        return view('patients.medical-file', compact('patient'));
    }
}
