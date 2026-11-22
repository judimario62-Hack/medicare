@csrf

@php
    $isPatient = Auth::user()?->role === 'patient';
    $selectedType = old('consultation_type', $appointment->consultation_type ?? 'clinic');
    $patientId = old('patient_id', $appointment->patient_id ?? ($currentPatient?->id ?? ''));
    $patientAddress = old('home_address', $appointment->home_address ?? ($currentPatient?->address ?? ''));
@endphp

<div class="form-grid">
    {{-- Patient --}}
    @if ($isPatient && $currentPatient)
        <div class="field full">
            <label>Patient</label>
            <div class="patient-badge">
                <span class="patient-icon">👤</span>
                <div>
                    <strong>{{ $currentPatient->first_name }} {{ $currentPatient->last_name }}</strong>
                    <span class="patient-sub">{{ $currentPatient->phone ?? 'Aucun téléphone renseigné' }}</span>
                </div>
            </div>
            <input type="hidden" name="patient_id" value="{{ $currentPatient->id }}">
        </div>
    @else
        <div class="field">
            <label for="patient_id">Patient <span class="text-danger">*</span></label>
            <select id="patient_id" name="patient_id" required>
                <option value="">Sélectionnez un patient</option>
                @foreach ($patients as $p)
                    <option value="{{ $p->id }}" data-address="{{ $p->address }}" @selected((string) $patientId === (string) $p->id)>
                        {{ $p->last_name }} {{ $p->first_name }}
                    </option>
                @endforeach
            </select>
            @error('patient_id') <p class="error">{{ $message }}</p> @enderror
        </div>
    @endif

    {{-- Médecin --}}
    <div class="field {{ $isPatient ? 'full' : '' }}">
        <label for="doctor_id">Médecin <span class="text-danger">*</span></label>
        <select id="doctor_id" name="doctor_id" required>
            <option value="">Sélectionnez un médecin</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected((string) old('doctor_id', $appointment->doctor_id ?? '') === (string) $doctor->id)>
                    {{ $doctor->user?->name ?? 'Médecin #'.$doctor->id }}{{ $doctor->specialty ? ' — '.$doctor->specialty : '' }}
                </option>
            @endforeach
        </select>
        @error('doctor_id') <p class="error">{{ $message }}</p> @enderror
    </div>

    {{-- Choix : Cabinet ou Visite à domicile --}}
    <div class="field full">
        <label>Lieu du rendez-vous <span class="text-danger">*</span></label>
        <div class="consultation-type-selector">
            <label class="type-card @if($selectedType === 'clinic') active @endif">
                <input type="radio" name="consultation_type" value="clinic" @checked($selectedType === 'clinic')>
                <div class="type-icon">🏥</div>
                <div class="type-info">
                    <strong>Au cabinet</strong>
                    <span>Le patient se déplace au cabinet médical</span>
                </div>
            </label>

            <label class="type-card @if($selectedType === 'home') active @endif">
                <input type="radio" name="consultation_type" value="home" @checked($selectedType === 'home')>
                <div class="type-icon">🏠</div>
                <div class="type-info">
                    <strong>À domicile</strong>
                    <span>Le médecin se déplace chez le patient</span>
                </div>
            </label>
        </div>
        @error('consultation_type') <p class="error">{{ $message }}</p> @enderror
    </div>

    {{-- Adresse pour visite à domicile --}}
    <div class="field full" id="home_address_container" style="@if($selectedType !== 'home') display: none; @endif">
        <label for="home_address">
            Adresse exacte du domicile pour la visite <span class="text-danger">*</span>
        </label>
        <input id="home_address" name="home_address" type="text"
               value="{{ $patientAddress }}"
               placeholder="Ex. Lot IVB 12, Rue Rainandriamampandry, Antananarivo" />
        <p class="field-hint">Précisez l'adresse, quartier, repère pour faciliter la venue du médecin.</p>
        @error('home_address') <p class="error">{{ $message }}</p> @enderror
    </div>

    {{-- Salle (visible seulement pour staff/admin et si consultation au cabinet) --}}
    @if (!$isPatient)
        <div class="field" id="room_container" style="@if($selectedType === 'home') display: none; @endif">
            <label for="room_id">Salle de consultation</label>
            <select id="room_id" name="room_id">
                <option value="">Aucune salle attribuée</option>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" @selected((string) old('room_id', $appointment->room_id ?? '') === (string) $room->id)>
                        {{ $room->name }}
                    </option>
                @endforeach
            </select>
            @error('room_id') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="status">Statut</label>
            <select id="status" name="status" required>
                @foreach (['scheduled' => 'Programmé', 'pending' => 'En attente', 'confirmed' => 'Confirmé', 'checked_in' => 'Arrivé', 'completed' => 'Terminé', 'cancelled' => 'Annulé', 'no_show' => 'Absent'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $appointment->status ?? 'scheduled') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('status') <p class="error">{{ $message }}</p> @enderror
        </div>
    @endif

    {{-- Dates & Horaires --}}
    <div class="field">
        <label for="starts_at">Date et heure de début <span class="text-danger">*</span></label>
        <input id="starts_at" name="starts_at" type="datetime-local"
               value="{{ old('starts_at', isset($appointment) && $appointment->starts_at ? $appointment->starts_at->format('Y-m-d\\TH:i') : '') }}"
               required>
        @error('starts_at') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="ends_at">Date et heure de fin <span class="text-danger">*</span></label>
        <input id="ends_at" name="ends_at" type="datetime-local"
               value="{{ old('ends_at', isset($appointment) && $appointment->ends_at ? $appointment->ends_at->format('Y-m-d\\TH:i') : '') }}"
               required>
        @error('ends_at') <p class="error">{{ $message }}</p> @enderror
    </div>

    {{-- Motif --}}
    <div class="field full">
        <label for="reason">Motif de la consultation / Symptômes</label>
        <textarea id="reason" name="reason" rows="3" placeholder="Décrivez brièvement la raison de votre demande ou vos symptômes...">{{ old('reason', $appointment->reason ?? '') }}</textarea>
        @error('reason') <p class="error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="actions">
    <button type="submit" class="button primary">{{ $submitLabel }}</button>
    <a class="button secondary" href="{{ route('appointments.index') }}">Annuler</a>
</div>

<style>
    .consultation-type-selector {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 0.25rem;
    }

    .type-card {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.9rem 1.1rem;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .type-card input[type="radio"] {
        display: none;
    }

    .type-card:hover {
        border-color: #93c5fd;
        background: #eff6ff;
    }

    .type-card.active {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.12);
    }

    .type-icon {
        font-size: 1.8rem;
        line-height: 1;
    }

    .type-info {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .type-info strong {
        font-size: 0.95rem;
        color: #0f172a;
    }

    .type-info span {
        font-size: 0.78rem;
        color: #64748b;
    }

    .patient-badge {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
    }

    .patient-icon {
        font-size: 1.4rem;
    }

    .patient-sub {
        display: block;
        font-size: 0.8rem;
        color: #64748b;
    }

    .field-hint {
        margin: 0.3rem 0 0;
        font-size: 0.75rem;
        color: #64748b;
    }

    .text-danger {
        color: #ef4444;
    }

    @media (max-width: 640px) {
        .consultation-type-selector {
            grid-template-columns: 1fr;
        }

        .type-card {
            padding: 0.8rem 1rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeRadios = document.querySelectorAll('input[name="consultation_type"]');
        const homeContainer = document.getElementById('home_address_container');
        const roomContainer = document.getElementById('room_container');
        const patientSelect = document.getElementById('patient_id');
        const homeInput = document.getElementById('home_address');

        function updateTypeUI() {
            let selected = 'clinic';
            typeRadios.forEach(r => {
                if (r.checked) selected = r.value;
                r.closest('.type-card').classList.toggle('active', r.checked);
            });

            if (selected === 'home') {
                if (homeContainer) homeContainer.style.display = 'block';
                if (roomContainer) roomContainer.style.display = 'none';
            } else {
                if (homeContainer) homeContainer.style.display = 'none';
                if (roomContainer) roomContainer.style.display = 'block';
            }
        }

        typeRadios.forEach(r => {
            r.addEventListener('change', updateTypeUI);
        });

        // Si l'utilisateur change de patient (secrétaire), pré-remplir l'adresse si vide
        if (patientSelect && homeInput) {
            patientSelect.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                const addr = opt ? opt.getAttribute('data-address') : '';
                if (addr && !homeInput.value) {
                    homeInput.value = addr;
                }
            });
        }

        // Auto-calculer l'heure de fin (+30 min) si l'heure de début change et que fin est vide
        const startsInput = document.getElementById('starts_at');
        const endsInput = document.getElementById('ends_at');
        if (startsInput && endsInput) {
            startsInput.addEventListener('change', function () {
                if (this.value && !endsInput.value) {
                    const d = new Date(this.value);
                    d.setMinutes(d.getMinutes() + 30);
                    const pad = n => String(n).padStart(2, '0');
                    endsInput.value = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
                }
            });
        }
    });
</script>
