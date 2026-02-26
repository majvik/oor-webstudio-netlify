#!/usr/bin/env bash
# Бэкап локальной БД в data/wordpress.sql. Учётные данные из .env (секреты не в коде).
# Перед коммитом: подставьте в дампе локальный URL на __WP_HOME__ для подмены на проде.
set -e
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$PROJECT_ROOT"

if [ ! -f .env ]; then
  echo "Создайте .env из .env.example и заполните DB_ROOT_PASSWORD, DB_NAME"
  exit 1
fi

set -a
# shellcheck source=/dev/null
. .env
set +a

mkdir -p data
DUMP="$PROJECT_ROOT/data/wordpress.sql"
LOCAL_URL="${LOCAL_URL:-https://localhost:8443}"

echo "→ Бэкап БД (из .env: DB_NAME=${DB_NAME}, хост db) в $DUMP ..."
docker compose exec -T db mysqldump -u root -p"${DB_ROOT_PASSWORD:?DB_ROOT_PASSWORD не задан в .env}" "${DB_NAME:-wordpress}" \
  --single-transaction --quick \
  > "$DUMP"

echo "→ Замена URL на плейсхолдер для прода: $LOCAL_URL → __WP_HOME__"
sed "s|${LOCAL_URL}|__WP_HOME__|g" "$DUMP" > "${DUMP}.tmp" && mv "${DUMP}.tmp" "$DUMP"

echo "✅ Готово: $DUMP (можно коммитить, секретов нет; на проде задайте WP_HOME и LOAD_DB_FROM_FILE=1 для загрузки)"
