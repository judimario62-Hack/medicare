@extends('layouts.app')

@section('title', 'Modifier le patient · MediCare')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Patients</p>
            <h1>Modifier le dossier</h1>
        </div>
    </div>

    <section class="form-card">
        <form method="POST" action="{{ auth()->user()->role === 'patient' ? route('patient-record.update') : route('patients.update', $patient) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('patients.form', ['submitLabel' => 'Enregistrer'])
        </form>
    </section>
@endsection
