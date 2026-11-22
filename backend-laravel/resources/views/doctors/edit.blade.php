@extends('layouts.app')
@section('title', 'Modifier le médecin · MediCare')
@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Médecins</p>
            <h1>Modifier le médecin</h1>
        </div>
    </div>

    <section class="form-card">
        <form method="POST" action="{{ route('doctors.update', $doctor) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('doctors.form', ['submitLabel' => 'Enregistrer'])
        </form>
    </section>
@endsection
