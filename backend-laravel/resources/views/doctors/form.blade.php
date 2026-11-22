@csrf
<div class="form-grid">
    <div class="field full">
        @if (!empty($doctor->avatar_path ?? null))
            <x-account-avatar :name="$doctor->user->name ?? 'Médecin'" :path="$doctor->avatar_path" :size="72" />
        @elseif (!empty($doctor->user->avatar_path ?? null))
            <x-account-avatar :name="$doctor->user->name ?? 'Médecin'" :path="$doctor->user->avatar_path" :size="72" />
        @endif
        <label for="avatar">Photo du médecin</label>
        <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp">
        @error('avatar') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="user_id">Compte utilisateur médecin</label>
        <select id="user_id" name="user_id">
            <option value="">Aucun compte associé</option>
            @foreach ($doctorUsers as $user)
                <option value="{{ $user->id }}" @selected((string) old('user_id', $doctor->user_id ?? '') === (string) $user->id)>{{ $user->name }} — {{ $user->email }}</option>
            @endforeach
        </select>
        @error('user_id') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="specialty">Spécialité</label>
        <input id="specialty" name="specialty" value="{{ old('specialty', $doctor->specialty ?? '') }}">
        @error('specialty') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="license_number">Numéro professionnel</label>
        <input id="license_number" name="license_number" value="{{ old('license_number', $doctor->license_number ?? '') }}">
        @error('license_number') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="phone">Téléphone</label>
        <input id="phone" name="phone" value="{{ old('phone', $doctor->phone ?? '') }}">
        @error('phone') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field full">
        <label class="checkbox-field">
            <input type="hidden" name="is_active" value="0">
            <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $doctor->is_active ?? true))>
            <span>Médecin actif</span>
        </label>
        @error('is_active') <p class="error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="actions">
    <button type="submit">{{ $submitLabel }}</button>
    <a class="button secondary" href="{{ route('doctors.index') }}">Annuler</a>
</div>
