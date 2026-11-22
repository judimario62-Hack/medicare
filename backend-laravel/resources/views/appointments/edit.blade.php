@extends('layouts.app')
@section('title', 'Modifier le rendez-vous · MediCare')
@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Rendez-vous</p>
            <h1>Modifier le rendez-vous</h1>
        </div>
    </div>

    <section class="form-card">
        <form method="POST" action="{{ route('appointments.update', $appointment) }}">
            @method('PUT')
            @include('appointments.form', ['submitLabel' => 'Enregistrer'])
        </form>
    </section>
@endsection
