@extends('layouts.app')

@section('title', 'Consultation · MediCare')

@section('content')
    @php
        $statusLabels = [
            'scheduled' => 'En attente',
            'pending' => 'En attente',
            'confirmed' => 'Confirmé',
            'checked_in' => 'Arrivé',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
            'no_show' => 'Absent',
        ];

        $statusClasses = [
            'scheduled' => 'warning',
            'pending' => 'warning',
            'confirmed' => 'success',
            'checked_in' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            'no_show' => 'muted',
        ];
    @endphp

    <div class="page-header">
        <div>
            <p class="page-kicker">Consultation</p>
            <h1>Consultation médicale</h1>
        </div>
        <a class="button secondary" href="{{ route('appointments.agenda') }}">Retour à l’agenda</a>
    </div>

    <section class="detail-card" style="margin-bottom:1rem;">
        <div class="detail-grid">
            <div class="detail-item">
                <span>Patient</span>
                <strong>{{ $appointment->patient?->last_name ?? '' }} {{ $appointment->patient?->first_name ?? '' }}</strong>
            </div>
            <div class="detail-item">
                <span>Médecin</span>
                <strong>{{ $appointment->doctor?->user?->name ?? 'Médecin' }}</strong>
            </div>
            <div class="detail-item">
                <span>Date / Heure</span>
                <strong>{{ $appointment->starts_at?->format('d/m/Y H:i') }}</strong>
            </div>
            <div class="detail-item">
                <span>Statut</span>
                <strong><span class="status-pill {{ $statusClasses[$appointment->status] ?? 'muted' }}">{{ $statusLabels[$appointment->status] ?? ucfirst($appointment->status) }}</span></strong>
            </div>
        </div>
    </section>

    <section class="form-card">
        <form method="POST" action="{{ route('appointments.consultation.store', $appointment) }}">
            @csrf

            <div class="form-grid">
                <div class="field full">
                    <label for="subjective">Sujetif</label>
                    <textarea id="subjective" name="subjective" rows="4">{{ old('subjective', $appointment->consultation?->subjective ?? '') }}</textarea>
                </div>

                <div class="field full">
                    <label for="objective">Objectif</label>
                    <textarea id="objective" name="objective" rows="4">{{ old('objective', $appointment->consultation?->objective ?? '') }}</textarea>
                </div>

                <div class="field full">
                    <label for="assessment">Évaluation</label>
                    <textarea id="assessment" name="assessment" rows="4">{{ old('assessment', $appointment->consultation?->assessment ?? '') }}</textarea>
                </div>

                <div class="field full">
                    <label for="plan">Plan</label>
                    <textarea id="plan" name="plan" rows="4">{{ old('plan', $appointment->consultation?->plan ?? '') }}</textarea>
                </div>
            </div>

            <div style="margin-top:1.5rem; padding:1rem 1.1rem; border:1px solid rgba(148,163,184,0.18); border-radius:18px; background:#f8fafc;">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
                    <h3 style="margin:0;">Signes vitaux</h3>
                    <button type="button" class="button secondary" onclick="addVitalRow()">Ajouter un signe vital</button>
                </div>

                <div id="vital-signs-list" style="display:grid; gap:0.9rem;">
                    <div class="vital-row" style="display:grid; grid-template-columns:1.2fr 1fr 1fr 2fr; gap:0.75rem; align-items:end;">
                        <div class="field">
                            <label>Type</label>
                            <input type="text" name="vital_signs[0][type]" value="">
                        </div>
                        <div class="field">
                            <label>Valeur</label>
                            <input type="text" name="vital_signs[0][value]" value="">
                        </div>
                        <div class="field">
                            <label>Unité</label>
                            <input type="text" name="vital_signs[0][unit]" value="">
                        </div>
                        <div class="field">
                            <label>Notes</label>
                            <input type="text" name="vital_signs[0][notes]" value="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="actions" style="justify-content:space-between;">
                <a class="button secondary" href="{{ route('appointments.agenda') }}">Retour à l’agenda</a>
                <button type="submit">Enregistrer la consultation</button>
            </div>
        </form>

        @if ($appointment->consultation)
            <div style="margin-top:1.5rem; display:flex; justify-content:flex-end;">
                <a class="button" href="{{ route('appointments.prescription.pdf', $appointment) }}" target="_blank">Générer ordonnance PDF</a>
            </div>
        @endif
    </section>

    <script>
        function addVitalRow() {
            const list = document.getElementById('vital-signs-list');
            const rows = list.querySelectorAll('.vital-row');
            const index = rows.length;
            const row = document.createElement('div');
            row.className = 'vital-row';
            row.style.display = 'grid';
            row.style.gridTemplateColumns = '1.2fr 1fr 1fr 2fr';
            row.style.gap = '.75rem';
            row.style.alignItems = 'end';
            row.innerHTML = `
                <div class="field">
                    <label>Type</label>
                    <input type="text" name="vital_signs[${index}][type]" value="">
                </div>
                <div class="field">
                    <label>Valeur</label>
                    <input type="text" name="vital_signs[${index}][value]" value="">
                </div>
                <div class="field">
                    <label>Unité</label>
                    <input type="text" name="vital_signs[${index}][unit]" value="">
                </div>
                <div class="field">
                    <label>Notes</label>
                    <input type="text" name="vital_signs[${index}][notes]" value="">
                </div>
            `;
            list.appendChild(row);
        }
    </script>
@endsection
