<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MediCare')</title>
    <style>
        .account-avatar {
            width: var(--avatar-size, 44px);
            height: var(--avatar-size, 44px);
            display: inline-grid;
            place-items: center;
            flex: 0 0 var(--avatar-size, 44px);
            border-radius: 50%;
            overflow: hidden;
            background: linear-gradient(135deg, #1d4ed8, #7c3aed);
            color: #fff;
            font-size: calc(var(--avatar-size, 44px) * 0.34);
            font-weight: 800;
            letter-spacing: 0.02em;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.14);
        }
        .account-avatar img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        :root {
            --bg: #f4f7fb;
            --bg-strong: #ebf2ff;
            --panel: rgba(255,255,255,0.85);
            --panel-strong: #ffffff;
            --line: #dfeaf8;
            --text: #0f172a;
            --muted: #64748b;
            --primary: #1d4ed8;
            --primary-soft: rgba(29, 78, 216, 0.12);
            --success: #16a34a;
            --success-soft: rgba(22, 163, 74, 0.12);
            --warning: #f59e0b;
            --danger: #ef4444;
            --shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59,130,246,0.12), transparent 25%),
                linear-gradient(180deg, #f8fbff 0%, #edf4ff 100%);
            color: var(--text);
        }

        a { color: inherit; text-decoration: none; }
        button, select, input { font: inherit; }

        .shell {
            width: min(1400px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 1.2rem 0 2.5rem;
        }

        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.2rem;
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 22px;
            box-shadow: var(--shadow);
            color: white;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .brand-mark {
            display: grid;
            place-items: center;
            width: 2.2rem;
            height: 2.2rem;
            border-radius: 12px;
            background: linear-gradient(135deg, #60a5fa, #4f46e5);
            color: white;
            font-size: 0.9rem;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .nav-link {
            color: rgba(255,255,255,0.82);
            padding: 0.55rem 0.8rem;
            border-radius: 10px;
            transition: 0.2s ease;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }

        .logout {
            background: white;
            color: #0f172a;
            border: none;
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            font-weight: 600;
            cursor: pointer;
        }

        .dashboard-shell {
            padding-top: 1.8rem;
        }

        .topbar,
        .panel,
        .kpi-card {
            background: var(--panel);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(148, 163, 184, 0.15);
            box-shadow: var(--shadow);
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1.3rem 1.4rem;
            border-radius: 24px;
        }

        .eyebrow {
            margin: 0 0 0.35rem;
            color: var(--muted);
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        h1, h2, h3, p {
            margin-top: 0;
        }

        h1 {
            margin-bottom: 0;
            font-size: clamp(1.8rem, 2.2vw, 2.8rem);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .page-header h1 {
            margin: 0;
            letter-spacing: -0.05em;
        }

        .page-kicker {
            margin: 0 0 0.3rem;
            color: var(--muted);
            font-size: 0.73rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .page-subtitle {
            margin: 0.35rem 0 0;
            color: var(--muted);
            font-size: 0.96rem;
        }

        .button,
        button[type="submit"],
        .button.secondary,
        .button.danger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
            border-radius: 12px;
            padding: 0.72rem 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
            text-decoration: none;
        }

        .button,
        button[type="submit"] {
            background: linear-gradient(135deg, #1d4ed8, #4f46e5);
            color: white;
            box-shadow: 0 12px 22px rgba(79, 70, 229, 0.24);
        }

        .button.secondary {
            background: #edf2ff;
            color: #1e3a8a;
        }

        .button.danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .button:hover,
        button[type="submit"]:hover,
        .button.secondary:hover,
        .button.danger:hover {
            transform: translateY(-1px);
        }

        .card,
        .form-card,
        .detail-card {
            background: rgba(255,255,255,0.9);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 22px;
            box-shadow: var(--shadow);
        }

        .card {
            padding: 1.25rem;
        }

        .detail-card {
            padding: 1.3rem;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 0.38rem 0.7rem;
            font-size: 0.72rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-pill.success,
        .status-pill.active {
            background: rgba(34,197,94,0.12);
            color: #15803d;
        }

        .status-pill.warning,
        .status-pill.inactive {
            background: rgba(245,158,11,0.12);
            color: #b45309;
        }

        .status-pill.info {
            background: rgba(59,130,246,0.12);
            color: #1d4ed8;
        }

        .status-pill.danger {
            background: rgba(239,68,68,0.12);
            color: #b91c1c;
        }

        .status-pill.muted {
            background: rgba(100,116,139,0.12);
            color: #475569;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.2rem;
        }

        .stat-box {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 1rem 1.1rem;
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(255,255,255,0.75);
            box-shadow: var(--shadow);
        }

        .stat-box span {
            color: var(--muted);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .stat-box strong {
            font-size: clamp(1.5rem, 1.8vw, 2rem);
            letter-spacing: -0.05em;
        }

        .table-shell {
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            background: white;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 0.95rem 0.9rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .data-table th {
            color: #475569;
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: #f8fafc;
        }

        .data-table tbody tr:hover {
            background: #f8fafc;
        }

        .table-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .empty-state {
            padding: 1.2rem;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 1rem;
            color: #475569;
        }

        .form-card {
            padding: 1.4rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.1rem;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .field.full { grid-column: 1 / -1; }
        .field.full-width { grid-column: 1 / -1; }

        .field label {
            color: #334155;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            background: #f8fbff;
            color: var(--text);
            padding: 0.8rem 0.9rem;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: rgba(79,70,229,0.8);
            box-shadow: 0 0 0 4px rgba(79,70,229,0.08);
        }

        .field textarea {
            min-height: 130px;
            resize: vertical;
        }

        .field input[type="checkbox"] {
            width: auto;
            accent-color: #1d4ed8;
            transform: scale(1.1);
        }

        .checkbox-field {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            min-height: 49px;
            padding: 0.75rem 0.9rem;
            border-radius: 12px;
            border: 1px solid #dbeafe;
            background: #f8fbff;
            color: #334155;
            font-weight: 600;
        }

        .error {
            margin: 0;
            color: #b91c1c;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1.2rem;
        }

        .detail-item {
            padding: 1rem 1.1rem;
            border-radius: 16px;
            background: rgba(148,163,184,0.04);
            border: 1px solid rgba(148,163,184,0.12);
        }

        .detail-item span {
            display: block;
            color: var(--muted);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.45rem;
        }

        .detail-item strong,
        .detail-item p {
            margin: 0;
            line-height: 1.5;
        }

        @media (max-width: 1024px) {
            .kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .main-grid,
            .secondary-grid,
            .third-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .form-grid,
            .detail-grid,
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .data-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }

        @media (max-width: 640px) {
            .shell { width: min(100% - 1rem, 1400px); }
            .top-nav,
            .topbar,
            .panel-header,
            .patient-item,
            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            .nav-actions { justify-content: space-between; }
            .kpi-grid { grid-template-columns: 1fr; }
            .mini-metrics { grid-template-columns: 1fr; }
            .page-header {
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="top-nav">
            <div class="brand">
                <span class="brand-mark">M</span>
                <span>MediCare</span>
            </div>
            <nav class="nav-actions">
                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>

                @if (auth()->user()->role === 'patient')
                    <a class="nav-link" href="{{ route('patient-record.show') }}">Mon dossier</a>
                    <a class="nav-link" href="{{ route('appointments.index') }}">Mes rendez-vous</a>
                @endif

                @if (in_array(auth()->user()->role, ['admin', 'doctor', 'staff'], true))
                    <a class="nav-link" href="{{ route('patients.index') }}">Patients</a>
                    <a class="nav-link" href="{{ route('appointments.index') }}">Rendez-vous</a>
                    <a class="nav-link" href="{{ route('assistant.index') }}">🤖 Assistant IA</a>
                    <a class="nav-link" href="{{ route('notifications.index') }}">Notifications</a>
                @endif

                @if (auth()->user()->role === 'doctor')
                    <a class="nav-link" href="{{ route('appointments.agenda') }}">Agenda</a>
                @endif

                @if (auth()->user()->role === 'admin')
                    <a class="nav-link" href="{{ route('doctors.index') }}">Médecins</a>
                    <a class="nav-link" href="{{ route('rooms.index') }}">Salles</a>
                    <a class="nav-link" href="{{ route('equipment.index') }}">Équipements</a>
                @endif

                <x-account-avatar :name="auth()->user()->name" :path="auth()->user()->avatar_path" :size="38" />
                <form method="POST" action="{{ route('logout') }}">@csrf <button class="logout" type="submit">Se déconnecter</button></form>
            </nav>
        </header>

        <main>
            @if (session('status'))
                <div class="status-pill">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
