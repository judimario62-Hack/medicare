@extends('layouts.app')

@section('title', 'Patients · MediCare')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Dossiers patients</p>
            <h1>Gestion des patients</h1>
        </div>
        @can('create', App\Models\Patient::class)
            <a class="button" href="{{ route('patients.create') }}">Ajouter un patient</a>
        @endcan
    </div>

    <section class="card">
        @if ($patients->isEmpty())
            <div class="empty-state">
                Aucun dossier patient n’a encore été créé.
            </div>
        @else
            <div class="table-shell">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date de naissance</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($patients as $patient)
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:.7rem;">
                                        <x-account-avatar :name="$patient->first_name.' '.$patient->last_name" :path="$patient->avatar_path ?? null" :size="42" />
                                        <strong>{{ $patient->last_name }} {{ $patient->first_name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $patient->date_of_birth?->format('d/m/Y') ?? 'Non renseignée' }}</td>
                                <td>{{ $patient->phone ?? 'Non renseigné' }}</td>
                                <td>{{ $patient->email ?? 'Non renseigné' }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a class="button secondary" href="{{ route('patients.show', $patient) }}">Voir</a>
                                        <a class="button secondary" href="{{ route('patients.edit', $patient) }}">Modifier</a>
                                        <form method="POST" action="{{ route('patients.destroy', $patient) }}" onsubmit="return confirm('Supprimer ce dossier patient ?');" style="display:inline;">
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
            <div style="margin-top: 1.25rem;">{{ $patients->links() }}</div>
        @endif
    </section>
@endsection
