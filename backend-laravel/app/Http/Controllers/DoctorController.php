<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Doctor::class);

        return view('doctors.index', [
            'doctors' => Doctor::query()->with('user')->orderBy('id')->paginate(15),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Doctor::class);

        return view('doctors.create', ['doctorUsers' => $this->doctorUsers()]);
    }

    public function store(StoreDoctorRequest $request): RedirectResponse
    {
        Gate::authorize('create', Doctor::class);

        $data = $request->validated();
        $avatar = $data['avatar'] ?? null;
        unset($data['avatar']);
        if ($avatar) {
            $data['avatar_path'] = $avatar->store('avatars/doctors', 'public');
        }

        $doctor = Doctor::query()->create($data);
        if ($avatar && $doctor->user) {
            if ($doctor->user->avatar_path) {
                Storage::disk('public')->delete($doctor->user->avatar_path);
            }
            $doctor->user->update(['avatar_path' => $doctor->avatar_path]);
        }

        return redirect()->route('doctors.show', $doctor)->with('status', 'Le médecin a été créé.');
    }

    public function show(Doctor $doctor): View
    {
        Gate::authorize('view', $doctor);

        return view('doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor): View
    {
        Gate::authorize('update', $doctor);

        return view('doctors.edit', ['doctor' => $doctor, 'doctorUsers' => $this->doctorUsers()]);
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        Gate::authorize('update', $doctor);

        $data = $request->validated();
        $avatar = $data['avatar'] ?? null;
        unset($data['avatar']);
        if ($avatar) {
            if ($doctor->avatar_path) {
                Storage::disk('public')->delete($doctor->avatar_path);
            }
            $data['avatar_path'] = $avatar->store('avatars/doctors', 'public');
        }

        $doctor->update($data);
        if ($avatar && $doctor->user) {
            if ($doctor->user->avatar_path) {
                Storage::disk('public')->delete($doctor->user->avatar_path);
            }
            $doctor->user->update(['avatar_path' => $doctor->avatar_path]);
        }

        return redirect()->route('doctors.show', $doctor)->with('status', 'Le médecin a été mis à jour.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        Gate::authorize('delete', $doctor);

        if ($doctor->avatar_path) {
            Storage::disk('public')->delete($doctor->avatar_path);
        }
        if ($doctor->user?->avatar_path) {
            Storage::disk('public')->delete($doctor->user->avatar_path);
        }
        $doctor->delete();

        return redirect()->route('doctors.index')->with('status', 'Le médecin a été supprimé.');
    }

    /** @return Collection<int, User> */
    private function doctorUsers()
    {
        return User::query()
            ->where('role', User::ROLE_DOCTOR)
            ->orderBy('name')
            ->get();
    }
}
