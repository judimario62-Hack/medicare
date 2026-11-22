@extends('layouts.app')

@section('title', 'Agenda médecin · MediCare')

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
            <p class="page-kicker">Agenda</p>
            <h1>Planning du médecin</h1>
        </div>
    </div>

    <section class="card">
        @if ($appointments->isEmpty())
            <div class="empty-state">
                Aucun rendez-vous n’est programmé pour votre agenda.
            </div>
        @else
            <div class="table-shell">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date & Heure</th>
                            <th>Lieu</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appointments as $appointment)
                            <tr>
                                <td>
                                    <strong>{{ $appointment->patient?->last_name ?? '' }} {{ $appointment->patient?->first_name ?? '' }}</strong>
                                    @if ($appointment->isHomeVisit() && $appointment->home_address)
                                        <br><small style="color:#b45309;">📍 {{ $appointment->home_address }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $appointment->starts_at?->format('d/m/Y') }}</strong><br>
                                    <small style="color:var(--muted)">{{ $appointment->starts_at?->format('H:i') }} — {{ $appointment->ends_at?->format('H:i') }}</small>
                                </td>
                                <td>
                                    @if ($appointment->isHomeVisit())
                                        <span class="status-pill warning">🏠 Domicile</span>
                                    @else
                                        <span class="status-pill info">🏥 {{ $appointment->room?->name ?? 'Cabinet' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-pill {{ $statusClasses[$appointment->status] ?? 'muted' }}">
                                        {{ $statusLabels[$appointment->status] ?? ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a class="button secondary" href="{{ route('appointments.show', $appointment) }}">Voir</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
