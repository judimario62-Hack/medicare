<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Patient · MediCare</title>
    <style>
        :root {
            --bg: #f5f3ff;
            --panel: rgba(255,255,255,0.96);
            --panel-soft: #f8f5ff;
            --primary: #7c3aed;
            --primary-dark: #5b21b6;
            --secondary: #ec4899;
            --text: #1f2937;
            --muted: #6b7280;
            --line: rgba(124,58,237,0.14);
            --shadow: 0 18px 48px rgba(124,58,237,0.12);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #f5f3ff 0%, #faf5ff 35%, #f8fafc 100%);
            color: var(--text);
        }

        .app {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 22px;
            padding: 18px 22px;
            box-shadow: var(--shadow);
            margin-bottom: 22px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-mark {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-weight: 800;
            color: white;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        .brand h1 { margin: 0; font-size: 1.5rem; }
        .brand small { color: var(--muted); }

        .chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(124,58,237,0.12);
            color: var(--primary-dark);
            font-weight: 700;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            border: none;
            padding: 11px 18px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 16px 30px rgba(124,58,237,0.18);
        }

        .grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 22px;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 22px;
            box-shadow: var(--shadow);
            padding: 22px;
        }

        .hero h2 {
            margin: 0;
            font-size: clamp(2rem, 3vw, 2.8rem);
        }

        .hero p {
            margin: 10px 0 0;
            color: var(--muted);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin: 22px 0;
        }

        .stat {
            padding: 18px;
            background: var(--panel-soft);
            border: 1px solid var(--line);
            border-radius: 18px;
        }

        .stat span {
            display: block;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .stat strong {
            display: block;
            margin-top: 8px;
            font-size: 2rem;
        }

        .list {
            display: grid;
            gap: 12px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: white;
        }

        .item strong {
            display: block;
            margin-bottom: 4px;
        }

        .item small { color: var(--muted); }

        .status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: .72rem;
            font-weight: 800;
        }

        .status.green { background: rgba(34,197,94,0.12); color: #166534; }
        .status.orange { background: rgba(245,158,11,0.12); color: #92400e; }

        .menu {
            display: grid;
            gap: 12px;
        }

        .menu a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text);
            background: linear-gradient(135deg, #ffffff, #f8f5ff);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 16px;
            font-weight: 700;
        }

        .menu .meta {
            color: var(--muted);
            font-size: .8rem;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
            .stats { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="app">
        <header class="topbar">
            <div class="brand">
                <div class="brand-mark">M</div>
                <div>
                    <h1>MediCare</h1>
                    <small>Mon espace patient</small>
                </div>
            </div>
            <div class="topbar-actions">
                <x-account-avatar :name="auth()->user()->name" :path="auth()->user()->avatar_path" :size="42" />
                <span class="chip">Patient</span>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="btn btn-primary" type="submit">Se déconnecter</button>
                </form>
            </div>
        </header>

        <div class="grid">
            <section class="panel">
                <div class="hero">
                    <div>
                        <h2>Bonjour, {{ auth()->user()->name ?? 'Patient' }}</h2>
                        <p>Voici le suivi de votre santé et vos rendez-vous.</p>
                    </div>
                </div>

                <div class="stats">
                    <div class="stat">
                        <span>Rendez-vous</span>
                        <strong>{{ $appointmentsTotal }}</strong>
                    </div>
                    <div class="stat">
                        <span>Consultations terminées</span>
                        <strong>{{ $completedAppointments }}</strong>
                    </div>
                    <div class="stat">
                        <span>Notifications non lues</span>
                        <strong>{{ $unreadAlerts }}</strong>
                    </div>
                </div>

                <div class="list">
                    <h3>Mes prochains rendez-vous</h3>
                    @forelse ($appointments as $appointment)
                        @php
                            $statusLabel = match ($appointment->status) {
                                'scheduled', 'pending' => 'En attente',
                                'confirmed' => 'Confirmé',
                                'checked_in' => 'Arrivé',
                                'completed' => 'Terminé',
                                'cancelled' => 'Annulé',
                                'no_show' => 'Absent',
                                default => ucfirst($appointment->status),
                            };
                            $statusClass = in_array($appointment->status, ['scheduled', 'pending'], true)
                                ? 'orange'
                                : ($appointment->status === 'confirmed' ? 'green' : 'blue');
                        @endphp
                        <a class="item" href="{{ route('appointments.show', $appointment) }}">
                            <div>
                                <strong>{{ $appointment->doctor?->user?->name ?? 'Médecin' }}</strong>
                                <small>
                                    {{ $appointment->starts_at?->format('d/m/Y à H:i') ?? 'Date à préciser' }}
                                    @if ($appointment->reason) · {{ Str::limit($appointment->reason, 45) }} @endif
                                </small>
                            </div>
                            <span class="status {{ $statusClass }}">{{ $statusLabel }}</span>
                        </a>
                    @empty
                        <div class="item">
                            <div>
                                <strong>Aucun rendez-vous à venir</strong>
                                <small>Vous pouvez envoyer une nouvelle demande au cabinet.</small>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>

            <aside class="panel menu">
                <a href="{{ route('appointments.create') }}">
                    <span>Demander un rendez-vous</span>
                    <span class="meta">Nouveau</span>
                </a>
                <a href="{{ route('patient-record.show') }}">
                    <span>Mon dossier médical</span>
                    <span class="meta">Voir</span>
                </a>
                <a href="{{ route('appointments.index') }}">
                    <span>Mes rendez-vous</span>
                    <span class="meta">Consulter</span>
                </a>
                <a href="{{ route('notifications.index') }}">
                    <span>Notifications</span>
                    <span class="meta">{{ $unreadAlerts }} non lues</span>
                </a>
            </aside>
        </div>
    </div>
</body>
</html>
