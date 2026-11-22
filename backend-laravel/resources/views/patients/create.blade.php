@extends('layouts.app')

@section('title', 'Nouveau patient · MediCare')

@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Patients</p>
            <h1>Nouveau patient</h1>
        </div>
    </div>

    <section class="form-card">
        <form method="POST" action="{{ route('patients.store') }}" enctype="multipart/form-data">
            @include('patients.form', ['submitLabel' => 'Créer le dossier'])
        </form>
    </section>
@endsection
