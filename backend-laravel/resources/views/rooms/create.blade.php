@extends('layouts.app')

@section('title', 'Nouvelle salle · MediCare')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Salles</p>
            <h1>Nouvelle salle</h1>
        </div>
    </div>

    <section class="form-card">
        <form method="POST" action="{{ route('rooms.store') }}" enctype="multipart/form-data">
            @include('rooms.form', ['submitLabel' => 'Créer la salle'])
        </form>
    </section>
@endsection
