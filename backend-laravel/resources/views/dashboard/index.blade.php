<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tableau de bord · MediCare</title>
    <style>
        :root {
            --bg: #eef5ff;
            --surface: rgba(255,255,255,0.8);
            --surface-strong: #ffffff;
            --surface-soft: #f8fafc;
            --sidebar: #0b1220;
            --sidebar-2: #101d33;
            --sidebar-text: #e2e8f0;
            --primary: #2563eb;
            --primary-2: #0f766e;
            --accent: #4f46e5;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --line: rgba(148,163,184,0.22);
            --text: #0f172a;
            --muted: #64748b;
            --shadow: 0 18px 38px rgba(15, 23, 42, 0.12);
        }

        body.dark {
            --bg: #07111d;
            --surface: rgba(15,23,42,0.82);
            --surface-strong: #0f172a;
            --surface-soft: #0e1726;
            --sidebar: #050d18;
            --sidebar-2: #0b1727;
            --sidebar-text: #e2e8f0;
            --primary: #60a5fa;
            --primary-2: #2dd4bf;
            --accent: #818cf8;
            --success: #4ade80;
            --warning: #fbbf24;
            --danger: #f87171;
            --line: rgba(148,163,184,0.18);
            --text: #e2e8f0;
            --muted: #94a3b8;
            --shadow: 0 22px 48px rgba(2, 6, 23, 0.58);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(180deg, #f6f9ff 0%, var(--bg) 100%);
            color: var(--text);
        }
        body.dark { background: linear-gradient(180deg, #06111d 0%, var(--bg) 100%); }

        a { color: inherit; text-decoration: none; }
        button, select { font: inherit; }
        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            position: sticky;
            top: 0;
            width: 270px;
            min-height: 100vh;
            padding: 1.1rem .9rem;
            background: linear-gradient(180deg, var(--sidebar), var(--sidebar-2));
            color: var(--sidebar-text);
            border-right: 1px solid var(--line);
        }

        .brand-badge {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .85rem .9rem;
            border-radius: 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 1.3rem;
            box-shadow: 0 18px 34px rgba(79,70,229,0.14);
        }

        .brand-mark {
            display: grid;
            place-items: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: .9rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
        }

        .brand-copy strong { display: block; }
        .brand-copy small { color: rgba(255,255,255,0.7); }

        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: .45rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .78rem .8rem;
            border-radius: .9rem;
            color: var(--sidebar-text);
            opacity: .8;
            transition: .2s ease;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,0.06);
            opacity: 1;
        }

        .nav-item svg { width: 1rem; height: 1rem; }

        .nav-section {
            margin-top: 1.1rem;
            margin-bottom: .6rem;
            padding: 0 .45rem;
            color: rgba(255,255,255,0.58);
            font-size: .67rem;
            text-transform: uppercase;
            letter-spacing: .12em;
            font-weight: 700;
        }

        .ai-button {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            width: 100%;
            margin-top: .4rem;
            border: 1px solid rgba(52,211,153,0.45);
            background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(59,130,246,0.12));
            color: white;
            border-radius: .9rem;
            padding: .8rem .85rem;
            font-weight: 700;
            cursor: pointer;
        }

        .ai-button small {
            display: block;
            color: rgba(255,255,255,0.7);
            font-weight: 600;
        }

        .ai-button .spark {
            display: grid;
            place-items: center;
            width: 2.1rem;
            height: 2.1rem;
            border-radius: .75rem;
            background: rgba(255,255,255,0.12);
        }

        .ai-button svg { width: 1.1rem; height: 1.1rem; }

        .sidebar-footer {
            position: absolute;
            left: 1rem;
            right: 1rem;
            bottom: 1rem;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .8rem .75rem;
            border-radius: 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.07);
        }

        .user-avatar {
            display: grid;
            place-items: center;
            width: 2.6rem;
            height: 2.6rem;
            border-radius: 50%;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
        }

        .user-meta strong { display: block; }
        .user-meta small { color: rgba(255,255,255,0.7); text-transform: capitalize; }

        .main-panel {
            flex: 1;
            min-width: 0;
            padding: 1.2rem 1.2rem 2rem;
        }

        .topbar-shell {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.2rem;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 1.2rem;
            box-shadow: var(--shadow);
            backdrop-filter: blur(14px);
        }

        .topbar-title {
            display: flex;
            align-items: center;
            gap: .8rem;
            font-weight: 700;
        }

        .dot {
            width: .7rem;
            height: .7rem;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--success), var(--primary));
            box-shadow: 0 0 0 6px rgba(34,197,94,0.12);
        }

        .topbar-tools {
            display: flex;
            align-items: center;
            gap: .8rem;
        }

        .theme-toggle,
        .logout-btn {
            border: none;
            border-radius: .9rem;
            padding: .7rem .9rem;
            font-weight: 700;
            cursor: pointer;
        }

        .theme-toggle {
            background: var(--surface-strong);
            color: var(--text);
            border: 1px solid var(--line);
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            box-shadow: 0 12px 24px rgba(79,70,229,0.24);
        }

        .hero-title {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.3rem;
        }

        .eyebrow {
            margin: 0 0 .35rem;
            color: var(--muted);
            font-size: .72rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            font-weight: 700;
        }

        h1 {
            margin: 0;
            font-size: clamp(2rem, 3vw, 2.8rem);
        }

        .role-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .45rem .7rem;
            border-radius: 999px;
            font-size: .72rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-weight: 800;
            background: rgba(37,99,235,0.12);
            color: var(--primary);
        }

        .role-admin .role-pill { background: rgba(37,99,235,0.14); color: #2563eb; }
        .role-doctor .role-pill { background: rgba(34,197,94,0.12); color: #15803d; }
        .role-patient .role-pill { background: rgba(168,85,247,0.14); color: #7c3aed; }
        .role-staff .role-pill { background: rgba(245,158,11,0.12); color: #b45309; }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .9rem;
            margin-top: 1.3rem;
        }

        .hero-action {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: .7rem;
            padding: .9rem 1.15rem;
            border-radius: 1rem;
            color: white;
            text-decoration: none;
            font-weight: 800;
            box-shadow: 0 18px 40px rgba(79, 70, 229, 0.25);
            transition: transform .22s ease, box-shadow .22s ease, filter .22s ease;
            overflow: hidden;
        }

        .hero-action::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.18), rgba(255,255,255,0));
            pointer-events: none;
        }

        .hero-action:hover {
            transform: translateY(-2px) scale(1.01);
            filter: brightness(1.04);
        }

        .hero-action.primary { background: linear-gradient(135deg, var(--primary-2), var(--primary)); }
        .hero-action.secondary { background: linear-gradient(135deg, #1d4ed8, #4f46e5); }
        .hero-action.success { background: linear-gradient(135deg, #059669, #10b981); }
        .hero-action.warning { background: linear-gradient(135deg, #d97706, #f59e0b); }
        .hero-action.purple { background: linear-gradient(135deg, #7c3aed, #a855f7); }

        .hero-action svg {
            width: 1.05rem;
            height: 1.05rem;
            z-index: 1;
        }

        .hero-action span { position: relative; z-index: 1; }

        .management-panel {
            margin-top: 1.4rem;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.96), rgba(30, 64, 175, 0.92));
            border-radius: 1.25rem;
            padding: 1rem;
            box-shadow: 0 22px 42px rgba(15, 23, 42, 0.16);
        }

        .management-panel-header {
            margin-bottom: 1rem;
            color: white;
        }

        .management-subtitle {
            margin: 0;
            font-size: .72rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.7);
        }

        .management-panel-header h2 {
            margin: .25rem 0 0;
            font-size: 1.2rem;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: .9rem;
        }

        .action-card {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: .7rem;
            padding: 1rem;
            border-radius: 1rem;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            transition: transform .22s ease, background .22s ease, box-shadow .22s ease;
        }

        .action-card:hover {
            transform: translateY(-3px);
            background: rgba(255,255,255,0.14);
            box-shadow: 0 18px 32px rgba(59,130,246,0.2);
        }

        .action-icon {
            display: grid;
            place-items: center;
            width: 2.7rem;
            height: 2.7rem;
            border-radius: .8rem;
            background: rgba(255,255,255,0.12);
        }

        .action-icon svg { width: 1.15rem; height: 1.15rem; }
        .action-title { font-weight: 800; font-size: 1rem; }
        .action-caption { color: rgba(255,255,255,0.75); font-size: .76rem; line-height: 1.4; }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .kpi-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 1rem;
            padding: 1rem 1.1rem;
            box-shadow: var(--shadow);
        }

        .kpi-meta {
            display: flex;
            justify-content: space-between;
            gap: .6rem;
            align-items: center;
            color: var(--muted);
            font-size: .72rem;
            font-weight: 700;
        }

        .chip {
            display: inline-flex;
            border-radius: 999px;
            padding: .25rem .55rem;
            font-size: .7rem;
        }

        .chip.positive { background: rgba(34,197,94,.12); color: #15803d; }
        .chip.warning { background: rgba(245,158,11,.12); color: #b45309; }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: .75rem;
        }

        .panel-header h2 {
            margin: 0;
            font-size: 1.1rem;
        }

        .pill {
            display: inline-flex;
            border-radius: 999px;
            padding: .3rem .55rem;
            font-size: .7rem;
            font-weight: 700;
        }

        .pill.success { background: rgba(34,197,94,.12); color: #15803d; }
        .pill.neutral { background: rgba(59,130,246,.12); color: #1d4ed8; }

        .chart-wrap {
            position: relative;
            height: 260px;
            border-radius: 1rem;
            background: linear-gradient(180deg, rgba(79,70,229,0.05), rgba(59,130,246,0.02));
            overflow: hidden;
        }

        .chart-grid {
            position: absolute;
            inset: 0;
            background-image: linear-gradient(to right, rgba(148,163,184,.15) 1px, transparent 1px), linear-gradient(to top, rgba(148,163,184,.15) 1px, transparent 1px);
            background-size: 14% 25%;
        }

        .chart-wrap svg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .chart-labels {
            position: absolute;
            left: .75rem;
            right: .75rem;
            bottom: .4rem;
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            font-size: .7rem;
            color: var(--muted);
        }

        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            margin-top: 1rem;
        }

        .quick-action {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            text-decoration: none;
            color: var(--text);
            background: var(--surface-soft);
            border: 1px solid var(--line);
            border-radius: .8rem;
            padding: .7rem .9rem;
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
        }

        .quick-action.primary {
            background: linear-gradient(135deg, var(--primary-2), var(--primary));
            color: white;
            border: none;
        }

        .quick-action svg { width: 1rem; height: 1rem; }

        .mini-metrics { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem; }
        .mini-metric {
            background: var(--surface-soft);
            border: 1px solid var(--line);
            border-radius: .9rem;
            padding: .8rem .75rem;
        }

        .mini-metric span {
            color: var(--muted);
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .mini-metric strong { display: block; margin-top: .35rem; font-size: 1.6rem; }
        .mini-metric small { display: block; margin-top: .2rem; color: #15803d; font-weight: 700; }

        .ai-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: .7rem;
        }

        .ai-list li {
            position: relative;
            padding-left: 1rem;
            color: var(--text);
        }

        .ai-list li::before {
            content: '';
            position: absolute;
            left: 0;
            top: .45rem;
            width: .5rem;
            height: .5rem;
            background: linear-gradient(135deg, #22c55e, #14b8a6);
            border-radius: 50%;
        }

        .recommendation-box {
            margin-top: 1rem;
            background: rgba(79,70,229,.04);
            border: 1px solid rgba(79,70,229,.12);
            border-radius: 1rem;
            padding: .9rem 1rem;
        }

        .recommendation-box h3 { margin: 0 0 .5rem; }
        .recommendation-box ul { margin: 0; padding-left: 1.1rem; color: var(--text); }

        .patient-list, .distribution-list { display: flex; flex-direction: column; gap: .85rem; }
        .patient-item, .distribution-item {
            background: var(--surface-soft);
            border: 1px solid var(--line);
            border-radius: .9rem;
            padding: .75rem;
        }

        .patient-item { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
        .patient-item small { color: var(--muted); }
        .patient-meta { display: flex; flex-direction: column; align-items: flex-end; gap: .45rem; min-width: 120px; }

        .risk { display: inline-flex; padding: .2rem .55rem; border-radius: 999px; font-size: .7rem; font-weight: 700; }
        .risk.faible { background: rgba(34,197,94,.12); color: #15803d; }
        .risk.moyen { background: rgba(245,158,11,.12); color: #b45309; }
        .risk.eleve { background: rgba(239,68,68,.12); color: #b91c1c; }

        .progress-wrap { width: 100%; height: .6rem; background: rgba(148,163,184,.18); border-radius: 999px; overflow: hidden; }
        .progress-wrap span, .distribution-bar span { display: block; height: 100%; border-radius: inherit; }
        .progress-wrap span { background: linear-gradient(90deg, #22c55e, #4f46e5); }
        .distribution-bar { width: 100%; height: .6rem; background: rgba(148,163,184,.18); border-radius: 999px; overflow: hidden; }
        .distribution-bar.blue span { background: linear-gradient(90deg, #4f46e5, #6366f1); }
        .distribution-bar.green span { background: linear-gradient(90deg, #22c55e, #34d399); }
        .distribution-bar.orange span { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .distribution-bar.red span { background: linear-gradient(90deg, #ef4444, #fb7185); }
        .distribution-bar.sky span { background: linear-gradient(90deg, #0ea5e9, #38bdf8); }

        .distribution-info { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }

        .mini-preview-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 1rem;
            margin-top: 1.4rem;
        }

        .media-gallery {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
        }

        .media-card {
            position: relative;
            min-height: 150px;
            overflow: hidden;
            border-radius: 1rem;
            border: 1px solid var(--line);
            background: var(--surface-soft);
        }

        .media-card img {
            width: 100%;
            height: 150px;
            display: block;
            object-fit: cover;
        }

        .media-card-copy {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            padding: .65rem .75rem;
            background: linear-gradient(transparent, rgba(15,23,42,.92));
            color: white;
        }

        .media-card-copy strong,
        .media-card-copy small {
            display: block;
        }

        .media-empty {
            min-height: 150px;
            display: grid;
            place-items: center;
            color: var(--muted);
            text-align: center;
            background: var(--surface-soft);
        }

        .mini-preview-panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 1.1rem;
            box-shadow: var(--shadow);
            padding: 1rem 1.05rem;
        }

        .mini-preview-panel h3 {
            margin: 0 0 .9rem;
            font-size: 1.05rem;
        }

        .mini-preview-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: .7rem;
        }

        .mini-preview-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .7rem;
            background: var(--surface-soft);
            border: 1px solid var(--line);
            border-radius: .9rem;
            padding: .75rem .8rem;
        }

        .mini-preview-item strong {
            display: block;
            margin-bottom: .2rem;
            font-size: .95rem;
        }

        .mini-preview-item small {
            color: var(--muted);
        }

        .mini-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: .25rem .55rem;
            font-size: .68rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .mini-status.confirmed { background: rgba(34,197,94,.12); color: #15803d; }
        .mini-status.pending { background: rgba(59,130,246,.12); color: #1d4ed8; }
        .mini-status.review { background: rgba(245,158,11,.12); color: #b45309; }

        @media (max-width: 1100px) {
            .app-shell { display: block; }
            .sidebar { position: relative; width: 100%; min-height: auto; border-right: none; border-bottom: 1px solid var(--line); }
            .sidebar-footer { position: static; margin-top: 1rem; }
            .nav-menu { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
        }

        @media (max-width: 1024px) {
            .kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .main-grid, .secondary-grid, .third-grid { grid-template-columns: 1fr; }
            .mini-preview-grid { grid-template-columns: 1fr; }
            .media-gallery { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 640px) {
            .topbar-shell, .hero-title, .panel-header, .patient-item { flex-direction: column; align-items: flex-start; }
            .kpi-grid { grid-template-columns: 1fr; }
            .mini-metrics { grid-template-columns: 1fr; }
            .hero-action { width: 100%; }
        }

        .model-grid {
            display: grid;
            gap: .8rem;
            margin-top: .8rem;
        }

        .model-card {
            background: var(--surface-soft);
            border: 1px solid var(--line);
            border-radius: .9rem;
            padding: .75rem .85rem;
        }

        .model-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
        }

        .model-chip {
            display: inline-flex;
            font-size: .65rem;
            padding: .25rem .5rem;
            border-radius: 999px;
            background: rgba(16,185,129,0.12);
            color: #047857;
            font-weight: 700;
        }

        .model-card h4 {
            margin: .55rem 0 .25rem;
            font-size: 1rem;
        }

        .model-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.5;
            font-size: .82rem;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-width]').forEach(function (element) {
                element.style.width = element.dataset.width + '%';
            });

            const savedTheme = localStorage.getItem('medicare-theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark');
            }

            const toggle = document.getElementById('theme-toggle');
            if (toggle) {
                toggle.addEventListener('click', function () {
                    document.body.classList.toggle('dark');
                    const isDark = document.body.classList.contains('dark');
                    localStorage.setItem('medicare-theme', isDark ? 'dark' : 'light');
                    toggle.textContent = isDark ? 'Mode clair' : 'Mode sombre';
                });
            }
        });
    </script>
</head>
<body class="role-{{ auth()->user()->role ?? 'admin' }}">
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand-badge">
                <div class="brand-mark">M</div>
                <div class="brand-copy">
                    <strong>MediCare</strong>
                    <small>Cabinet médical</small>
                </div>
            </div>

            @php
                $userRole = auth()->user()->role ?? 'patient';
            @endphp

            <nav class="nav-menu" aria-label="Navigation principale">
                <a class="nav-item active" href="{{ route('dashboard') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 9.5V20h14V9.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Accueil
                </a>

                @if (in_array($userRole, ['admin', 'staff', 'doctor'], true))
                    <a class="nav-item" href="{{ route('admin.index') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20V8.5A2.5 2.5 0 0 1 6.5 6H17.5A2.5 2.5 0 0 1 20 8.5V20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 10h8M8 14h8M8 18h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Administration
                    </a>
                @endif

                @if ($userRole === 'admin')
                    <a class="nav-item" href="{{ route('doctors.index') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20V8.5A2.5 2.5 0 0 1 6.5 6H17.5A2.5 2.5 0 0 1 20 8.5V20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 10h8M8 14h8M8 18h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Médecins
                    </a>
                @endif

                @if (in_array($userRole, ['admin', 'doctor', 'staff'], true))
                    <a class="nav-item" href="{{ route('patients.index') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="2"/><path d="M20 19v-1a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Patients
                    </a>
                @endif

                <a class="nav-item" href="{{ route('appointments.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2"/><path d="M8 2v4M16 2v4M4 10h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Rendez-vous
                </a>

                @if ($userRole === 'doctor')
                    <a class="nav-item" href="{{ route('appointments.agenda') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 2v4M16 2v4M3.5 9.5h17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><rect x="3.5" y="5.5" width="17" height="15" rx="2" stroke="currentColor" stroke-width="2"/></svg>
                        Agenda
                    </a>
                @endif

                @if (in_array($userRole, ['admin', 'staff'], true))
                    <a class="nav-item" href="{{ route('rooms.index') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 21V7.5A2.5 2.5 0 0 1 5.5 5H18.5A2.5 2.5 0 0 1 21 7.5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M7 12h10M7 16h10M9 5V3M15 5V3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Salles
                    </a>
                    <a class="nav-item" href="{{ route('equipment.index') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 14.5h16M7 14.5V8.5a5 5 0 0 1 10 0v6M9 18h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M10 8.5h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Équipements
                    </a>
                @endif

                <a class="nav-item" href="{{ route('notifications.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 8a6 6 0 1 1 12 0v5l2 3H4l2-3V8Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M10 20a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Notifications
                </a>

                @if ($userRole === 'patient')
                    <a class="nav-item" href="{{ route('patient-record.show') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 4.75A2.75 2.75 0 0 1 9.75 2h4.5A2.75 2.75 0 0 1 17 4.75V19.5a.5.5 0 0 1-.75.43L12 17.5l-4.25 2.43A.5.5 0 0 1 7 19.5V4.75Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                        Mon dossier
                    </a>
                @endif

                @if (in_array($userRole, ['admin', 'doctor', 'staff'], true))
                    <a class="nav-item ai-button" href="{{ route('assistant.index') }}">
                        <span>
                            Assistant IA
                            <small>Analyse rapide</small>
                        </span>
                        <span class="spark">✦</span>
                    </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <div class="user-card">
                    <x-account-avatar :name="auth()->user()->name" :path="auth()->user()->avatar_path" :size="42" />
                    <div class="user-meta">
                        <strong>{{ auth()->user()->name ?? 'Utilisateur' }}</strong>
                        <small>{{ auth()->user()->role ?? 'admin' }}</small>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-panel">
            <div class="topbar-shell">
                <div class="topbar-title">
                    <span class="dot"></span>
                    <span>Centre de gestion · MediCare</span>
                </div>
                <div class="topbar-tools">
                    <button id="theme-toggle" type="button" class="theme-toggle">Mode sombre</button>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button class="logout-btn" type="submit">Se déconnecter</button>
                    </form>
                </div>
            </div>

            <div class="hero-title">
                <div>
                    <p class="eyebrow">Aperçu exécutif</p>
                    <h1>Tableau de bord MediCare</h1>
                </div>
                <span class="role-pill">{{ auth()->user()->role ?? 'admin' }}</span>
            </div>

            <div class="hero-actions">
                <a class="hero-action primary" href="{{ route('admin.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20V8.5A2.5 2.5 0 0 1 6.5 6H17.5A2.5 2.5 0 0 1 20 8.5V20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 10h8M8 14h8M8 18h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <span>Administration</span>
                </a>
                <a class="hero-action secondary" href="{{ route('doctors.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="2"/><path d="M20 19v-1a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <span>Médecins</span>
                </a>
                <a class="hero-action success" href="{{ route('patients.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="2"/><path d="M20 19v-1a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <span>Patients</span>
                </a>
                <a class="hero-action warning" href="{{ route('appointments.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2"/><path d="M8 2v4M16 2v4M4 10h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <span>Rendez-vous</span>
                </a>
                <a class="hero-action purple" href="{{ route('appointments.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 4.75A2.75 2.75 0 0 1 9.75 2h4.5A2.75 2.75 0 0 1 17 4.75V19.5a.5.5 0 0 1-.75.43L12 17.5l-4.25 2.43A.5.5 0 0 1 7 19.5V4.75Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                    <span>Voir les rendez-vous</span>
                </a>
            </div>

            <div class="management-panel">
                <div class="management-panel-header">
                    <div>
                        <p class="management-subtitle">Gestion</p>
                        <h2>Centre de commande du cabinet</h2>
                    </div>
                </div>

                <div class="action-grid">
                    <a class="action-card" href="{{ route('admin.index') }}">
                        <div class="action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20V8.5A2.5 2.5 0 0 1 6.5 6H17.5A2.5 2.5 0 0 1 20 8.5V20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 10h8M8 14h8M8 18h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></div>
                        <div class="action-title">Administration</div>
                        <div class="action-caption">Gérer le cabinet, les médecins, les salles et les rendez-vous.</div>
                    </a>

                    <a class="action-card" href="{{ route('doctors.index') }}">
                        <div class="action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="2"/><path d="M20 19v-1a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></div>
                        <div class="action-title">Médecins</div>
                        <div class="action-caption">Suivre les praticiens et leurs disponibilités.</div>
                    </a>

                    <a class="action-card" href="{{ route('patients.index') }}">
                        <div class="action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="2"/><path d="M20 19v-1a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></div>
                        <div class="action-title">Patients</div>
                        <div class="action-caption">Voir les dossiers et la prise en charge.</div>
                    </a>

                    <a class="action-card" href="{{ route('appointments.index') }}">
                        <div class="action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2"/><path d="M8 2v4M16 2v4M4 10h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></div>
                        <div class="action-title">Rendez-vous</div>
                        <div class="action-caption">Gérer le planning et les consultations programmées.</div>
                    </a>

                    <a class="action-card" href="{{ route('rooms.index') }}">
                        <div class="action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 21V7.5A2.5 2.5 0 0 1 5.5 5H18.5A2.5 2.5 0 0 1 21 7.5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M7 12h10M7 16h10M9 5V3M15 5V3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></div>
                        <div class="action-title">Salles</div>
                        <div class="action-caption">Gérer les salles de consultation et la logistique.</div>
                    </a>

                    <a class="action-card" href="{{ route('equipment.index') }}">
                        <div class="action-icon">M</div>
                        <div class="action-title">Équipements</div>
                        <div class="action-caption">Gérer les appareils, tensions, stéthoscopes et matériel médical.</div>
                    </a>

                    <a class="action-card" href="{{ route('appointments.index') }}">
                        <div class="action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 2v4M16 2v4M3.5 9.5h17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><rect x="3.5" y="5.5" width="17" height="15" rx="2" stroke="currentColor" stroke-width="2"/></svg></div>
                        <div class="action-title">Planning des rendez-vous</div>
                        <div class="action-caption">Voir et gérer les rendez-vous du cabinet.</div>
                    </a>

                    <a class="action-card" href="{{ route('notifications.index') }}">
                        <div class="action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 8a6 6 0 1 1 12 0v5l2 3H4l2-3V8Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M10 20a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></div>
                        <div class="action-title">Notifications</div>
                        <div class="action-caption">Suivre les alertes et confirmations du cabinet.</div>
                    </a>

                    <a class="action-card" href="{{ route('patients.index') }}">
                        <div class="action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 4.75A2.75 2.75 0 0 1 9.75 2h4.5A2.75 2.75 0 0 1 17 4.75V19.5a.5.5 0 0 1-.75.43L12 17.5l-4.25 2.43A.5.5 0 0 1 7 19.5V4.75Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg></div>
                        <div class="action-title">Dossiers patients</div>
                        <div class="action-caption">Consulter les dossiers patients du cabinet.</div>
                    </a>
                </div>
            </div>

            <div class="mini-preview-panel" style="margin-top:1.4rem;">
                <div class="panel-header">
                    <h3>Galerie du cabinet</h3>
                    <span class="pill neutral">Salles et équipements</span>
                </div>
                <div class="media-gallery">
                    @foreach ($rooms as $room)
                        @if ($room->image_path)
                            <a class="media-card" href="{{ route('rooms.show', $room) }}">
                                <img src="{{ asset('storage/'.$room->image_path) }}" alt="{{ $room->name }}" loading="lazy">
                                <div class="media-card-copy"><strong>{{ $room->name }}</strong><small>Salle médicale</small></div>
                            </a>
                        @endif
                    @endforeach
                    @foreach ($equipment as $item)
                        @if ($item->image_path)
                            <a class="media-card" href="{{ route('equipment.show', $item) }}">
                                <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->name }}" loading="lazy">
                                <div class="media-card-copy"><strong>{{ $item->name }}</strong><small>{{ $item->category ?? 'Équipement médical' }}</small></div>
                            </a>
                        @endif
                    @endforeach
                </div>
                @if ($rooms->filter(fn ($room) => $room->image_path)->isEmpty() && $equipment->filter(fn ($item) => $item->image_path)->isEmpty())
                    <div class="media-empty">Ajoutez des photos aux salles et aux équipements pour les afficher ici.</div>
                @endif
            </div>

            <div class="mini-preview-grid">
                <div class="mini-preview-panel">
                    <h3>Rendez-vous à venir</h3>
                    <ul class="mini-preview-list">
                        @forelse ($upcomingAppointments as $appointment)
                            <li class="mini-preview-item">
                                <div>
                                    <strong>{{ $appointment->title }}</strong>
                                    <small>{{ $appointment->date?->format('d/m/Y à H:i') }}</small>
                                </div>
                                <span class="mini-status {{ $appointment->status === 'confirmed' ? 'confirmed' : ($appointment->status === 'scheduled' ? 'pending' : 'review') }}">
                                    {{ $appointment->status === 'confirmed' ? 'Confirmé' : ($appointment->status === 'scheduled' ? 'Programmé' : 'À revoir') }}
                                </span>
                            </li>
                        @empty
                            <li class="mini-preview-item">
                                <div>
                                    <strong>Aucun rendez-vous</strong>
                                    <small>Le planning est libre pour le moment.</small>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                    <div class="quick-actions" style="margin-top:1rem;">
                        <a class="quick-action primary" href="{{ route('appointments.index') }}">Voir le planning</a>
                    </div>
                </div>

                <div class="mini-preview-panel">
                    <h3>Consultations récentes</h3>
                    <ul class="mini-preview-list">
                        @forelse ($recentConsultations as $consultation)
                            <li class="mini-preview-item">
                                <div>
                                    <strong>{{ $consultation->patient->name }}</strong>
                                    <small>{{ $consultation->date?->format('d/m/Y à H:i') }}</small>
                                </div>
                                <span class="mini-status {{ $consultation->status }}">
                                    {{ $consultation->status === 'confirmed' ? 'OK' : ($consultation->status === 'pending' ? 'En cours' : 'À valider') }}
                                </span>
                            </li>
                        @empty
                            <li class="mini-preview-item">
                                <div>
                                    <strong>Aucune consultation</strong>
                                    <small>Les consultations récentes apparaîtront ici.</small>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                    <div class="quick-actions" style="margin-top:1rem;">
                        <a class="quick-action" href="{{ route('appointments.index') }}">Tous les rendez-vous</a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
