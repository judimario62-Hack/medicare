@extends('layouts.app')

@section('title', 'Dossier médical · MediCare')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap;">
        <div>
            <p style="margin:0 0 .3rem; color:#64748b; font-size:.7rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase;">Dossier</p>
            <h1 style="margin:0; font-size:clamp(2rem,3vw,2.8rem);">Dossier médical</h1>
        </div>
    </div>

    <section class="card" style="padding:1.25rem; border-radius:1.2rem; background:#fff; box-shadow:0 18px 40px rgba(15,23,42,.08); border:1px solid rgba(148,163,184,.18);">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem;">
            <div style="padding:1rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:1rem;">
                <strong>Patient</strong>
                <div>{{ $patient->last_name }} {{ $patient->first_name }}</div>
                <small style="color:#64748b;">{{ $patient->date_of_birth?->format('d/m/Y') ?? 'Date non renseignée' }}</small>
            </div>
            <div style="padding:1rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:1rem;">
                <strong>Coordonnées</strong>
                <div>{{ $patient->phone ?? 'Téléphone non renseigné' }}</div>
                <small style="color:#64748b;">{{ $patient->address ?? 'Adresse non renseignée' }}</small>
            </div>
        </div>

        @forelse ($patient->appointments as $appointment)
            <div style="margin-bottom:1.25rem; padding:1rem; border:1px solid #e2e8f0; border-radius:1rem; background:#f8fafc;">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:.75rem;">
                    <strong>Consultation du {{ $appointment->starts_at?->format('d/m/Y H:i') }}</strong>
                    <span style="display:inline-flex; padding:.35rem .7rem; border-radius:999px; background:rgba(59,130,246,.12); color:#1d4ed8; font-weight:700; font-size:.72rem;">
                        {{ $appointment->status }}
                    </span>
                </div>

                @if ($appointment->consultation)
                    <div style="display:grid; gap:.8rem;">
                        <div>
                            <strong>Sujetif</strong>
                            <p style="margin:.3rem 0 0; color:#475569;">{{ $appointment->consultation->subjective ?? 'Non renseigné' }}</p>
                        </div>
                        <div>
                            <strong>Objectif</strong>
                            <p style="margin:.3rem 0 0; color:#475569;">{{ $appointment->consultation->objective ?? 'Non renseigné' }}</p>
                        </div>
                        <div>
                            <strong>Évaluation</strong>
                            <p style="margin:.3rem 0 0; color:#475569;">{{ $appointment->consultation->assessment ?? 'Non renseigné' }}</p>
                        </div>
                        <div>
                            <strong>Plan</strong>
                            <p style="margin:.3rem 0 0; color:#475569;">{{ $appointment->consultation->plan ?? 'Non renseigné' }}</p>
                        </div>

                        @if ($appointment->consultation->vitalSigns->isNotEmpty())
                            <div>
                                <strong>Signes vitaux</strong>
                                <ul style="margin:.6rem 0 0; padding-left:1.1rem; color:#475569;">
                                    @foreach ($appointment->consultation->vitalSigns as $sign)
                                        <li>{{ $sign->type }} : {{ $sign->value }} {{ $sign->unit ?? '' }} — {{ $sign->notes ?? 'Aucune note' }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @else
                    <p style="margin:0; color:#64748b;">Aucune consultation enregistrée pour ce rendez-vous.</p>
                @endif
            </div>
        @empty
            <div style="padding:1.2rem; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:1rem; color:#475569;">
                Aucune consultation pour ce patient.
            </div>
        @endforelse
    </section>
@endsection
