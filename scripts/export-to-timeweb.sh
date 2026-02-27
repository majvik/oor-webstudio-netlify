#!/usr/bin/env bash
# Экспорт локальной БД и импорт в Managed MySQL на Timeweb.
# Заменяет локальные URL на продовые через wp-cli (корректно обрабатывает сериализованные данные).
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$PROJECT_ROOT"

if [ ! -f .env ]; then
  echo "❌ Нет .env — скопируйте из .env.example и заполните"
  exit 1
fi

set -a
# shellcheck source=/dev/null
. .env
set +a

LOCAL_URL="${WP_URL:-http://localhost:8080}"
PROD_URL="${PROD_WP_URL:-https://majvik-oor-webstudio-netlify-b925.twc1.net}"

PROD_DB_HOST="${WORDPRESS_DB_PUBLIC_HOST:-${WORDPRESS_DB_HOST:?Задайте WORDPRESS_DB_HOST в .env}}"
PROD_DB_NAME="${WORDPRESS_DB_NAME:?Задайте WORDPRESS_DB_NAME в .env}"
PROD_DB_USER="${WORDPRESS_DB_USER:?Задайте WORDPRESS_DB_USER в .env}"
PROD_DB_PASS="${WORDPRESS_DB_PASSWORD:?Задайте WORDPRESS_DB_PASSWORD в .env}"
PROD_DB_PORT="${WORDPRESS_DB_PORT:-3306}"

mkdir -p "$PROJECT_ROOT/data"
DUMP="$PROJECT_ROOT/data/export-to-timeweb.sql"

echo ""
echo "=== Экспорт локальной БД → Timeweb ==="
echo ""
echo "  Локальный URL:  $LOCAL_URL"
echo "  Продовый URL:   $PROD_URL"
echo "  Прод БД:        $PROD_DB_USER@$PROD_DB_HOST:$PROD_DB_PORT/$PROD_DB_NAME"
echo ""

# 1. Замена URL через wp-cli search-replace (корректно обрабатывает сериализованные данные)
echo "→ [1/5] Замена URL в локальной БД через wp-cli..."
LOCAL_URL_NO_SLASH="${LOCAL_URL%/}"
PROD_URL_NO_SLASH="${PROD_URL%/}"

ALL_LOCAL_URLS=("$LOCAL_URL_NO_SLASH")
for EXTRA in "https://localhost:8443" "http://localhost:8080" "https://45.141.102.187.nip.io"; do
  if [ "$EXTRA" != "$LOCAL_URL_NO_SLASH" ]; then
    ALL_LOCAL_URLS+=("$EXTRA")
  fi
done

for OLD_URL in "${ALL_LOCAL_URLS[@]}"; do
  echo "  $OLD_URL → $PROD_URL_NO_SLASH"
  docker compose exec -T wordpress wp search-replace \
    "$OLD_URL" "$PROD_URL_NO_SLASH" \
    --all-tables --precise --allow-root --quiet 2>/dev/null || true
done
echo "  Замена завершена"

# 2. Дамп БД (уже с продовыми URL)
echo "→ [2/5] Дамп локальной БД..."
docker compose exec -T db mysqldump \
  -u root -p"${DB_ROOT_PASSWORD:?DB_ROOT_PASSWORD не задан}" \
  "${DB_NAME:-wordpress}" \
  --single-transaction --quick --routines --triggers \
  > "$DUMP"

DUMP_SIZE=$(wc -c < "$DUMP" | tr -d ' ')
echo "  Дамп: $(( DUMP_SIZE / 1024 )) KB"

if [ "$DUMP_SIZE" -lt 1000 ]; then
  echo "❌ Дамп подозрительно маленький (${DUMP_SIZE} байт). Проверьте локальную БД."
  exit 1
fi

# 3. Откат URL в локальной БД обратно на локальные
echo "→ [3/5] Откат URL в локальной БД..."
for OLD_URL in "${ALL_LOCAL_URLS[@]}"; do
  docker compose exec -T wordpress wp search-replace \
    "$PROD_URL_NO_SLASH" "$OLD_URL" \
    --all-tables --precise --allow-root --quiet 2>/dev/null || true
  break  # откатываем только на основной LOCAL_URL
done
echo "  Откат завершён"

# 4. Проверка подключения к Timeweb
echo "→ [4/5] Проверка подключения к Managed MySQL на Timeweb..."
if ! docker run --rm mysql:8.0 \
  mysql -h "$PROD_DB_HOST" -P "$PROD_DB_PORT" -u "$PROD_DB_USER" -p"$PROD_DB_PASS" \
  --ssl-mode=REQUIRED -e "SELECT 1" "$PROD_DB_NAME" > /dev/null 2>&1; then
  echo "❌ Не удалось подключиться к $PROD_DB_HOST:$PROD_DB_PORT"
  echo "   Проверьте WORDPRESS_DB_* в .env и доступность MySQL."
  exit 1
fi
echo "  Подключение OK"

# 5. Импорт
echo "→ [5/5] Импорт дампа в Managed MySQL..."
echo ""
read -p "⚠️  Это ПЕРЕЗАПИШЕТ данные в $PROD_DB_NAME на $PROD_DB_HOST. Продолжить? (y/N) " CONFIRM
if [ "$CONFIRM" != "y" ] && [ "$CONFIRM" != "Y" ]; then
  echo "Отменено."
  exit 0
fi

docker run --rm -i mysql:8.0 \
  mysql -h "$PROD_DB_HOST" -P "$PROD_DB_PORT" -u "$PROD_DB_USER" -p"$PROD_DB_PASS" \
  --ssl-mode=REQUIRED "$PROD_DB_NAME" < "$DUMP"

echo ""
echo "✅ Готово! БД импортирована в $PROD_DB_NAME на $PROD_DB_HOST"
echo ""
echo "Проверьте сайт: $PROD_URL"
echo "Если домен отличается — задайте WP_HOME и WP_SITEURL в переменных приложения на Timeweb."
