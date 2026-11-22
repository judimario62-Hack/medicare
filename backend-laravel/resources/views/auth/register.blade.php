<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription · MediCare</title>
    <style>
        :root {
            --bg: #edf6ff;
            --panel: rgba(255,255,255,0.94);
            --primary: #0f766e;
            --primary-dark: #115e59;
            --primary-2: #2563eb;
            --text: #0f172a;
            --muted: #475569;
            --line: #dfeaf8;
            --danger: #dc2626;
            --shadow: 0 18px 50px rgba(15, 23, 42, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #ecfeff 0%, #eff6ff 40%, #f8fafc 100%);
            font-family: Arial, sans-serif;
            color: var(--text);
        }

        .auth-shell {
            width: min(100% - 1.5rem, 1080px);
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            border-radius: 26px;
            overflow: hidden;
            background: var(--panel);
            box-shadow: var(--shadow);
            border: 1px solid rgba(148,163,184,0.18);
        }

        .visual {
            background: linear-gradient(135deg, rgba(15,118,110,0.14), rgba(37,99,235,0.14));
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: .8rem;
            margin-bottom: 2rem;
            font-weight: 800;
            font-size: 1.2rem;
        }

        .logo {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            color: white;
            box-shadow: 0 14px 30px rgba(37,99,235,0.25);
        }

        .logo svg {
            width: 24px;
            height: 24px;
        }

        .visual h1 {
            margin: 0 0 .75rem;
            font-size: clamp(2rem, 3vw, 3rem);
            line-height: 1.1;
        }

        .visual p {
            margin: 0;
            max-width: 26rem;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.7;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 1.5rem 0 0;
            display: grid;
            gap: .8rem;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: .7rem;
            color: var(--text);
            font-weight: 600;
        }

        .feature-list span {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            display: inline-block;
        }

        .form-panel {
            padding: 2.2rem 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        form {
            width: min(100%, 24rem);
        }

        .eyebrow {
            display: inline-block;
            margin-bottom: .7rem;
            color: var(--primary);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        h2 {
            margin: 0 0 1.2rem;
            font-size: 2rem;
        }

        label {
            display: block;
            margin: 1rem 0 .45rem;
            font-weight: 700;
        }

        input {
            width: 100%;
            padding: .82rem .9rem;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: #fff;
            font: inherit;
            color: var(--text);
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        input:focus {
            border-color: rgba(37, 99, 235, 0.6);
            box-shadow: 0 0 0 4px rgba(37,99,235,0.12);
        }

        button {
            width: 100%;
            margin-top: 1.3rem;
            padding: .9rem 1rem;
            border: none;
            border-radius: 12px;
            font: inherit;
            font-weight: 800;
            color: white;
            cursor: pointer;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            box-shadow: 0 16px 30px rgba(37,99,235,0.2);
        }

        .link {
            display: inline-block;
            margin-top: 1.2rem;
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
        }

        .error {
            margin-top: .45rem;
            color: var(--danger);
            font-size: .82rem;
            font-weight: 600;
        }

        @media (max-width: 760px) {
            .auth-shell {
                grid-template-columns: 1fr;
            }

            .visual {
                padding-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <main class="auth-shell">
        <section class="visual">
            <div class="brand">
                <div class="logo" aria-label="Logo MediCare">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M6 19V8.5A2.5 2.5 0 0 1 8.5 6H15.5A2.5 2.5 0 0 1 18 8.5V19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M9 10h6M12 7v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M5 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <span>MediCare</span>
            </div>

            <h1>Créez votre espace <br>patient.</h1>
            <p>Inscrivez-vous pour demander des rendez-vous, suivre vos consultations et recevoir les notifications de votre cabinet.</p>

            <ul class="feature-list">
                <li><span></span> Accès sécurisé</li>
                <li><span></span> Suivi patient centralisé</li>
                <li><span></span> Planification simplifiée</li>
            </ul>
        </section>

        <section class="form-panel">
            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                @csrf
                <span class="eyebrow">Inscription</span>
                <h2>Créer un compte</h2>

                <label for="name">Nom complet</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>
                @error('name') <div class="error">{{ $message }}</div> @enderror

                <label for="email">Adresse e-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror

                <label for="avatar">Photo de profil</label>
                <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp">
                @error('avatar') <div class="error">{{ $message }}</div> @enderror

                <label for="password">Mot de passe</label>
                <input id="password" name="password" type="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror

                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>

                <button type="submit">S'inscrire</button>

                <div style="text-align:center; margin-top:1rem;">
                    <a href="{{ route('login') }}" class="link">Déjà inscrit ? Se connecter</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
