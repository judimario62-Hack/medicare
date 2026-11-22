<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Ordonnance médicale</title>
    <style>
        body { font-family: Arial, sans-serif; color: #0f172a; margin: 32px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; color: #1d4ed8; }
        .meta { font-size: 12px; color: #475569; }
        .section { margin-top: 18px; }
        .label { font-weight: bold; color: #0f172a; }
        .box { border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; margin-top: 8px; background: #f8fafc; }
        ul { margin: 0; padding-left: 18px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="title">Ordonnance médicale</div>
            <div class="meta">MediCare · Cabinet médical</div>
        </div>
        <div class="meta">
            <div>{{ now()->format('d/m/Y') }}</div>
            <div>{{ $doctor?->name ?? 'Médecin' }}</div>
        </div>
    </div>

    <div class="section">
        <div class="label">Patient</div>
        <div class="box">
            {{ $patient?->last_name ?? '' }} {{ $patient?->first_name ?? '' }}<br>
            {{ $patient?->date_of_birth?->format('d/m/Y') ?? 'Date de naissance non enregistrée' }}
        </div>
    </div>

    <div class="section">
        <div class="label">Diagnostic / évaluation</div>
        <div class="box">
            {{ $summary }}
        </div>
    </div>

    <div class="section">
        <div class="label">Traitement proposé</div>
        <div class="box">
            {{ $plan }}
        </div>
    </div>

    <div class="section">
        <div class="label">Médicaments requis</div>
        <div class="box">
            <ul>
                <li>Antalgique adapté selon la symptomatologie.</li>
                <li>Traitement local ou général selon l’évaluation clinique.</li>
                <li>Suivi de contrôle et surveillance recommandée.</li>
            </ul>
        </div>
    </div>

    <div class="section" style="margin-top:32px; text-align:right;">
        <div class="label">Signature</div>
        <div style="margin-top:30px; border-top:1px solid #cbd5e1; display:inline-block; padding-top:6px; min-width:180px; text-align:center;">
            {{ $doctor?->name ?? 'Médecin' }}
        </div>
    </div>
</body>
</html>
