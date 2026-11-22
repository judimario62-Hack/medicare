@extends('layouts.app')
@section('title', 'Équipement · MediCare')
@section('content')
<div class="page-header"><div><p class="page-kicker">Équipement</p><h1>{{ $equipment->name }}</h1></div><a class="button" href="{{ route('equipment.edit', $equipment) }}">Modifier</a></div>
<section class="detail-card">
@if ($equipment->image_path)<img src="{{ asset('storage/'.$equipment->image_path) }}" alt="{{ $equipment->name }}" style="width:min(100%,420px);height:280px;border-radius:18px;object-fit:cover;margin-bottom:1rem;">@endif
<div class="detail-grid"><div class="detail-item"><span>Catégorie</span><strong>{{ $equipment->category ?? 'Non renseignée' }}</strong></div><div class="detail-item"><span>Numéro de série</span><strong>{{ $equipment->serial_number ?? 'Non renseigné' }}</strong></div><div class="detail-item"><span>Salle</span><strong>{{ $equipment->room?->name ?? 'Non attribué' }}</strong></div><div class="detail-item"><span>Statut</span><strong>{{ ['available'=>'Disponible','in_use'=>'En utilisation','maintenance'=>'Maintenance','out_of_service'=>'Hors service'][$equipment->status] ?? $equipment->status }}</strong></div><div class="detail-item full"><span>Notes</span><p>{{ $equipment->notes ?? 'Aucune note' }}</p></div></div>
</section>
@endsection
