@csrf
<div class="form-grid">
    @if (!empty($room->image_path))
        <div class="field full">
            <img src="{{ asset('storage/'.$room->image_path) }}" alt="{{ $room->name }}" style="width:220px;height:150px;border-radius:16px;object-fit:cover;">
        </div>
    @endif
    <div class="field full">
        <label for="image">Photo de la salle</label>
        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp">
        @error('image') <p class="error">{{ $message }}</p> @enderror
    </div>
    <div class="field">
        <label for="name">Nom de la salle</label>
        <input id="name" name="name" value="{{ old('name', $room->name ?? '') }}" required>
        @error('name') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="location">Emplacement</label>
        <input id="location" name="location" value="{{ old('location', $room->location ?? '') }}">
        @error('location') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="capacity">Capacité</label>
        <input id="capacity" name="capacity" type="number" min="1" max="999" value="{{ old('capacity', $room->capacity ?? 1) }}" required>
        @error('capacity') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field full">
        <label class="checkbox-field">
            <input type="hidden" name="is_active" value="0">
            <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $room->is_active ?? true))>
            <span>Salle active</span>
        </label>
        @error('is_active') <p class="error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="actions">
    <button type="submit">{{ $submitLabel }}</button>
    <a class="button secondary" href="{{ route('rooms.index') }}">Annuler</a>
</div>
