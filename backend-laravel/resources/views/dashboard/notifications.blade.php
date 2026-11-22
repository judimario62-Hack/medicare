@extends('layouts.app')

@section('title', 'Notifications · MediCare')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Alertes</p>
            <h1>Notifications</h1>
        </div>
    </div>

    @php
        $typeClasses = [
            'appointment_pending' => 'warning',
            'appointment_pending_staff' => 'warning',
            'appointment_confirmed_patient' => 'success',
            'appointment_rejected_patient' => 'danger',
            'appointment_booked' => 'info',
            'consultation_recorded' => 'info',
            'appointment_confirmed' => 'success',
            'appointment_confirmed_staff' => 'success',
            'appointment_rejected' => 'danger',
            'appointment_rejected_staff' => 'danger',
            'appointment_completed_patient' => 'success',
            'appointment_completed_staff' => 'success',
            'appointment_cancelled' => 'muted',
        ];

        $typeLabels = [
            'appointment_pending' => 'En attente',
            'appointment_pending_staff' => 'À valider',
            'appointment_confirmed_patient' => 'Confirmé',
            'appointment_confirmed_staff' => 'Confirmé',
            'appointment_rejected_patient' => 'Refusé',
            'appointment_rejected_staff' => 'Refusé',
            'appointment_completed_patient' => 'Consultation terminée',
            'appointment_completed_staff' => 'Consultation terminée',
            'appointment_booked' => 'Nouvelle demande',
            'appointment_confirmed' => 'Rendez-vous confirmé',
            'appointment_rejected' => 'Rendez-vous refusé',
            'consultation_recorded' => 'Consultation enregistrée',
            'appointment_cancelled' => 'Annulé',
        ];
    @endphp

    <section class="card">
        @if ($alerts->isEmpty())
            <div class="empty-state">
                Aucune notification pour le moment.
            </div>
        @else
            <div style="display:grid; gap:1rem;">
                @foreach ($alerts as $alert)
                    @php
                        $typeKey = $alert->type ?? 'info';
                        $badgeClass = $typeClasses[$typeKey] ?? 'info';
                    @endphp
                    <article style="padding:1rem 1.1rem; background:linear-gradient(135deg,#f8fafc,#f8f5ff); border:1px solid #e2e8f0; border-radius:1rem; box-shadow:0 8px 20px rgba(15, 23, 42, 0.04);">
                        <div style="display:flex; justify-content:space-between; gap:1rem; align-items:center; flex-wrap:wrap; margin-bottom:.55rem;">
                            <strong style="font-size:1rem; color:#0f172a;">{{ $alert->title }}</strong>
                            <span class="status-pill {{ $badgeClass }}">
                                {{ $typeLabels[$typeKey] ?? ucfirst(str_replace('_', ' ', $typeKey)) }}
                            </span>
                        </div>
                        <div style="color:#475569; margin-bottom:.5rem; line-height:1.5;">{{ $alert->message }}</div>
                        <small style="color:#64748b; display:inline-flex; align-items:center; gap:.4rem;">
                            <span>🕒</span>
                            {{ $alert->created_at?->format('d/m/Y H:i') }}
                        </small>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
