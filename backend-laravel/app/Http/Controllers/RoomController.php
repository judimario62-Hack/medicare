<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Room::class);

        return view('rooms.index', ['rooms' => Room::query()->orderBy('name')->paginate(15)]);
    }

    public function create(): View
    {
        Gate::authorize('create', Room::class);

        return view('rooms.create');
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        Gate::authorize('create', Room::class);

        $data = $request->validated();
        $image = $data['image'] ?? null;
        unset($data['image']);
        if ($image) {
            $data['image_path'] = $image->store('rooms', 'public');
        }

        $room = Room::query()->create($data);

        return redirect()->route('rooms.show', $room)->with('status', 'La salle a été créée.');
    }

    public function show(Room $room): View
    {
        Gate::authorize('view', $room);

        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        Gate::authorize('update', $room);

        return view('rooms.edit', compact('room'));
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        Gate::authorize('update', $room);

        $data = $request->validated();
        $image = $data['image'] ?? null;
        unset($data['image']);
        if ($image) {
            if ($room->image_path) {
                Storage::disk('public')->delete($room->image_path);
            }
            $data['image_path'] = $image->store('rooms', 'public');
        }

        $room->update($data);

        return redirect()->route('rooms.show', $room)->with('status', 'La salle a été mise à jour.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        Gate::authorize('delete', $room);

        if ($room->image_path) {
            Storage::disk('public')->delete($room->image_path);
        }
        $room->delete();

        return redirect()->route('rooms.index')->with('status', 'La salle a été supprimée.');
    }
}
