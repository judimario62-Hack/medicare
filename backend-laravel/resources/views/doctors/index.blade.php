@extends('layouts.app')

@section('title', 'Médecins · MediCare')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Personnel médical</p>
            <h1>Gestion des médecins</h1>
        </div>
        <a class="button" href="{{ route('doctors.create') }}">Ajouter un médecin</a>
    </div>

    <section class="card">
        @if ($doctors->isEmpty())
            <div class="empty-state">
                Aucun médecin n’a encore été créé.
            </div>
        @else
            <div class="table-shell">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Médecin</th>
                            <th>Spécialité</th>
                            <th>Numéro</th>
                            <th>Téléphone</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($doctors as $doctor)
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:.7rem;">
                                        <x-account-avatar
                                            :name="$doctor->user?->name ?? 'Médecin'"
                                            :path="$doctor->avatar_path ?? $doctor->user?->avatar_path"
                                            :size="42"
                                        />
                                        <strong>{{ $doctor->user?->name ?? 'Aucun compte associé' }}</strong>
                                    </div>
                                </td>
                                <td>{{ $doctor->specialty ?? 'Spécialité non renseignée' }}</td>
                                <td>{{ $doctor->license_number ?? 'N° non renseigné' }}</td>
                                <td>{{ $doctor->phone ?? 'Non renseigné' }}</td>
                                <td>
                                    <span class="status-pill {{ $doctor->is_active ? 'active' : 'inactive' }}">
                                        {{ $doctor->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a class="button secondary" href="{{ route('doctors.show', $doctor) }}">Voir</a>
                                        <a class="button secondary" href="{{ route('doctors.edit', $doctor) }}">Modifier</a>
                                        <form method="POST" action="{{ route('doctors.destroy', $doctor) }}" onsubmit="return confirm('Supprimer ce médecin ?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button danger" type="submit">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1.25rem;">{{ $doctors->links() }}</div>
        @endif
    </section>
@endsection
