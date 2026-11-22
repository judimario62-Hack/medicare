<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Secrétariat · MediCare</title>
    <style>
        :root {
            --bg: #fff7ed;
            --panel: rgba(255,255,255,0.96);
            --panel-soft: #fff7ed;
            --primary: #f59e0b;
            --primary-dark: #b45309;
            --secondary: #ea580c;
            --text: #1f2937;
            --muted: #6b7280;
            --line: rgba(245,158,11,0.14);
            --shadow: 0 18px 48px rgba(245,158,11,0.12);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #fff7ed 0%, #fffbeb 35%, #f8fafc 100%);
            color: var(--text);
        }

        .app {
            max-width: 1300px;
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
            background: rgba(245,158,11,0.12);
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
            box-shadow: 0 16px 30px rgba(245,158,11,0.18);
        }

        .grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
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
            grid-template-columns: repeat(4, minmax(0, 1fr));
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

        .item-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .item-actions form { margin: 0; }

        .item-actions .btn {
            padding: 8px 11px;
            font-size: .78rem;
        }

        .btn-details { background: #f1f5f9; color: #334155; }
        .btn-confirm { background: #16a34a; color: #fff; }
        .btn-reject { background: #fee2e2; color: #b91c1c; }

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
        .status.blue { background: rgba(59,130,246,0.12); color: #1d4ed8; }

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
            background: linear-gradient(135deg, #ffffff, #fff7ed);
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

        @media (max-width: 640px) {
            .app { padding: 16px; }
            .item { align-items: flex-start; flex-direction: column; }
            .item-actions { justify-content: flex-start; }
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
                    <small>Secrétariat</small>
                </div>
            </div>
            <div class="topbar-actions">
                <x-account-avatar :name="auth()->user()->name" :path="auth()->user()->avatar_path" :size="42" />
                <span class="chip">Staff</span>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="btn btn-primary" type="submit">Se déconnecter</button>
                </form>
            </div>
        </header>

        <div class="grid">
            <section class="panel">
                <div class="hero">
                    <div>
                        <h2>Bonjour, {{ auth()->user()->name ?? 'Secrétariat' }}</h2>
                        <p>Suivi administratif et organisation des rendez-vous.</p>
                    </div>
                </div>

                <div class="stats">
                    <div class="stat">
                        <span>Rendez-vous aujourd’hui</span>
                        <strong>{{ $appointmentsToday }}</strong>
                    </div>
                    <div class="stat">
                        <span>Patients</span>
                        <strong>{{ $patientsTotal }}</strong>
                    </div>
                    <div class="stat">
                        <span>À confirmer</span>
                        <strong>{{ $pendingCount }}</strong>
                    </div>
                    <div class="stat">
                        <span>Notifications non lues</span>
                        <strong>{{ $unreadAlerts }}</strong>
                    </div>
                </div>

                <div class="list">
                    <h3>Demandes à confirmer</h3>
                    @forelse ($pendingAppointments as $appointment)
                        <div class="item">
                            <div>
                                <strong>{{ $appointment->patient?->first_name }} {{ $appointment->patient?->last_name }}</strong>
                                <small>
                                    {{ $appointment->starts_at?->format('d/m/Y à H:i') ?? 'Date à préciser' }}
                                    · {{ $appointment->doctor?->user?->name ?? 'Médecin non attribué' }}
                                </small>
                            </div>
                            <div class="item-actions">
                                <a class="btn btn-details" href="{{ route('appointments.show', $appointment) }}">Détails</a>
                                @can('confirm', $appointment)
                                    <form method="POST" action="{{ route('appointments.confirm', $appointment) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-confirm" type="submit">Confirmer</button>
                                    </form>
                                    <form method="POST" action="{{ route('appointments.reject', $appointment) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-reject" type="submit">Refuser</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">Aucune demande en attente de confirmation.</div>
                    @endforelse
                    <a class="btn btn-primary" href="{{ route('appointments.pending') }}">Voir toutes les demandes à valider</a>
                </div>
            </section>

            <aside class="panel menu">
                <a href="{{ route('appointments.index') }}">
                    <span>Rendez-vous</span>
                    <span class="meta">Agenda</span>
                </a>
                <a href="{{ route('appointments.pending') }}">
                    <span>Demandes à valider</span>
                    <span class="meta">Validation</span>
                </a>
                <a href="{{ route('patients.index') }}">
                    <span>Patients</span>
                    <span class="meta">Dossiers</span>
                </a>
                <a href="{{ route('rooms.index') }}">
                    <span>Salles</span>
                    <span class="meta">Gestion</span>
                </a>
                <a href="{{ route('notifications.index') }}">
                    <span>Notifications</span>
                    <span class="meta">Alertes</span>
                </a>
            </aside>
        </div>
    </div>
</body>
</html>
