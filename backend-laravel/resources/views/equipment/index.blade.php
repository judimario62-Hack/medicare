@extends('layouts.app')
@section('title', 'Équipements · MediCare')
@section('content')
<div class="page-header"><div><p class="page-kicker">Matériel médical</p><h1>Gestion des équipements</h1></div><a class="button" href="{{ route('equipment.create') }}">Ajouter un équipement</a></div>
<section class="card">
@if ($equipment->isEmpty())<div class="empty-state">Aucun équipement enregistré.</div>
@else
<div class="table-shell"><table class="data-table"><thead><tr><th>Équipement</th><th>Catégorie</th><th>Salle</th><th>Statut</th><th>Actions</th></tr></thead><tbody>
@foreach ($equipment as $item)<tr><td><div style="display:flex;align-items:center;gap:.7rem;">@if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->name }}" style="width:54px;height:54px;border-radius:12px;object-fit:cover;">@endif<strong>{{ $item->name }}</strong></div></td><td>{{ $item->category ?? 'Non renseignée' }}</td><td>{{ $item->room?->name ?? 'Non attribué' }}</td><td><span class="status-pill {{ $item->status === 'available' ? 'active' : ($item->status === 'maintenance' ? 'warning' : 'muted') }}">{{ ['available'=>'Disponible','in_use'=>'En utilisation','maintenance'=>'Maintenance','out_of_service'=>'Hors service'][$item->status] ?? $item->status }}</span></td><td><div class="table-actions"><a class="button secondary" href="{{ route('equipment.show', $item) }}">Voir</a><a class="button secondary" href="{{ route('equipment.edit', $item) }}">Modifier</a><form method="POST" action="{{ route('equipment.destroy', $item) }}" onsubmit="return confirm('Supprimer cet équipement ?');">@csrf @method('DELETE')<button class="button danger" type="submit">Supprimer</button></form></div></td></tr>@endforeach
</tbody></table></div><div style="margin-top:1.25rem;">{{ $equipment->links() }}</div>
@endif
</section>
@endsection
