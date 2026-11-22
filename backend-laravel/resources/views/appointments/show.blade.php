@extends('layouts.app')

@section('title', 'Rendez-vous · MediCare')

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
            <p class="page-kicker">Rendez-vous</p>
            <h1>Rendez-vous du {{ $appointment->starts_at->format('d/m/Y') }}</h1>
            <p class="page-subtitle">{{ $appointment->starts_at->format('H:i') }} — {{ $appointment->ends_at->format('H:i') }}</p>
        </div>
        @can('update', $appointment)
            <a class="button" href="{{ route('appointments.edit', $appointment) }}">Modifier</a>
        @endcan
    </div>

    <section class="detail-card">
        <div class="detail-grid">
            <div class="detail-item">
                <span>Patient</span>
                <strong>{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</strong>
            </div>
            <div class="detail-item">
                <span>Médecin</span>
                <strong>{{ $appointment->doctor->user?->name ?? 'Médecin #'.$appointment->doctor_id }}</strong>
            </div>
            <div class="detail-item">
                <span>Type de consultation</span>
                <strong>
                    @if ($appointment->isHomeVisit())
                        <span class="status-pill warning">🏠 Visite à domicile</span>
                    @else
                        <span class="status-pill info">🏥 Au cabinet</span>
                    @endif
                </strong>
            </div>
            @if ($appointment->isHomeVisit())
                <div class="detail-item" style="grid-column: 1 / -1; background: #fffbeb; border-color: #fde68a;">
                    <span style="color: #b45309;">📍 Adresse du domicile du patient</span>
                    <strong style="color: #92400e; font-size: 1.05rem;">
                        {{ $appointment->home_address ?: ($appointment->patient->address ?: 'Non renseignée') }}
                    </strong>
                    @if ($appointment->patient->phone)
                        <p style="margin-top: 0.35rem; color: #b45309; font-size: 0.85rem;">
                            📞 Téléphone du patient : <a href="tel:{{ $appointment->patient->phone }}"><strong>{{ $appointment->patient->phone }}</strong></a>
                        </p>
                    @endif
                </div>
            @else
                <div class="detail-item">
                    <span>Salle</span>
                    <strong>{{ $appointment->room?->name ?? 'Non attribuée' }}</strong>
                </div>
            @endif
            <div class="detail-item">
                <span>Statut</span>
                <strong><span class="status-pill {{ $statusClasses[$appointment->status] ?? 'muted' }}">{{ $statusLabels[$appointment->status] ?? ucfirst($appointment->status) }}</span></strong>
            </div>
            <div class="detail-item" style="grid-column: 1 / -1;">
                <span>Motif</span>
                <p>{!! nl2br(e($appointment->reason ?? 'Non renseigné')) !!}</p>
            </div>
        </div>

        <div style="display:flex; gap:.75rem; flex-wrap:wrap; margin-top:1.25rem;">
            @can('confirm', $appointment)
                @if ($appointment->status !== 'confirmed')
                    <form method="POST" action="{{ route('appointments.confirm', $appointment) }}">
                        @csrf
                        @method('PATCH')
                        <button class="button success" type="submit">Confirmer le rendez-vous</button>
                    </form>
                @endif

                @if ($appointment->status !== 'cancelled')
                    <form method="POST" action="{{ route('appointments.reject', $appointment) }}">
                        @csrf
                        @method('PATCH')
                        <button class="button danger" type="submit">Refuser le rendez-vous</button>
                    </form>
                @endif
            @endcan
        </div>
    </section>
@endsection
