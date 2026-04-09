#!/bin/bash
set -e

echo "==> Caching Laravel config/routes/views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Seeding database (if empty)..."
php artisan db:seed --force --class=DatabaseSeeder 2>/dev/null || echo "Seeder skipped or already run."

echo "==> Creating storage symlink..."
php artisan storage:link 2>/dev/null || echo "Storage link already exists."

echo "==> Starting Apache..."
exec apache2-foreground
