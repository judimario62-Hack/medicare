@extends('layouts.app')
@section('title', 'Nouvel équipement · MediCare')
@section('content')
<div class="page-header"><div><p class="page-kicker">Équipements</p><h1>Ajouter un équipement</h1></div></div>
<section class="form-card"><form method="POST" action="{{ route('equipment.store') }}" enctype="multipart/form-data">@include('equipment.form', ['submitLabel' => 'Ajouter'])</form></section>
@endsection
