#!/bin/sh
set -eu
if [ -z "${APP_KEY:-}" ]; then
    echo 'APP_KEY must be supplied as a persistent deployment secret.' >&2
    exit 1
fi
if [ "${APP_DEBUG:-false}" != "false" ] || [ "${APP_ENV:-production}" != "production" ]; then
    echo 'Production containers require APP_ENV=production and APP_DEBUG=false.' >&2
    exit 1
fi
# Cache only after deployment environment variables are available.
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache
# Migrations and account creation are explicit release commands, never startup side effects.
exec "$@"
