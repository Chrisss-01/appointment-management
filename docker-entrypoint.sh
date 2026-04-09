#!/bin/bash
set -e

echo "========================================"
echo "  UV Clinic - Container Startup"
echo "========================================"

# Railway injects $PORT — configure Apache to listen on it (default 80 locally)
APP_PORT="${PORT:-80}"
echo "==> Configuring Apache to listen on port ${APP_PORT}..."
sed -i "s/Listen 80/Listen ${APP_PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${APP_PORT}>/" /etc/apache2/sites-enabled/*.conf

# Cache config/routes/views (requires APP_KEY to be set)
echo "==> Caching Laravel configuration..."
php artisan config:cache || echo "WARNING: config:cache failed (APP_KEY missing?)"
php artisan route:cache  || echo "WARNING: route:cache failed"
php artisan view:cache   || echo "WARNING: view:cache failed"

# Run DB migrations — non-fatal: Apache will still start even if DB is unavailable
echo "==> Running database migrations..."
if php artisan migrate --force 2>&1; then
    echo "    Migrations complete."

    echo "==> Seeding database..."
    php artisan db:seed --force 2>&1 || echo "    Seeder skipped (already seeded or error)."
else
    echo "    WARNING: Migrations failed — DB may not be configured yet."
    echo "    Check DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD in Railway Variables."
fi

# Create storage symlink (non-fatal)
echo "==> Creating storage symlink..."
php artisan storage:link 2>/dev/null || echo "    Storage link already exists."

echo "==> Starting Apache on port ${APP_PORT}..."
exec apache2-foreground
