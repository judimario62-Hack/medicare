@extends('layouts.app')

@section('title', 'Demandes à valider · MediCare')

@section('content')
    @php
        $statusLabels = [
            'scheduled' => 'En attente',
            'pending' => 'En attente',
            'confirmed' => 'Confirmé',
            'cancelled' => 'Refusé',
            'checked_in' => 'Arrivé',
            'completed' => 'Terminé',
            'no_show' => 'Absent',
        ];

        $statusClasses = [
            'scheduled' => 'warning',
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'checked_in' => 'info',
            'completed' => 'success',
            'no_show' => 'muted',
        ];
    @endphp

    <div class="page-header">
        <div>
            <p class="page-kicker">Secrétariat</p>
            <h1>Demandes à valider</h1>
        </div>
        <a class="button" href="{{ route('appointments.index') }}">Voir tous les rendez-vous</a>
    </div>

    <section class="card">
        @if ($appointments->isEmpty())
            <div class="empty-state">
                Aucune demande en attente de validation.
            </div>
        @else
            <div class="table-shell">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Médecin</th>
                            <th>Date & Heure</th>
                            <th>Lieu</th>
                            <th>Motif</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appointments as $appointment)
                            <tr>
                                <td>
                                    <strong>{{ $appointment->patient?->first_name ?? '' }} {{ $appointment->patient?->last_name ?? '' }}</strong>
                                    @if ($appointment->isHomeVisit() && $appointment->home_address)
                                        <br><small style="color:#b45309;">📍 {{ Str::limit($appointment->home_address, 30) }}</small>
                                    @endif
                                </td>
                                <td>{{ $appointment->doctor?->user?->name ?? 'Médecin #'.$appointment->doctor_id }}</td>
                                <td>
                                    <strong>{{ $appointment->starts_at?->format('d/m/Y') ?? '-' }}</strong><br>
                                    <small style="color:var(--muted)">{{ $appointment->starts_at?->format('H:i') ?? '-' }} — {{ $appointment->ends_at?->format('H:i') ?? '-' }}</small>
                                </td>
                                <td>
                                    @if ($appointment->isHomeVisit())
                                        <span class="status-pill warning" title="{{ $appointment->home_address }}">🏠 Domicile</span>
                                    @else
                                        <span class="status-pill info">🏥 Cabinet</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($appointment->reason ?? 'Non renseigné', 35) }}</td>
                                <td>
                                    <span class="status-pill {{ $statusClasses[$appointment->status] ?? 'muted' }}">
                                        {{ $statusLabels[$appointment->status] ?? ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a class="button secondary" href="{{ route('appointments.show', $appointment) }}">Détails</a>
                                        @can('confirm', $appointment)
                                            <form method="POST" action="{{ route('appointments.confirm', $appointment) }}" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button class="button" type="submit">Confirmer</button>
                                            </form>
                                            <form method="POST" action="{{ route('appointments.reject', $appointment) }}" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button class="button danger" type="submit">Refuser</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1.25rem;">{{ $appointments->links() }}</div>
        @endif
    </section>
@endsection
