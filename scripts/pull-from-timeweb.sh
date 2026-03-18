#!/usr/bin/env bash
# Pull DB from Timeweb Cloud Managed MySQL → local docker-compose MySQL.
# Usage: bash scripts/pull-from-timeweb.sh
# Requires: .env with WORDPRESS_DB_* (Timeweb Managed MySQL credentials)
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$PROJECT_ROOT"

if [ ! -f .env ]; then
  echo "❌ Нет файла .env — скопируйте из .env.example и заполните WORDPRESS_DB_* для Timeweb."
  exit 1
fi

set -a
# shellcheck source=/dev/null
. .env
set +a

# --- Timeweb Managed MySQL ---
TW_HOST="${WORDPRESS_DB_PUBLIC_HOST:-${WORDPRESS_DB_HOST:?Задайте WORDPRESS_DB_HOST или WORDPRESS_DB_PUBLIC_HOST в .env}}"
TW_PORT="${WORDPRESS_DB_PORT:-3306}"
TW_DB="${WORDPRESS_DB_NAME:?Задайте WORDPRESS_DB_NAME в .env}"
TW_USER="${WORDPRESS_DB_USER:?Задайте WORDPRESS_DB_USER в .env}"
TW_PASS="${WORDPRESS_DB_PASSWORD:?Задайте WORDPRESS_DB_PASSWORD в .env}"

# --- Local docker-compose MySQL ---
LOCAL_DB_NAME="${DB_NAME:-wordpress}"
LOCAL_DB_ROOT_PASS="${DB_ROOT_PASSWORD:-rootpassword}"

LOCAL_URL="${LOCAL_URL:-https://localhost:8443}"
PROD_URL="${PROD_WP_URL:-https://outofrec.com}"

echo ""
echo "=== Pull DB: Timeweb Managed MySQL → Local ==="
echo ""
echo "  Timeweb:     $TW_USER@$TW_HOST:$TW_PORT/$TW_DB"
echo "  Local:       oor-mysql / $LOCAL_DB_NAME"
echo "  URL replace: $PROD_URL → $LOCAL_URL"
echo ""

# 1. Backup local DB
BACKUP_FILE="local_backup_$(date +%F_%H%M).sql"
echo "→ [1/4] Бэкап текущей локальной БД..."
if docker compose exec -T db mysqldump -u root -p"$LOCAL_DB_ROOT_PASS" "$LOCAL_DB_NAME" > "$BACKUP_FILE" 2>/dev/null; then
  echo "  ✅ Создан $BACKUP_FILE"
else
  rm -f "$BACKUP_FILE" 2>/dev/null
  echo "  ⚠️  Локальная БД не запущена или пуста — пропуск бэкапа"
fi

# 2. Dump from Timeweb Managed MySQL
echo "→ [2/4] Дамп БД с Timeweb..."
DUMP_FILE="$PROJECT_ROOT/data/timeweb-dump.sql"
mkdir -p "$PROJECT_ROOT/data"

docker run --rm \
  mysql:8.0 \
  mysqldump \
    -h "$TW_HOST" -P "$TW_PORT" \
    -u "$TW_USER" -p"$TW_PASS" \
    --ssl-mode=REQUIRED \
    --skip-lock-tables --quick --no-tablespaces \
    "$TW_DB" \
  > "$DUMP_FILE" 2>/dev/null || true

DUMP_SIZE=$(wc -c < "$DUMP_FILE" | tr -d ' ')
echo "  ✅ Дамп: $(( DUMP_SIZE / 1024 )) KB"

if [ "$DUMP_SIZE" -lt 1000 ]; then
  echo "❌ Дамп подозрительно маленький (${DUMP_SIZE} байт). Проверьте подключение к Timeweb MySQL."
  exit 1
fi

# 3. Import into local MySQL
echo "→ [3/4] Импорт в локальную БД..."
docker compose exec -T db mysql -u root -p"$LOCAL_DB_ROOT_PASS" "$LOCAL_DB_NAME" < "$DUMP_FILE"
echo "  ✅ Импорт завершён"

# 4. Search-replace URLs
echo "→ [4/4] Search & Replace ($PROD_URL → $LOCAL_URL)..."
docker compose exec -T wordpress wp search-replace \
  "$PROD_URL" "$LOCAL_URL" \
  --all-tables --precise --allow-root 2>/dev/null || \
docker compose exec -T wordpress wp-cli search-replace \
  "$PROD_URL" "$LOCAL_URL" \
  --all-tables --precise --allow-root
echo "  ✅ Готово"

rm -f "$DUMP_FILE"

echo ""
echo "✨ Локальная БД синхронизирована с Timeweb. Откройте $LOCAL_URL"
