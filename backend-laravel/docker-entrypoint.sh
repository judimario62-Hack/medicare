#!/bin/bash
set -e

# Render injecte la variable $PORT dynamique (ex: 10000)
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Préparation de Laravel. Les erreurs interrompent le démarrage pour éviter
# de déclarer le service disponible avec des migrations ou caches invalides.
php artisan config:cache
php artisan view:cache
php artisan route:clear

# Exécution des migrations de base de données (PostgreSQL sur Render)
echo "Exécution des migrations..."
php artisan migrate --force

# Rend les fichiers uploadés accessibles depuis le dossier public
php artisan storage:link --force

# Création uniquement des comptes configurés via les variables d'environnement Render
echo "Seeding des comptes administratifs configurés..."
php artisan db:seed --force

# Démarrage d'Apache en avant-plan
echo "Démarrage de MediCare sur le port ${PORT}..."
exec apache2-foreground
