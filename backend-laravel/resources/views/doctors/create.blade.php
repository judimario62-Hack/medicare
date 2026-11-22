@extends('layouts.app')
@section('title', 'Nouveau médecin · MediCare')
@section('content')
    <div class="page-header">
        <div>
            <p class="page-kicker">Médecins</p>
            <h1>Nouveau médecin</h1>
        </div>
    </div>

    <section class="form-card">
        <form method="POST" action="{{ route('doctors.store') }}" enctype="multipart/form-data">
            @include('doctors.form', ['submitLabel' => 'Créer le médecin'])
        </form>
    </section>
@endsection
