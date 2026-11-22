<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administration · MediCare</title>
    <style>
        :root {
            --bg: #f8fafc;
            --panel: #ffffff;
            --panel-soft: #f4f7fb;
            --primary: #0f766e;
            --primary-dark: #115e59;
            --primary-soft: rgba(15, 118, 110, 0.12);
            --blue: #2563eb;
            --text: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --success: #16a34a;
            --shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: linear-gradient(135deg, #ecfeff 0%, var(--bg) 35%, #eef2ff 100%);
            color: var(--text);
            font-family: Arial, sans-serif;
        }

        .page-shell {
            max-width: 1180px;
            margin: 0 auto;
            padding: 2rem 1.25rem 3rem;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1rem 1.25rem;
            background: rgba(255,255,255,0.9);
            border: 1px solid rgba(148,163,184,0.2);
            border-radius: 1rem;
            box-shadow: var(--shadow);
            backdrop-filter: blur(8px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: .85rem;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--blue));
            color: #fff;
            box-shadow: 0 10px 18px rgba(37, 99, 235, 0.22);
        }

        .brand-mark svg { display: block; }

        .brand-copy strong {
            display: block;
            font-size: 1.1rem;
        }

        .brand-copy span {
            color: var(--muted);
            font-size: .8rem;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .55rem;
            border: none;
            border-radius: .8rem;
            padding: .8rem 1rem;
            font: inherit;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--blue));
            color: #fff;
            box-shadow: 0 14px 24px rgba(37, 99, 235, 0.22);
        }

        .btn-secondary {
            background: #fff;
            color: var(--text);
            border: 1px solid var(--line);
        }

        .hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem 1.25rem;
            background: linear-gradient(135deg, rgba(15,118,110,0.08), rgba(37,99,235,0.06));
            border: 1px solid rgba(15,118,110,0.12);
            border-radius: 1.25rem;
            margin-bottom: 2rem;
        }

        .hero h1 {
            margin: 0 0 .3rem;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
        }

        .hero p {
            margin: 0;
            color: var(--muted);
            font-size: 1rem;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: rgba(22, 163, 74, 0.12);
            color: #166534;
            border-radius: 999px;
            padding: .55rem .8rem;
            font-weight: 700;
            font-size: .8rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--panel);
            border: 1px solid rgba(148,163,184,0.18);
            border-radius: 1rem;
            padding: 1rem 1.1rem;
            box-shadow: var(--shadow);
        }

        .stat-label {
            display: block;
            margin-bottom: .5rem;
            color: var(--muted);
            font-size: .75rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .stat-value {
            font-size: clamp(1.5rem, 2vw, 2.2rem);
            font-weight: 800;
        }

        .stat-trend {
            display: inline-block;
            margin-top: .5rem;
            padding: .28rem .56rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            background: rgba(34,197,94,0.12);
            color: #166534;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.2rem;
        }

        .admin-card {
            background: var(--panel);
            border: 1px solid rgba(148,163,184,0.18);
            border-radius: 1.2rem;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .admin-card-header {
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: 1.25rem 1.25rem 0;
        }

        .icon-wrap {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            color: #fff;
            background: linear-gradient(135deg, var(--primary), var(--blue));
        }

        .icon-wrap svg {
            width: 24px;
            height: 24px;
        }

        .admin-card-header h2 {
            margin: 0;
            font-size: 1.2rem;
        }

        .admin-card-header p {
            margin: .15rem 0 0;
            color: var(--muted);
            font-size: .85rem;
        }

        .admin-card-body {
            padding: 1.25rem;
        }

        .action-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: .7rem;
        }

        .action-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
            padding: .8rem .9rem;
            background: var(--panel-soft);
            border: 1px solid var(--line);
            border-radius: .9rem;
        }

        .action-copy {
            display: flex;
            align-items: center;
            gap: .7rem;
        }

        .mini-bullet {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--blue));
        }

        .action-text {
            display: flex;
            flex-direction: column;
        }

        .action-text strong {
            font-size: .95rem;
        }

        .action-text span {
            color: var(--muted);
            font-size: .78rem;
        }

        .btn-mini {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            background: #fff;
            color: var(--primary-dark);
            border: 1px solid rgba(15, 118, 110, 0.18);
            border-radius: .7rem;
            padding: .55rem .8rem;
            font-weight: 700;
            font-size: .8rem;
        }

        .preview-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 1.2rem;
            margin-top: 2rem;
        }

        .preview-panel {
            background: var(--panel);
            border: 1px solid rgba(148,163,184,0.18);
            border-radius: 1.2rem;
            box-shadow: var(--shadow);
            padding: 1.25rem;
        }

        .preview-panel h3 {
            margin: 0 0 1rem;
            font-size: 1.1rem;
        }

        .preview-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: .8rem;
        }

        .preview-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
            background: var(--panel-soft);
            border: 1px solid var(--line);
            border-radius: .9rem;
            padding: .8rem .9rem;
        }

        .preview-item strong {
            display: block;
            margin-bottom: .2rem;
        }

        .preview-meta {
            color: var(--muted);
            font-size: .78rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: .3rem .6rem;
            font-size: .7rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-badge.confirmed {
            background: rgba(34,197,94,0.12);
            color: #15803d;
        }

        .status-badge.pending {
            background: rgba(59,130,246,0.12);
            color: #1d4ed8;
        }

        .status-badge.review {
            background: rgba(245,158,11,0.12);
            color: #b45309;
        }

        @media (max-width: 900px) {
            .stats-grid,
            .cards-grid,
            .preview-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .topbar,
            .hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-grid,
            .cards-grid,
            .preview-grid {
                grid-template-columns: 1fr;
            }

            .nav-actions {
                width: 100%;
            }

            .btn,
            .btn-mini {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <header class="topbar">
            <div class="brand">
                <div class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" width="20" height="20">
                        <path d="M5 18.5V6.5A1.5 1.5 0 0 1 6.5 5H17.5A1.5 1.5 0 0 1 19 6.5V18.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M8 9h8M8 13h8M8 17h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="brand-copy">
                    <strong>MediCare</strong>
                    <span>Administration</span>
                </div>
            </div>

            @php
                $returnDashboardRoute = route('dashboard');
            @endphp

            <nav class="nav-actions">
                <a class="btn btn-secondary" href="{{ $returnDashboardRoute }}">Retour au tableau de bord</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-primary" type="submit">Se déconnecter</button>
                </form>
            </nav>
        </header>

        <section class="hero">
            <div>
                <h1>Bienvenue dans l’administration</h1>
                <p>Gérez les ressources, les patients, les rendez-vous et les services de votre structure.</p>
            </div>
            <div class="hero-badge">● Système actif</div>
        </section>

        <section class="stats-grid">
            <article class="stat-card">
                <span class="stat-label">Médecins</span>
                <div class="stat-value">24</div>
                <span class="stat-trend">+3 ce mois</span>
            </article>
            <article class="stat-card">
                <span class="stat-label">Patients</span>
                <div class="stat-value">1 482</div>
                <span class="stat-trend">+8.4%</span>
            </article>
            <article class="stat-card">
                <span class="stat-label">Rendez-vous</span>
                <div class="stat-value">318</div>
                <span class="stat-trend">+12.1%</span>
            </article>
            <article class="stat-card">
                <span class="stat-label">Salles</span>
                <div class="stat-value">17</div>
                <span class="stat-trend">100% disponibles</span>
            </article>
        </section>

        <section class="cards-grid">
            <article class="admin-card">
                <div class="admin-card-header">
                    <div class="icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M4 20V8.5A2.5 2.5 0 0 1 6.5 6H17.5A2.5 2.5 0 0 1 20 8.5V20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M8 10h8M8 14h8M8 18h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Docteurs</h2>
                        <p>Gestion médicale et profils</p>
                    </div>
                </div>
                <div class="admin-card-body">
                    <ul class="action-list">
                        <li class="action-item">
                            <div class="action-copy">
                                <span class="mini-bullet"></span>
                                <div class="action-text">
                                    <strong>Liste des médecins</strong>
                                    <span>Consulter et organiser les praticiens</span>
                                </div>
                            </div>
                            <a class="btn-mini" href="{{ route('doctors.index') }}">Ouvrir</a>
                        </li>
                    </ul>
                </div>
            </article>

            <article class="admin-card">
                <div class="admin-card-header">
                    <div class="icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M20 19v-1a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Patients</h2>
                        <p>Suivi et dossiers</p>
                    </div>
                </div>
                <div class="admin-card-body">
                    <ul class="action-list">
                        <li class="action-item">
                            <div class="action-copy">
                                <span class="mini-bullet"></span>
                                <div class="action-text">
                                    <strong>Liste des patients</strong>
                                    <span>Accéder aux dossiers et informations</span>
                                </div>
                            </div>
                            <a class="btn-mini" href="{{ route('patients.index') }}">Ouvrir</a>
                        </li>
                    </ul>
                </div>
            </article>

            <article class="admin-card">
                <div class="admin-card-header">
                    <div class="icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 2v4M16 2v4M4 10h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Rendez-vous</h2>
                        <p>Planning et prises en charge</p>
                    </div>
                </div>
                <div class="admin-card-body">
                    <ul class="action-list">
                        <li class="action-item">
                            <div class="action-copy">
                                <span class="mini-bullet"></span>
                                <div class="action-text">
                                    <strong>Agenda des consultations</strong>
                                    <span>Suivre les rendez-vous et les disponibilités</span>
                                </div>
                            </div>
                            <a class="btn-mini" href="{{ route('admin.index') }}#appointments-overview">Ouvrir</a>
                        </li>
                    </ul>
                </div>
            </article>

            <article class="admin-card">
                <div class="admin-card-header">
                    <div class="icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M3 21V7.5A2.5 2.5 0 0 1 5.5 5H18.5A2.5 2.5 0 0 1 21 7.5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M7 12h10M7 16h10M9 5V3M15 5V3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Salles</h2>
                        <p>Disponibilités et ressources</p>
                    </div>
                </div>
                <div class="admin-card-body">
                    <ul class="action-list">
                        <li class="action-item">
                            <div class="action-copy">
                                <span class="mini-bullet"></span>
                                <div class="action-text">
                                    <strong>Gestion des salles</strong>
                                    <span>Suivre les chambres et salles de consultation</span>
                                </div>
                            </div>
                            <a class="btn-mini" href="{{ route('rooms.index') }}">Ouvrir</a>
                        </li>
                    </ul>
                </div>
            </article>

            <article class="admin-card">
                <div class="admin-card-header">
                    <div class="icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M7 4.75A2.75 2.75 0 0 1 9.75 2h4.5A2.75 2.75 0 0 1 17 4.75V19.5a.5.5 0 0 1-.75.43L12 17.5l-4.25 2.43A.5.5 0 0 1 7 19.5V4.75Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Consultations</h2>
                        <p>Suivi clinique et dossiers</p>
                    </div>
                </div>
                <div class="admin-card-body">
                    <ul class="action-list">
                        <li class="action-item">
                            <div class="action-copy">
                                <span class="mini-bullet"></span>
                                <div class="action-text">
                                    <strong>Historique et suivi</strong>
                                    <span>Voir les consultations en cours et validées</span>
                                </div>
                            </div>
                            <a class="btn-mini" href="{{ route('admin.index') }}#consultations-overview">Ouvrir</a>
                        </li>
                    </ul>
                </div>
            </article>
        </section>

        <section class="preview-grid" id="appointments-overview">
            <div class="preview-panel">
                <h3>Rendez-vous du jour</h3>
                <ul class="preview-list">
                    <li class="preview-item">
                        <div>
                            <strong>Martin Dupont</strong>
                            <div class="preview-meta">Dr. Amélie Morel · 09:30</div>
                        </div>
                        <span class="status-badge confirmed">Confirmé</span>
                    </li>
                    <li class="preview-item">
                        <div>
                            <strong>Claire Bernard</strong>
                            <div class="preview-meta">Dr. Karim Saleh · 11:00</div>
                        </div>
                        <span class="status-badge pending">Programmé</span>
                    </li>
                    <li class="preview-item">
                        <div>
                            <strong>Nicolas Petit</strong>
                            <div class="preview-meta">Dr. Sarah Lemoine · 14:15</div>
                        </div>
                        <span class="status-badge review">À revoir</span>
                    </li>
                </ul>
                <div style="margin-top:1rem;">
                    <a class="btn btn-primary" href="{{ route('appointments.index') }}">Voir le planning complet</a>
                </div>
            </div>

            <div class="preview-panel" id="consultations-overview">
                <h3>Consultations</h3>
                <ul class="preview-list">
                    <li class="preview-item">
                        <div>
                            <strong>Suivi patient</strong>
                            <div class="preview-meta">Dossier médical · 08:45</div>
                        </div>
                        <span class="status-badge confirmed">OK</span>
                    </li>
                    <li class="preview-item">
                        <div>
                            <strong>Ordonnance</strong>
                            <div class="preview-meta">À finaliser · 10:20</div>
                        </div>
                        <span class="status-badge pending">En cours</span>
                    </li>
                    <li class="preview-item">
                        <div>
                            <strong>Compte-rendu</strong>
                            <div class="preview-meta">Validation doctor · 15:00</div>
                        </div>
                        <span class="status-badge review">À valider</span>
                    </li>
                </ul>
                <div style="margin-top:1rem;">
                    <a class="btn btn-secondary" href="{{ route('appointments.index') }}">Ouvrir les rendez-vous</a>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
