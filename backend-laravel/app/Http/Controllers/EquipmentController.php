<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use App\Models\Equipment;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    public function index(): View
    {
        return view('equipment.index', [
            'equipment' => Equipment::query()->with('room')->orderBy('name')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('equipment.create', ['rooms' => Room::query()->orderBy('name')->get()]);
    }

    public function store(StoreEquipmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $image = $data['image'] ?? null;
        unset($data['image']);
        if ($image) {
            $data['image_path'] = $image->store('equipment', 'public');
        }
        $equipment = Equipment::query()->create($data);

        return redirect()->route('equipment.show', $equipment)->with('status', 'L’équipement a été ajouté.');
    }

    public function show(Equipment $equipment): View
    {
        return view('equipment.show', ['equipment' => $equipment->load('room')]);
    }

    public function edit(Equipment $equipment): View
    {
        return view('equipment.edit', [
            'equipment' => $equipment,
            'rooms' => Room::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateEquipmentRequest $request, Equipment $equipment): RedirectResponse
    {
        $data = $request->validated();
        $image = $data['image'] ?? null;
        unset($data['image']);
        if ($image) {
            if ($equipment->image_path) {
                Storage::disk('public')->delete($equipment->image_path);
            }
            $data['image_path'] = $image->store('equipment', 'public');
        }
        $equipment->update($data);

        return redirect()->route('equipment.show', $equipment)->with('status', 'L’équipement a été mis à jour.');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        if ($equipment->image_path) {
            Storage::disk('public')->delete($equipment->image_path);
        }
        $equipment->delete();

        return redirect()->route('equipment.index')->with('status', 'L’équipement a été supprimé.');
    }
}
