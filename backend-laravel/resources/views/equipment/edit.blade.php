@extends('layouts.app')
@section('title', 'Modifier l’équipement · MediCare')
@section('content')
<div class="page-header"><div><p class="page-kicker">Équipements</p><h1>Modifier l’équipement</h1></div></div>
<section class="form-card"><form method="POST" action="{{ route('equipment.update', $equipment) }}" enctype="multipart/form-data">@method('PUT')@include('equipment.form', ['submitLabel' => 'Enregistrer'])</form></section>
@endsection
