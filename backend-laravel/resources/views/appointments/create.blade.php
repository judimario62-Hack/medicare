@extends('layouts.app')
@section('title', Auth::user()?->role === 'patient' ? 'Demande de rendez-vous · MediCare' : 'Nouveau rendez-vous · MediCare')
@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Rendez-vous</p>
            <h1>{{ Auth::user()?->role === 'patient' ? 'Demander un rendez-vous' : 'Nouveau rendez-vous' }}</h1>
            @if (Auth::user()?->role === 'patient')
                <p class="page-subtitle">Votre demande restera en attente jusqu’à sa confirmation par le secrétariat.</p>
            @endif
        </div>
    </div>

    <section class="form-card">
        <form method="POST" action="{{ route('appointments.store') }}">
            @include('appointments.form', [
                'submitLabel' => Auth::user()?->role === 'patient' ? 'Envoyer ma demande' : 'Créer le rendez-vous',
            ])
        </form>
    </section>
@endsection
