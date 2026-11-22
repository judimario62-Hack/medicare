<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Assistant IA local avec Ollama

Pour tester MedGemma en local sur Windows, installe Ollama puis, dans PowerShell, télécharge et démarre le modèle :

```powershell
ollama pull medgemma
ollama run medgemma
```

Ollama expose son API locale sur `http://127.0.0.1:11434`. Dans `backend-laravel\.env`, configure :

```dotenv
AI_PROVIDER=ollama
OLLAMA_URL=http://127.0.0.1:11434
OLLAMA_MODEL=medgemma
OLLAMA_TIMEOUT=300
```

Le premier appel peut prendre plus de temps pendant le chargement du modèle. Laravel applique une limite PHP adaptée au délai `OLLAMA_TIMEOUT` ; si Ollama ne répond pas ou si la génération dépasse le délai, le formulaire revient avec un message et conserve les champs saisis.

Dans un autre terminal PowerShell, démarre Laravel depuis le dossier `backend-laravel` :

```powershell
php artisan serve
```

Ouvre ensuite l’application, connecte-toi avec un compte autorisé et va sur **Assistant IA**. Le formulaire existant envoie les informations à Laravel, qui appelle Ollama côté serveur. Ne mets pas l’URL Ollama dans le JavaScript du navigateur. Cette adresse locale ne sera pas accessible depuis Render ; un déploiement distant doit utiliser un serveur Ollama accessible de façon sécurisée.

## Déploiement démo sur Render

Le fichier `render.yaml` configure une démo Render avec Docker, PostgreSQL, HTTPS, les migrations automatiques et le point de contrôle `/up`.

1. Publiez le dossier `backend-laravel` dans un dépôt GitHub. Si le dépôt contient aussi d’autres dossiers, choisissez `backend-laravel` comme **Root Directory** du service Render et utilisez `backend-laravel/render.yaml` comme Blueprint.
2. Dans Render, créez un **Blueprint** relié au dépôt et sélectionnez le plan gratuit pour cette démo.
3. Renseignez les variables secrètes demandées dans Render :
   - `APP_KEY` : générez une clé dans le dossier du projet avec `php artisan key:generate --show`, puis copiez-la dans Render.
   - `APP_URL` : URL HTTPS attribuée au service, par exemple `https://medicare-app.onrender.com`.
   - `MEDICARE_ADMIN_NAME`, `MEDICARE_ADMIN_EMAIL`, `MEDICARE_ADMIN_PASSWORD` pour le compte administrateur.
   - `MEDICARE_STAFF_NAME`, `MEDICARE_STAFF_EMAIL`, `MEDICARE_STAFF_PASSWORD` pour le compte du secrétariat.
   - Les variables `MEDICARE_DOCTOR_*` sont à renseigner si un compte médecin doit être créé au premier démarrage.
4. Attendez la fin du déploiement et l’état **Live**, puis ouvrez `https://<votre-service>.onrender.com/connexion`.
5. Testez le parcours : inscription d’un patient sur son téléphone, demande de rendez-vous, connexion staff, confirmation, puis vérification de la notification dans l’espace patient.

Les notifications configurées sont visibles **dans l’application après connexion**; aucun SMS ni notification push n’est envoyé. Cette configuration gratuite est uniquement destinée aux essais : le service web peut s’endormir après inactivité, la base gratuite expire après 30 jours et les photos/fichiers uploadés disparaissent aux redémarrages. N’y stockez pas de véritables dossiers ou données médicales. Pour un usage réel, passez aux services payants et configurez un stockage persistant avant d’inviter des patients.
