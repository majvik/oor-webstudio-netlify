#!/usr/bin/env bash
# Проверка подключения к БД из Docker. Учётные данные из .env (секреты не в коде).
# Если заданы WORDPRESS_DB_* — проверяет внешнюю БД (Managed MySQL). Иначе — локальный контейнер db.
set -e
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$PROJECT_ROOT"

if [ ! -f .env ]; then
  echo "Создайте .env и задайте переменные БД (DB_* или WORDPRESS_DB_*)"
  exit 1
fi

set -a
# shellcheck source=/dev/null
. .env
set +a

if [ -n "${WORDPRESS_DB_HOST:-}" ]; then
  echo "→ Проверка внешней БД: $WORDPRESS_DB_HOST (из WORDPRESS_DB_*)"
  docker run --rm mysql:8.0 mysql \
    -h "${WORDPRESS_DB_HOST}" \
    -P "${WORDPRESS_DB_PORT:-3306}" \
    -u "${WORDPRESS_DB_USER:?WORDPRESS_DB_USER не задан}" \
    -p"${WORDPRESS_DB_PASSWORD:?WORDPRESS_DB_PASSWORD не задан}" \
    -D "${WORDPRESS_DB_NAME:-wordpress}" \
    --connect-timeout=10 \
    -e "SELECT 1 AS ok; SHOW TABLES;" 2>&1
else
  echo "→ Проверка локальной БД (docker compose db)"
  docker compose exec -T db mysql -u root -p"${DB_ROOT_PASSWORD:?DB_ROOT_PASSWORD не задан}" "${DB_NAME:-wordpress}" -e "SELECT 1 AS ok; SHOW TABLES;" 2>&1
fi
echo "✅ Подключение к БД успешно"
