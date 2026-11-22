@extends('layouts.app')

@section('title', 'Résultat IA · MediCare')

@section('content')
<div class="page-header">
    <div>
        <p class="page-kicker">IA médicale</p>
        <h1>Résultat de l'analyse</h1>
    </div>
    <div class="actions" style="margin-top:0;">
        <a class="button secondary" href="{{ route('assistant.index') }}">← Nouvelle analyse</a>
    </div>
</div>

@if (!empty($result))
    {{-- Boutons d'action --}}
    <div class="ai-actions-bar">
        <a class="button primary" href="{{ route('assistant.prescription.pdf', $request) }}">
            📄 Télécharger PDF
        </a>
        <button class="button secondary" onclick="window.print()">
            🖨️ Imprimer
        </button>
    </div>

    {{-- Aperçu ordonnance format A4 --}}
    <div class="a4-wrapper">
        <div class="a4-page" id="ordonnance">
            {{-- En-tête --}}
            <div class="ord-header">
                <div class="ord-header-left">
                    <div class="ord-logo">M</div>
                    <div>
                        <div class="ord-title">ORDONNANCE MÉDICALE</div>
                        <div class="ord-subtitle">MediCare — Assistant IA clinique</div>
                    </div>
                </div>
                <div class="ord-header-right">
                    <div>{{ now()->format('d/m/Y') }}</div>
                    <div>Réf. ORD-IA-{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>

            {{-- Infos patient / médecin --}}
            <div class="ord-info-row">
                <div class="ord-info-box">
                    <div class="ord-info-label">Patient</div>
                    <div class="ord-info-value">
                        @if ($request->patient)
                            {{ $request->patient->first_name }} {{ $request->patient->last_name }}
                            @if($request->patient->date_of_birth)
                                <span class="ord-info-detail">{{ $request->patient->date_of_birth->age }} ans — Né(e) le {{ $request->patient->date_of_birth->format('d/m/Y') }}</span>
                            @endif
                        @else
                            {{ $request->input_data['patient_name'] ?? 'Non renseigné' }}
                        @endif
                    </div>
                </div>
                <div class="ord-info-box">
                    <div class="ord-info-label">Médecin</div>
                    <div class="ord-info-value">
                        @if ($request->requester)
                            Dr. {{ $request->requester->first_name ?? '' }} {{ $request->requester->last_name ?? '' }}
                        @else
                            Dr. —
                        @endif
                    </div>
                </div>
            </div>

            {{-- Diagnostic --}}
            <div class="ord-section">
                <div class="ord-section-title">Diagnostic</div>
                <div class="ord-section-body">
                    {{ $result['diagnostic'] ?? 'À confirmer par le médecin.' }}
                </div>
            </div>

            {{-- Traitement --}}
            <div class="ord-section">
                <div class="ord-section-title">Traitement</div>
                <div class="ord-section-body">
                    {{ $result['traitement'] ?? 'À adapter selon l\'examen clinique.' }}
                </div>
            </div>

            {{-- Médicaments --}}
            <div class="ord-section">
                <div class="ord-section-title">Prescription</div>
                <div class="ord-section-body">
                    @if (is_array($result['medicaments'] ?? null) && count($result['medicaments']) > 0)
                        <table class="ord-med-table">
                            @foreach ($result['medicaments'] as $i => $med)
                                <tr>
                                    <td class="ord-med-num">{{ $i + 1 }}.</td>
                                    <td>{{ $med }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <p class="ord-empty">Aucun médicament — à compléter par le médecin.</p>
                    @endif
                </div>
            </div>

            {{-- Suivi --}}
            @if (!empty($result['medicaments_suivi']) && is_array($result['medicaments_suivi']))
                <div class="ord-section">
                    <div class="ord-section-title">Suivi médicamenteux</div>
                    <div class="ord-section-body">
                        <ul class="ord-list">
                            @foreach ($result['medicaments_suivi'] as $med)
                                <li>{{ $med }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="ord-section">
                <div class="ord-section-title">Suivi</div>
                <div class="ord-section-body">
                    {{ $result['suivi'] ?? 'Contrôle dans 48-72h.' }}
                </div>
            </div>

            <div class="ord-section ord-section-warn">
                <div class="ord-section-title">Précautions</div>
                <div class="ord-section-body">
                    {{ $result['precautions'] ?? 'Vérifier allergies et interactions.' }}
                </div>
            </div>

            {{-- Avertissement --}}
            <div class="ord-disclaimer">
                ⚕ Cette ordonnance est générée par l'IA MediCare à titre d'aide à la décision.
                Elle doit être validée et signée par un médecin qualifié avant délivrance.
            </div>

            {{-- Signature --}}
            <div class="ord-footer">
                <div class="ord-footer-left">
                    MediCare · {{ now()->format('d/m/Y H:i') }}
                </div>
                <div class="ord-signature">
                    <div class="ord-sig-label">Signature du médecin</div>
                    <div class="ord-sig-line">
                        @if ($request->requester)
                            Dr. {{ $request->requester->first_name ?? '' }} {{ $request->requester->last_name ?? '' }}
                        @else
                            Dr. _______________
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Boutons en bas aussi --}}
    <div class="ai-actions-bar" style="margin-top:1.5rem;">
        <a class="button primary" href="{{ route('assistant.prescription.pdf', $request) }}">
            📄 Télécharger l'ordonnance PDF
        </a>
        <button class="button secondary" onclick="window.print()">
            🖨️ Imprimer l'ordonnance
        </button>
        <a class="button secondary" href="{{ route('assistant.index') }}">
            🔄 Nouvelle analyse
        </a>
    </div>
@else
    <div class="empty-state" style="text-align:center; padding:2rem;">
        <p>❌ Aucune recommandation disponible.</p>
        <a class="button primary" href="{{ route('assistant.index') }}" style="margin-top:1rem;">Nouvelle analyse</a>
    </div>
@endif

<style>
    /* ── Actions bar ── */
    .ai-actions-bar {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    /* ── Conteneur A4 ── */
    .a4-wrapper {
        display: flex;
        justify-content: center;
        padding: 1rem 0;
    }

    .a4-page {
        width: 210mm;
        min-height: 297mm;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        box-shadow: 0 4px 25px rgba(0,0,0,0.1);
        padding: 25mm 20mm 30mm 20mm;
        font-family: 'Segoe UI', Arial, sans-serif;
        font-size: 12px;
        color: #1a1a2e;
        line-height: 1.6;
    }

    /* ── En-tête ordonnance ── */
    .ord-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 2.5px solid #1d4ed8;
        padding-bottom: 14px;
        margin-bottom: 22px;
    }

    .ord-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ord-logo {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #3b82f6, #4f46e5);
        color: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 800;
    }

    .ord-title {
        font-size: 20px;
        font-weight: 800;
        color: #1d4ed8;
        letter-spacing: 0.03em;
    }

    .ord-subtitle {
        font-size: 10px;
        color: #64748b;
    }

    .ord-header-right {
        text-align: right;
        font-size: 11px;
        color: #64748b;
        line-height: 1.6;
    }

    /* ── Infos patient/médecin ── */
    .ord-info-row {
        display: flex;
        gap: 16px;
        margin-bottom: 20px;
    }

    .ord-info-box {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
    }

    .ord-info-label {
        font-size: 9px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 3px;
    }

    .ord-info-value {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }

    .ord-info-detail {
        display: block;
        font-size: 10px;
        font-weight: 400;
        color: #64748b;
        margin-top: 2px;
    }

    /* ── Sections ── */
    .ord-section {
        margin-bottom: 14px;
    }

    .ord-section-title {
        font-size: 11px;
        font-weight: 800;
        color: #1d4ed8;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border-bottom: 1px solid #dbeafe;
        padding-bottom: 4px;
        margin-bottom: 6px;
    }

    .ord-section-body {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #fafbff;
        font-size: 12px;
        color: #334155;
    }

    .ord-section-warn .ord-section-body {
        background: #fffbeb;
        border-color: #fde68a;
    }

    /* ── Tableau médicaments ── */
    .ord-med-table {
        width: 100%;
        border-collapse: collapse;
    }

    .ord-med-table td {
        padding: 6px 8px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 12px;
    }

    .ord-med-table tr:last-child td {
        border-bottom: none;
    }

    .ord-med-num {
        width: 28px;
        font-weight: 700;
        color: #1d4ed8;
    }

    .ord-list {
        margin: 0;
        padding-left: 18px;
    }

    .ord-list li {
        padding: 2px 0;
    }

    .ord-empty {
        margin: 0;
        color: #94a3b8;
        font-style: italic;
    }

    /* ── Disclaimer ── */
    .ord-disclaimer {
        margin-top: 18px;
        padding: 8px 12px;
        border: 1px solid #fbbf24;
        border-radius: 6px;
        background: #fffbeb;
        font-size: 9.5px;
        color: #92400e;
        line-height: 1.5;
    }

    /* ── Footer / Signature ── */
    .ord-footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: 30px;
    }

    .ord-footer-left {
        font-size: 9px;
        color: #94a3b8;
    }

    .ord-signature {
        text-align: center;
    }

    .ord-sig-label {
        font-size: 9px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .ord-sig-line {
        margin-top: 35px;
        border-top: 1px solid #94a3b8;
        padding-top: 5px;
        min-width: 180px;
        font-size: 11px;
        font-weight: 700;
        color: #0f172a;
    }

    /* ── Responsive ── */
    @media (max-width: 900px) {
        .a4-page {
            width: 100%;
            min-height: auto;
            padding: 20px;
            border-radius: 12px;
        }

        .ord-info-row {
            flex-direction: column;
        }
    }

    @media (max-width: 640px) {
        .ai-actions-bar {
            flex-direction: column;
        }

        .ai-actions-bar .button {
            width: 100%;
            text-align: center;
        }
    }

    /* ── Impression : uniquement l'ordonnance A4 ── */
    @media print {
        body {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .shell {
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .top-nav,
        .page-header,
        .ai-actions-bar,
        header,
        nav,
        .status-pill {
            display: none !important;
        }

        .a4-wrapper {
            padding: 0 !important;
        }

        .a4-page {
            width: 100% !important;
            border: none !important;
            box-shadow: none !important;
            padding: 15mm 18mm 20mm 18mm !important;
            margin: 0 !important;
            min-height: auto !important;
            border-radius: 0 !important;
            page-break-inside: avoid;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }
    }
</style>
@endsection
