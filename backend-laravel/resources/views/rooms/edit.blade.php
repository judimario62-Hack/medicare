@extends('layouts.app')

@section('title', 'Modifier la salle · MediCare')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Salles</p>
            <h1>Modifier la salle</h1>
        </div>
    </div>

    <section class="form-card">
        <form method="POST" action="{{ route('rooms.update', $room) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('rooms.form', ['submitLabel' => 'Enregistrer'])
        </form>
    </section>
@endsection
