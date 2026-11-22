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
            'scheduled' => 'info',
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
            <h1>{{ $pageTitle ?? 'Gestion des rendez-vous' }}</h1>
            @if (!empty($pageSubtitle))
                <p class="page-subtitle">{{ $pageSubtitle }}</p>
            @endif
        </div>
        <div class="actions" style="margin-top:0;">
            @if (in_array(Auth::user()?->role, ['admin', 'staff'], true))
                <a class="button secondary" href="{{ route('appointments.pending') }}">Demandes à valider</a>
            @endif
            @can('create', App\Models\Appointment::class)
                <a class="button" href="{{ route('appointments.create') }}">Nouveau rendez-vous</a>
            @endcan
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <span>Total</span>
            <strong>{{ $appointments->total() }}</strong>
        </div>
        <div class="stat-box">
            <span>En attente</span>
            <strong>{{ $appointments->whereIn('status', ['scheduled', 'pending'])->count() }}</strong>
        </div>
        <div class="stat-box">
            <span>Confirmés</span>
            <strong>{{ $appointments->whereIn('status', ['confirmed', 'checked_in', 'completed'])->count() }}</strong>
        </div>
    </div>

    <section class="card">
        @if ($appointments->isEmpty())
            <div class="empty-state">
                @if (Auth::user()?->role === 'patient')
                    Vous n’avez pas encore de rendez-vous.
                @else
                    Aucun rendez-vous n’est programmé.
                @endif
            </div>
        @else
            <div class="table-shell">
                <table class="data-table">
                    <thead>
                        <tr>
                            @if (Auth::user()?->role !== 'patient')
                                <th>Patient</th>
                            @endif
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
                                @if (Auth::user()?->role !== 'patient')
                                    <td>
                                        <strong>{{ $appointment->patient?->first_name ?? '' }} {{ $appointment->patient?->last_name ?? '' }}</strong>
                                    </td>
                                @endif
                                <td>{{ $appointment->doctor?->user?->name ?? 'Médecin #'.$appointment->doctor_id }}</td>
                                <td>
                                    <strong>{{ $appointment->starts_at?->format('d/m/Y') ?? '-' }}</strong><br>
                                    <small style="color:var(--muted)">{{ $appointment->starts_at?->format('H:i') ?? '-' }} — {{ $appointment->ends_at?->format('H:i') ?? '-' }}</small>
                                </td>
                                <td>
                                    @if ($appointment->isHomeVisit())
                                        <span class="status-pill warning" title="{{ $appointment->home_address }}">🏠 À domicile</span>
                                    @else
                                        <span class="status-pill info">🏥 Au cabinet</span>
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
                                        <a class="button secondary" href="{{ route('appointments.show', $appointment) }}">Voir</a>
                                        @can('confirm', $appointment)
                                            <form method="POST" action="{{ route('appointments.confirm', $appointment) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="button" type="submit">Confirmer</button>
                                            </form>
                                            <form method="POST" action="{{ route('appointments.reject', $appointment) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="button danger" type="submit">Refuser</button>
                                            </form>
                                        @endcan
                                        @if (Auth::user()?->role === 'patient' && $appointment->status !== 'cancelled')
                                            <a class="button secondary" href="{{ route('appointments.show', $appointment) }}">Détails</a>
                                        @endif
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
