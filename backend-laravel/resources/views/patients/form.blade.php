@csrf
<div class="form-grid">
    <div class="field full">
        @if (!empty($patient->avatar_path))
            <x-account-avatar :name="trim(($patient->first_name ?? '').' '.($patient->last_name ?? ''))" :path="$patient->avatar_path" :size="72" />
        @endif
        <label for="avatar">Photo du patient</label>
        <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp">
        @error('avatar') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="first_name">Prénom</label>
        <input id="first_name" name="first_name" value="{{ old('first_name', $patient->first_name ?? '') }}" required>
        @error('first_name') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="last_name">Nom</label>
        <input id="last_name" name="last_name" value="{{ old('last_name', $patient->last_name ?? '') }}" required>
        @error('last_name') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="date_of_birth">Date de naissance</label>
        <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth', isset($patient) && $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '') }}">
        @error('date_of_birth') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="phone">Téléphone</label>
        <input id="phone" name="phone" value="{{ old('phone', $patient->phone ?? '') }}">
        @error('phone') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field full">
        <label for="address">Adresse</label>
        <textarea id="address" name="address">{{ old('address', $patient->address ?? '') }}</textarea>
        @error('address') <p class="error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="actions">
    <button type="submit">{{ $submitLabel }}</button>
    <a class="button secondary" href="{{ auth()->user()->role === 'patient' ? route('patient-record.show') : route('patients.index') }}">Annuler</a>
</div>
