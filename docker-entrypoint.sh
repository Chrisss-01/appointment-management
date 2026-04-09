#!/bin/bash
set -e

echo "Fixing Apache MPM modules..."

# Disable all MPMs first (ignore errors if not enabled)
a2dismod mpm_event || true
a2dismod mpm_worker || true
a2dismod mpm_prefork || true

# Enable ONLY prefork (required for mod_php)
a2enmod mpm_prefork

# Enable required modules
a2enmod rewrite
a2enmod headers

echo "Enabled MPM modules:"
ls -la /etc/apache2/mods-enabled/mpm_*

# Railway injects $PORT — configure Apache to listen on it
APP_PORT="${PORT:-80}"
echo "Configuring Apache to listen on port ${APP_PORT}..."
sed -i "s/Listen 80/Listen ${APP_PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${APP_PORT}>/" /etc/apache2/sites-enabled/*.conf

# Laravel startup
echo "Caching Laravel configuration..."
php artisan config:cache || echo "WARNING: config:cache failed"
php artisan route:cache  || echo "WARNING: route:cache failed"
php artisan view:cache   || echo "WARNING: view:cache failed"

echo "Running database migrations..."
if php artisan migrate --force 2>&1; then
    echo "Migrations complete."
    php artisan db:seed --force 2>&1 || echo "Seeder skipped."
else
    echo "WARNING: Migrations failed — DB may not be configured yet."
fi

php artisan storage:link 2>/dev/null || echo "Storage link already exists."

echo "Starting Apache on port ${APP_PORT}..."
exec apache2-foreground
