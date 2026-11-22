@extends('layouts.app')

@section('title', 'Dossier patient · MediCare')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Dossier patient</p>
            <h1>{{ $patient->first_name }} {{ $patient->last_name }}</h1>
        </div>
        @can('update', $patient)
            <a class="button" href="{{ auth()->user()->role === 'patient' ? route('patient-record.edit') : route('patients.edit', $patient) }}">Modifier</a>
        @endcan
    </div>

    <section class="detail-card">
        <div style="display:flex; align-items:center; gap:1rem; margin-bottom:.5rem;">
            <x-account-avatar :name="$patient->first_name.' '.$patient->last_name" :path="$patient->avatar_path ?? null" :size="96" />
        </div>
        <div class="detail-grid">
            <div class="detail-item">
                <span>Date de naissance</span>
                <strong>{{ $patient->date_of_birth?->format('d/m/Y') ?? 'Non renseignée' }}</strong>
            </div>
            <div class="detail-item">
                <span>Téléphone</span>
                <strong>{{ $patient->phone ?? 'Non renseigné' }}</strong>
            </div>
            <div class="detail-item" style="grid-column: 1 / -1;">
                <span>Adresse</span>
                <p>{!! nl2br(e($patient->address ?? 'Non renseignée')) !!}</p>
            </div>
        </div>
    </section>
@endsection
