@extends('layouts.app')

@section('title', 'Salles · MediCare')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Infrastructure</p>
            <h1>Gestion des salles</h1>
        </div>
        <a class="button" href="{{ route('rooms.create') }}">Ajouter une salle</a>
    </div>

    <section class="card">
        @if ($rooms->isEmpty())
            <div class="empty-state">
                Aucune salle n’a encore été créée.
            </div>
        @else
            <div class="table-shell">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Emplacement</th>
                            <th>Capacité</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rooms as $room)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:.7rem;">
                                        @if ($room->image_path)
                                            <img src="{{ asset('storage/'.$room->image_path) }}" alt="{{ $room->name }}" style="width:54px;height:54px;border-radius:12px;object-fit:cover;">
                                        @endif
                                        <strong>{{ $room->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $room->location ?? 'Emplacement non renseigné' }}</td>
                                <td>{{ $room->capacity }}</td>
                                <td>
                                    <span class="status-pill {{ $room->is_active ? 'active' : 'inactive' }}">
                                        {{ $room->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a class="button secondary" href="{{ route('rooms.show', $room) }}">Voir</a>
                                        <a class="button secondary" href="{{ route('rooms.edit', $room) }}">Modifier</a>
                                        <form method="POST" action="{{ route('rooms.destroy', $room) }}" onsubmit="return confirm('Supprimer cette salle ?');" style="display:inline;">
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
            <div style="margin-top:1.25rem;">{{ $rooms->links() }}</div>
        @endif
    </section>
@endsection
