#!/usr/bin/env bash
set -e
if ! command -v composer >/dev/null 2>&1; then
  echo "Composer no está disponible. Abra Herd y habilite Composer/PHP en su terminal."
  exit 1
fi
composer install
[ -f .env ] || cp .env.example .env
php artisan key:generate --force
php artisan optimize:clear
echo "Dependencias listas. Cree la base MySQL 'iot_bodega', revise .env y ejecute: php artisan migrate --seed"
