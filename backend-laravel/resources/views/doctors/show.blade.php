@extends('layouts.app')

@section('title', 'Médecin · MediCare')

@section('content')
    @php
        $statusClass = $doctor->is_active ? 'success' : 'warning';
        $statusLabel = $doctor->is_active ? 'Actif' : 'Inactif';
    @endphp

    <div class="page-header">
        <div>
            <p class="page-kicker">Médecin</p>
            <h1>{{ $doctor->user?->name ?? 'Médecin' }}</h1>
            <p class="page-subtitle">{{ $doctor->specialty ?? 'Spécialité non renseignée' }}</p>
        </div>
        <a class="button" href="{{ route('doctors.edit', $doctor) }}">Modifier</a>
    </div>

    <section class="detail-card">
        <div style="margin-bottom:.5rem;">
            <x-account-avatar
                :name="$doctor->user?->name ?? 'Médecin'"
                :path="$doctor->avatar_path ?? $doctor->user?->avatar_path"
                :size="96"
            />
        </div>
        <div class="detail-grid">
            <div class="detail-item">
                <span>Numéro professionnel</span>
                <strong>{{ $doctor->license_number ?? 'Non renseigné' }}</strong>
            </div>
            <div class="detail-item">
                <span>Téléphone</span>
                <strong>{{ $doctor->phone ?? 'Non renseigné' }}</strong>
            </div>
            <div class="detail-item">
                <span>Statut</span>
                <strong><span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span></strong>
            </div>
            <div class="detail-item">
                <span>Email</span>
                <strong>{{ $doctor->user?->email ?? 'Aucun compte associé' }}</strong>
            </div>
        </div>
    </section>
@endsection
