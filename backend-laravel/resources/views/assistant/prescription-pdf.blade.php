<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Ordonnance médicale — MediCare</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 18mm 25mm 18mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            color: #1a1a2e;
            font-size: 11.5px;
            line-height: 1.55;
        }

        /* ── En-tête ── */
        .header {
            border-bottom: 2.5px solid #1d4ed8;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header table {
            width: 100%;
        }

        .header td {
            vertical-align: top;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1d4ed8;
            letter-spacing: 0.5px;
        }

        .subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        .header-meta {
            text-align: right;
            font-size: 10.5px;
            color: #64748b;
            line-height: 1.6;
        }

        /* ── Bloc Patient / Médecin ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .info-table td {
            width: 50%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            vertical-align: top;
        }

        .info-label {
            font-size: 9px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 12.5px;
            font-weight: bold;
            color: #0f172a;
        }

        .info-detail {
            font-size: 10px;
            color: #64748b;
            font-weight: normal;
            margin-top: 2px;
        }

        /* ── Sections ── */
        .section {
            margin-bottom: 14px;
        }

        .section-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #1d4ed8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #dbeafe;
            padding-bottom: 3px;
            margin-bottom: 6px;
        }

        .section-body {
            padding: 8px 11px;
            border: 1px solid #e2e8f0;
            background: #fafbff;
            font-size: 11.5px;
            color: #334155;
        }

        .section-warn .section-body {
            background: #fffbeb;
            border-color: #fde68a;
        }

        /* ── Tableau médicaments ── */
        .med-table {
            width: 100%;
            border-collapse: collapse;
        }

        .med-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11.5px;
        }

        .med-table tr:last-child td {
            border-bottom: none;
        }

        .med-num {
            width: 25px;
            font-weight: bold;
            color: #1d4ed8;
        }

        ul.med-list {
            margin: 0;
            padding-left: 16px;
        }

        ul.med-list li {
            padding: 2px 0;
            font-size: 11.5px;
        }

        /* ── Avertissement ── */
        .warning {
            margin-top: 16px;
            padding: 7px 10px;
            border: 1px solid #fbbf24;
            background: #fffbeb;
            font-size: 9px;
            color: #92400e;
            line-height: 1.4;
        }

        /* ── Signature ── */
        .footer {
            margin-top: 30px;
        }

        .footer table {
            width: 100%;
        }

        .footer td {
            vertical-align: bottom;
        }

        .footer-left {
            font-size: 9px;
            color: #94a3b8;
        }

        .footer-right {
            text-align: right;
        }

        .sig-label {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sig-line {
            margin-top: 35px;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            display: inline-block;
            min-width: 170px;
            text-align: center;
        }
    </style>
</head>
<body>
    {{-- En-tête --}}
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="title">ORDONNANCE MÉDICALE</div>
                    <div class="subtitle">MediCare — Assistant IA clinique</div>
                </td>
                <td class="header-meta">
                    Date : {{ now()->format('d/m/Y') }}<br>
                    Heure : {{ now()->format('H:i') }}<br>
                    Réf. : ORD-IA-{{ str_pad($aiRequest->id ?? 0, 5, '0', STR_PAD_LEFT) }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Patient / Médecin --}}
    <table class="info-table">
        <tr>
            <td>
                <div class="info-label">Patient</div>
                <div class="info-value">{{ $patientName ?? 'Patient' }}</div>
                @if ($aiRequest->patient && $aiRequest->patient->date_of_birth)
                    <div class="info-detail">
                        {{ $aiRequest->patient->date_of_birth->age }} ans — Né(e) le {{ $aiRequest->patient->date_of_birth->format('d/m/Y') }}
                    </div>
                @endif
            </td>
            <td>
                <div class="info-label">Médecin prescripteur</div>
                <div class="info-value">Dr. {{ $doctorName ?? 'Médecin' }}</div>
            </td>
        </tr>
    </table>

    {{-- Diagnostic --}}
    <div class="section">
        <div class="section-title">Diagnostic</div>
        <div class="section-body">
            {{ $result['diagnostic'] ?? 'Diagnostic clinique à confirmer par le médecin.' }}
        </div>
    </div>

    {{-- Traitement --}}
    <div class="section">
        <div class="section-title">Traitement recommandé</div>
        <div class="section-body">
            {{ $result['traitement'] ?? 'Traitement à adapter selon l\'examen clinique final.' }}
        </div>
    </div>

    {{-- Prescription --}}
    <div class="section">
        <div class="section-title">Prescription médicamenteuse</div>
        <div class="section-body">
            @if (is_array($result['medicaments'] ?? null) && count($result['medicaments']) > 0)
                <table class="med-table">
                    @foreach ($result['medicaments'] as $index => $medicament)
                        <tr>
                            <td class="med-num">{{ $index + 1 }}.</td>
                            <td>{{ $medicament }}</td>
                        </tr>
                    @endforeach
                </table>
            @else
                Aucun médicament spécifié — à compléter par le médecin.
            @endif
        </div>
    </div>

    {{-- Médicaments de suivi --}}
    @if (is_array($result['medicaments_suivi'] ?? null) && count($result['medicaments_suivi']) > 0)
        <div class="section">
            <div class="section-title">Médicaments de suivi</div>
            <div class="section-body">
                <ul class="med-list">
                    @foreach ($result['medicaments_suivi'] as $med)
                        <li>{{ $med }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Suivi --}}
    <div class="section">
        <div class="section-title">Suivi recommandé</div>
        <div class="section-body">
            {{ $result['suivi'] ?? 'Contrôler la réponse thérapeutique dans 48 à 72 heures.' }}
        </div>
    </div>

    {{-- Précautions --}}
    <div class="section section-warn">
        <div class="section-title">Précautions</div>
        <div class="section-body">
            {{ $result['precautions'] ?? 'Vérifier les allergies et interactions avant prescription finale.' }}
        </div>
    </div>

    {{-- Avertissement --}}
    <div class="warning">
        <strong>⚕ Avertissement :</strong> Cette ordonnance a été générée par l'assistant IA MediCare.
        Elle constitue une aide à la décision et doit être validée et signée par un médecin qualifié
        avant toute délivrance de médicaments. Le médecin reste seul responsable de la prescription.
    </div>

    {{-- Signature --}}
    <div class="footer">
        <table>
            <tr>
                <td class="footer-left">
                    MediCare — Système de gestion médicale<br>
                    Document généré le {{ now()->format('d/m/Y') }} à {{ now()->format('H:i') }}
                </td>
                <td class="footer-right">
                    <div class="sig-label">Signature du médecin</div>
                    <div class="sig-line">
                        Dr. {{ $doctorName ?? 'Médecin' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
