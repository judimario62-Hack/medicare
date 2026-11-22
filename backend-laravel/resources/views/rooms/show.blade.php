@extends('layouts.app')

@section('title', 'Salle · MediCare')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Salle</p>
            <h1>{{ $room->name }}</h1>
            <p class="page-subtitle">Salle du cabinet</p>
        </div>
        <a class="button" href="{{ route('rooms.edit', $room) }}">Modifier</a>
    </div>

    <section class="detail-card">
        @if ($room->image_path)
            <img src="{{ asset('storage/'.$room->image_path) }}" alt="{{ $room->name }}" style="width:min(100%,420px);height:260px;border-radius:18px;object-fit:cover;margin-bottom:1rem;">
        @endif
        <div class="detail-grid">
            <div class="detail-item">
                <span>Emplacement</span>
                <strong>{{ $room->location ?? 'Non renseigné' }}</strong>
            </div>
            <div class="detail-item">
                <span>Capacité</span>
                <strong>{{ $room->capacity }}</strong>
            </div>
            <div class="detail-item">
                <span>Statut</span>
                <strong><span class="status-pill {{ $room->is_active ? 'active' : 'inactive' }}">{{ $room->is_active ? 'Active' : 'Inactive' }}</span></strong>
            </div>
        </div>
    </section>
@endsection
