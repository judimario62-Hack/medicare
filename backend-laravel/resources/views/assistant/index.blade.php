@extends('layouts.app')

@section('title', 'Assistant IA · MediCare')

@section('content')
<div class="ai-page">
    <div class="page-header ai-page-header">
        <div>
            <p class="page-kicker">IA médicale</p>
            <h1>Assistant IA</h1>
            <p class="page-subtitle">Rassemblez les informations utiles pour préparer une analyse clinique.</p>
        </div>
        <a class="button secondary" href="{{ route('dashboard') }}">← Dashboard</a>
    </div>

    @if ($errors->any())
        <div class="ai-alert ai-alert-error" role="alert">
            <strong>⚠ Vérifiez les informations :</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card form-card ai-form-card">
        <div class="ai-form-intro">
            <div class="ai-intro-icon" aria-hidden="true">✦</div>
            <div>
                <strong>Préparer une analyse</strong>
                <p>L’assistant fournit une aide à la décision. Les résultats doivent être vérifiés par un professionnel de santé.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('assistant.generate') }}" id="aiForm">
            @csrf

            <section class="ai-form-section" aria-labelledby="ai-patient-heading">
                <div class="ai-section-heading">
                    <span class="ai-section-number">01</span>
                    <div>
                        <h2 id="ai-patient-heading">Informations du patient</h2>
                        <p>Choisissez un patient enregistré ou saisissez son nom.</p>
                    </div>
                </div>

                <div class="ai-form-grid">
                    <div class="ai-field-group">
                        <label for="patient_id">Patient enregistré</label>
                        <select id="patient_id" name="patient_id">
                            <option value="">— Sélectionner un patient —</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                    @if($patient->date_of_birth) ({{ $patient->date_of_birth->age }} ans) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="ai-field-group">
                        <label for="patient_name">Nom du patient</label>
                        <input id="patient_name" name="patient_name" type="text" value="{{ old('patient_name') }}" placeholder="Nom complet du patient" />
                    </div>
                </div>
            </section>

            <section class="ai-form-section" aria-labelledby="ai-context-heading">
                <div class="ai-section-heading">
                    <span class="ai-section-number">02</span>
                    <div>
                        <h2 id="ai-context-heading">Motif et contexte clinique</h2>
                        <p>Décrivez les symptômes et les informations médicales pertinentes.</p>
                    </div>
                </div>

                <div class="ai-form-grid">
                    <div class="ai-field-group ai-field-full">
                        <label for="symptoms">Symptômes <span class="ai-required">*</span></label>
                        <textarea id="symptoms" name="symptoms" rows="3" placeholder="Fièvre, toux, douleur abdominale, fatigue..." required>{{ old('symptoms') }}</textarea>
                    </div>

                    <div class="ai-field-group ai-field-full">
                        <label for="history">Antécédents / maladies</label>
                        <textarea id="history" name="history" rows="2" placeholder="Diabète, hypertension, asthme...">{{ old('history') }}</textarea>
                    </div>

                    <div class="ai-field-group">
                        <label for="allergies">Allergies</label>
                        <input id="allergies" name="allergies" type="text" value="{{ old('allergies') }}" placeholder="Pénicilline, aspirine..." />
                    </div>

                    <div class="ai-field-group">
                        <label for="medications">Médicaments actuels</label>
                        <input id="medications" name="medications" type="text" value="{{ old('medications') }}" placeholder="Paracétamol, Amoxicilline..." />
                    </div>
                </div>
            </section>

            <section class="ai-form-section" aria-labelledby="ai-exam-heading">
                <div class="ai-section-heading">
                    <span class="ai-section-number">03</span>
                    <div>
                        <h2 id="ai-exam-heading">Examen et objectif</h2>
                        <p>Ajoutez les mesures disponibles et précisez l’objectif de l’analyse.</p>
                    </div>
                </div>

                <div class="ai-form-grid">
                    <div class="ai-field-group">
                        <label for="vitals">Signes vitaux</label>
                        <input id="vitals" name="vitals" type="text" value="{{ old('vitals') }}" placeholder="TA : 120/80, FC : 78, T° : 37,2 °C, SpO₂ : 98 %" />
                    </div>

                    <div class="ai-field-group">
                        <label for="diagnostic_goal">Objectif</label>
                        <input id="diagnostic_goal" name="diagnostic_goal" type="text" value="{{ old('diagnostic_goal') }}" placeholder="Ex. : orienter l’évaluation clinique" />
                    </div>

                    <div class="ai-field-group ai-field-full">
                        <label for="exam">Examen clinique</label>
                        <textarea id="exam" name="exam" rows="3" placeholder="Observations, palpation, auscultation...">{{ old('exam') }}</textarea>
                    </div>
                </div>
            </section>

            <div class="ai-submit">
                <button type="submit" class="button primary ai-submit-btn" id="submitBtn" aria-live="polite">
                    ✨ Générer l’analyse
                </button>
                <p>Les champs marqués d’un <span class="ai-required">*</span> sont obligatoires.</p>
            </div>
        </form>
    </div>
</div>

<style>
    .ai-page {
        width: min(100%, 980px);
        margin: 0 auto;
    }

    .ai-page-header {
        align-items: flex-start;
    }

    .ai-form-card {
        max-width: 900px;
        margin: 0 auto;
        padding: clamp(1.1rem, 3.5vw, 2.4rem);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 26px;
        box-shadow: 0 24px 60px rgba(30, 64, 175, 0.1);
    }

    .ai-form-intro {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 1.1rem 1.25rem;
        border: 1px solid #dbeafe;
        border-radius: 18px;
        background: linear-gradient(120deg, #eff6ff, #f5f3ff);
    }

    .ai-intro-icon {
        display: grid;
        flex: 0 0 2.8rem;
        width: 2.8rem;
        height: 2.8rem;
        place-items: center;
        border-radius: 14px;
        background: linear-gradient(135deg, #2563eb, #6366f1);
        color: #fff;
        font-size: 1.4rem;
    }

    .ai-form-intro strong {
        display: block;
        margin-bottom: 0.2rem;
        color: #1e3a8a;
    }

    .ai-form-intro p,
    .ai-section-heading p {
        margin: 0;
        color: #64748b;
        font-size: 0.88rem;
        line-height: 1.55;
    }

    .ai-form-section + .ai-form-section {
        margin-top: 2rem;
        padding-top: 1.8rem;
        border-top: 1px solid #e8eef7;
    }

    .ai-section-heading {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        margin-bottom: 1.2rem;
    }

    .ai-section-number {
        display: grid;
        flex: 0 0 2.3rem;
        width: 2.3rem;
        height: 2.3rem;
        place-items: center;
        border-radius: 12px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 0.78rem;
        font-weight: 800;
    }

    .ai-section-heading h2 {
        margin: 0 0 0.2rem;
        color: #0f172a;
        font-size: 1.05rem;
        letter-spacing: -0.02em;
    }

    .ai-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.1rem 1.2rem;
    }

    .ai-field-full {
        grid-column: 1 / -1;
    }

    .ai-field-group {
        min-width: 0;
    }

    .ai-field-group label {
        display: block;
        margin-bottom: 0.45rem;
        color: #334155;
        font-size: 0.83rem;
        font-weight: 700;
    }

    .ai-field-group input,
    .ai-field-group select,
    .ai-field-group textarea {
        display: block;
        width: 100%;
        min-height: 48px;
        border: 1px solid #dbe4f0;
        border-radius: 13px;
        background: #fbfdff;
        color: #0f172a;
        padding: 0.75rem 0.9rem;
        font: inherit;
        font-size: 0.92rem;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .ai-field-group input::placeholder,
    .ai-field-group textarea::placeholder {
        color: #94a3b8;
    }

    .ai-field-group input:focus,
    .ai-field-group select:focus,
    .ai-field-group textarea:focus {
        border-color: #6366f1;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
    }

    .ai-field-group input:read-only {
        background: #f1f5f9;
        color: #64748b;
        cursor: not-allowed;
    }

    .ai-field-group textarea {
        min-height: 96px;
        resize: vertical;
    }

    .ai-required {
        color: #dc2626;
    }

    .ai-submit {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.7rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e8eef7;
        text-align: center;
    }

    .ai-submit-btn {
        width: min(100%, 340px);
        min-height: 52px;
        border-radius: 14px;
        font-size: 1rem;
    }

    .ai-submit-btn:disabled {
        cursor: wait;
        opacity: 0.7;
        transform: none;
    }

    .ai-submit p {
        margin: 0;
        color: #64748b;
        font-size: 0.78rem;
    }

    .ai-alert {
        max-width: 900px;
        margin: 0 auto 1rem;
        padding: 1rem 1.2rem;
        border-radius: 14px;
        font-size: 0.88rem;
    }

    .ai-alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }

    .ai-alert ul {
        margin: 0.4rem 0 0;
        padding-left: 1.2rem;
    }

    @media (max-width: 640px) {
        .ai-page-header {
            align-items: stretch;
        }

        .ai-page-header > .button {
            align-self: flex-start;
        }

        .ai-form-card {
            padding: 1rem;
            border-radius: 20px;
        }

        .ai-form-intro {
            align-items: flex-start;
            padding: 1rem;
        }

        .ai-form-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .ai-field-full {
            grid-column: auto;
        }

        .ai-form-section + .ai-form-section {
            margin-top: 1.5rem;
            padding-top: 1.4rem;
        }
    }
</style>

<script>
    document.getElementById('patient_id').addEventListener('change', function() {
        const nameInput = document.getElementById('patient_name');
        if (this.value) {
            nameInput.value = this.options[this.selectedIndex].text.split('(')[0].trim();
            nameInput.readOnly = true;
            nameInput.style.opacity = '0.5';
        } else {
            nameInput.value = '';
            nameInput.readOnly = false;
            nameInput.style.opacity = '1';
        }
    });

    document.getElementById('aiForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.textContent = '⏳ Analyse en cours…';
        btn.disabled = true;
    });
</script>
@endsection
