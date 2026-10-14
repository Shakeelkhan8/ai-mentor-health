#!/bin/sh
# Container entrypoint: prepare the app for this environment, then serve it.
# Runs on every boot. With the default SQLite setup the database lives inside the
# container, so each boot starts from a fresh, freshly seeded demo.
set -e

# Render provides the public URL; an explicit APP_URL wins.
export APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL:-http://localhost:${PORT:-8080}}}"

# Create the SQLite file before any artisan command: the app opens its database
# connection while booting (AppServiceProvider), so even key:generate needs it.
if [ "${DB_CONNECTION:-}" = "sqlite" ] && [ -n "${DB_DATABASE:-}" ]; then
    mkdir -p "$(dirname "$DB_DATABASE")"
    touch "$DB_DATABASE"
fi

# Without a configured key, generate one for this container's lifetime. That only
# suits an ephemeral demo: sessions and encrypted values don't survive a restart.
# Made from random bytes directly, so nothing a command prints can end up in the key.
if [ -z "${APP_KEY:-}" ]; then
    APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"
    export APP_KEY
    echo "APP_KEY not set: generated a key for this container's lifetime."
fi

php artisan migrate --force

if [ "${DEMO_MODE:-false}" = "true" ]; then
    php artisan db:seed --class=DemoSeeder --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec frankenphp php-server --listen ":${PORT:-8080}" --root public/
