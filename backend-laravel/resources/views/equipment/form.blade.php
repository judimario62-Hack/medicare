@csrf
<div class="form-grid">
    @if (!empty($equipment->image_path))
        <div class="field full">
            <img src="{{ asset('storage/'.$equipment->image_path) }}" alt="{{ $equipment->name }}" style="width:220px;height:220px;border-radius:16px;object-fit:cover;">
        </div>
    @endif
    <div class="field"><label for="name">Nom</label><input id="name" name="name" value="{{ old('name', $equipment->name ?? '') }}" required></div>
    <div class="field"><label for="category">Catégorie</label><input id="category" name="category" value="{{ old('category', $equipment->category ?? '') }}" placeholder="Tensiomètre, stéthoscope..."></div>
    <div class="field"><label for="serial_number">Numéro de série</label><input id="serial_number" name="serial_number" value="{{ old('serial_number', $equipment->serial_number ?? '') }}"></div>
    <div class="field"><label for="room_id">Salle</label><select id="room_id" name="room_id"><option value="">Aucune salle</option>@foreach ($rooms as $room)<option value="{{ $room->id }}" @selected((string) old('room_id', $equipment->room_id ?? '') === (string) $room->id)>{{ $room->name }}</option>@endforeach</select></div>
    <div class="field"><label for="status">Statut</label><select id="status" name="status">@foreach(['available'=>'Disponible','in_use'=>'En utilisation','maintenance'=>'Maintenance','out_of_service'=>'Hors service'] as $value=>$label)<option value="{{ $value }}" @selected(old('status', $equipment->status ?? 'available') === $value)>{{ $label }}</option>@endforeach</select></div>
    <div class="field"><label for="image">Photo de l’équipement</label><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp">@error('image')<p class="error">{{ $message }}</p>@enderror</div>
    <div class="field full"><label for="notes">Notes</label><textarea id="notes" name="notes">{{ old('notes', $equipment->notes ?? '') }}</textarea></div>
</div>
<div class="actions"><button type="submit">{{ $submitLabel }}</button><a class="button secondary" href="{{ route('equipment.index') }}">Annuler</a></div>
