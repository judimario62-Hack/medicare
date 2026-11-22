<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Médecin · MediCare</title>
    <style>
        :root {
            --bg: #ecfdf5;
            --panel: rgba(255,255,255,0.96);
            --panel-soft: #f0fdf4;
            --primary: #15803d;
            --primary-dark: #166534;
            --secondary: #0f766e;
            --text: #0f172a;
            --muted: #475569;
            --line: rgba(22,163,74,0.14);
            --shadow: 0 20px 48px rgba(22,101,52,0.12);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 35%, #f8fafc 100%);
            color: var(--text);
        }

        .app {
            max-width: 1400px;
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

        .brand h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .brand small {
            color: var(--muted);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(21,128,61,0.12);
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
            box-shadow: 0 16px 30px rgba(21,128,61,0.18);
        }

        .btn-secondary {
            background: #fff;
            border: 1px solid var(--line);
            color: var(--text);
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

        .hero {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: end;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .hero h2 {
            margin: 0;
            font-size: clamp(2rem, 3vw, 2.8rem);
        }

        .hero p {
            margin: 8px 0 0;
            color: var(--muted);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 22px;
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

        .item small {
            color: var(--muted);
        }

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
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            text-decoration: none;
            color: var(--text);
            background: linear-gradient(135deg, #ffffff, #f0fdf4);
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

        .right-stack {
            display: grid;
            gap: 22px;
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
                    <small>Interface médecin</small>
                </div>
            </div>
            <div class="topbar-actions">
                <x-account-avatar :name="auth()->user()->name" :path="auth()->user()->avatar_path" :size="42" />
                <span class="chip">Doctor</span>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="btn btn-primary" type="submit">Se déconnecter</button>
                </form>
            </div>
        </header>

        <div class="grid">
            <section class="panel">
                <div class="hero">
                    <div>
                        <h2>Bonjour, Dr. {{ auth()->user()->name ?? 'Médecin' }}</h2>
                        <p>Voici le résumé de votre journée clinique.</p>
                    </div>
                    <a class="btn btn-primary" href="{{ route('appointments.agenda') }}">Agenda</a>
                </div>

                <div class="stats">
                    <div class="stat">
                        <span>Consultations</span>
                        <strong>18</strong>
                    </div>
                    <div class="stat">
                        <span>Patients</span>
                        <strong>126</strong>
                    </div>
                    <div class="stat">
                        <span>Urgences</span>
                        <strong>3</strong>
                    </div>
                </div>

                <div class="list">
                    <div class="item">
                        <div>
                            <strong>Marie Nibona</strong>
                            <small>Suivi post-opératoire · 09:30</small>
                        </div>
                        <span class="status green">Confirmé</span>
                    </div>
                    <div class="item">
                        <div>
                            <strong>Yvan Kamanzi</strong>
                            <small>Contrôle tension · 11:00</small>
                        </div>
                        <span class="status orange">À suivre</span>
                    </div>
                    <div class="item">
                        <div>
                            <strong>Lucie Mugisha</strong>
                            <small>Examen respiratoire · 15:45</small>
                        </div>
                        <span class="status blue">Planifié</span>
                    </div>
                </div>
            </section>

            <aside class="right-stack">
                <div class="panel menu">
                    <a href="{{ route('patients.index') }}">
                        <span>Patients</span>
                        <span class="meta">Dossiers</span>
                    </a>
                    <a href="{{ route('appointments.index') }}">
                        <span>Rendez-vous</span>
                        <span class="meta">Planning</span>
                    </a>
                    <a href="{{ route('assistant.index') }}">
                        <span>Assistant IA</span>
                        <span class="meta">Aide au diagnostic</span>
                    </a>
                    <a href="{{ route('notifications.index') }}">
                        <span>Notifications</span>
                        <span class="meta">Alertes</span>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</body>
</html>
