@extends('layouts.app')

@section('title', 'Assistant IA · MediCare')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap;">
        <div>
            <p style="margin:0 0 .3rem; color:#64748b; font-size:.7rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase;">IA locale</p>
            <h1 style="margin:0; font-size:clamp(2rem,3vw,2.8rem);">Assistant médical Ollama</h1>
        </div>
        <a class="button" href="{{ route('dashboard') }}">Retour au dashboard</a>
    </div>

    <section class="card" style="padding:1.25rem; border-radius:1.2rem; background:#fff; box-shadow:0 18px 40px rgba(15,23,42,.08); border:1px solid rgba(148,163,184,.18);">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
            <div style="padding:1rem; border:1px solid #e2e8f0; border-radius:1rem; background:#f8fafc;">
                <strong>Modèle</strong>
                <div style="margin-top:.4rem; font-size:1.1rem; font-weight:700;">Llama 3.1</div>
                <small style="color:#64748b;">Analyse clinique locale</small>
            </div>
            <div style="padding:1rem; border:1px solid #e2e8f0; border-radius:1rem; background:#f8fafc;">
                <strong>Rôle</strong>
                <div style="margin-top:.4rem; font-size:1.1rem; font-weight:700;">Diagnostic</div>
                <small style="color:#64748b;">Symptômes, signes vitaux et suivi</small>
            </div>
            <div style="padding:1rem; border:1px solid #e2e8f0; border-radius:1rem; background:#f8fafc;">
                <strong>Traitement</strong>
                <div style="margin-top:.4rem; font-size:1.1rem; font-weight:700;">Mistral / Med42</div>
                <small style="color:#64748b;">Médicaments et prescriptions</small>
            </div>
        </div>

        <div style="display:grid; gap:1rem;">
            <div style="padding:1rem; border:1px solid #e2e8f0; border-radius:1rem; background:#f8fafc;">
                <strong>Analyse de symptômes</strong>
                <p style="margin:.7rem 0 0; color:#475569; line-height:1.6;">
                    L’IA locale analyse les plaintes du patient, les données de consultation, les signes vitaux et l’historique du dossier pour proposer une orientation clinique cohérente.
                </p>
            </div>
            <div style="padding:1rem; border:1px solid #e2e8f0; border-radius:1rem; background:#f8fafc;">
                <strong>Recommandation de traitement</strong>
                <p style="margin:.7rem 0 0; color:#475569; line-height:1.6;">
                    Le système peut suggérer des traitements, des médicaments requis, les contre-indications à vérifier et le plan de suivi à intégrer dans l’ordonnance médicale.
                </p>
            </div>
            <div style="padding:1rem; border:1px solid #e2e8f0; border-radius:1rem; background:#f8fafc;">
                <strong>Ordonnance PDF</strong>
                <p style="margin:.7rem 0 0; color:#475569; line-height:1.6;">
                    En consultation, le médecin peut générer une ordonnance au format PDF à partir de l’évaluation et du plan de traitement enregistré dans le dossier du patient.
                </p>
            </div>
        </div>
    </section>
@endsection
