#!/bin/sh
set -eu
mkdir -p /app/runtime
if [ ! -f /app/runtime/.env ]; then cp .env.example /app/runtime/.env; fi
ln -sf /app/runtime/.env /app/.env
if ! grep -q '^APP_KEY=base64:' .env; then php artisan key:generate; fi
php artisan migrate --force
if [ "${SEED_DEMO:-false}" = "true" ]; then php artisan db:seed --force; fi
exec php artisan serve --host=0.0.0.0 --port=8000
