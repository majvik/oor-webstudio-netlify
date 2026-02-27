#!/usr/bin/env bash
# Экспорт локальной БД и импорт в Managed MySQL на Timeweb.
# Заменяет локальные URL на продовый перед импортом.
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

# 1. Дамп локальной БД
echo "→ [1/4] Дамп локальной БД..."
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

# 2. Замена URL (все варианты: с/без trailing slash, http/https)
echo "→ [2/4] Замена URL: $LOCAL_URL → $PROD_URL"

LOCAL_URL_NO_SLASH="${LOCAL_URL%/}"
PROD_URL_NO_SLASH="${PROD_URL%/}"

sed -i.bak \
  -e "s|${LOCAL_URL_NO_SLASH}|${PROD_URL_NO_SLASH}|g" \
  "$DUMP"

# Дополнительно: если в БД есть https://localhost:8443 или другие варианты
for OLD_URL in "https://localhost:8443" "http://localhost:8080" "https://45.141.102.187.nip.io"; do
  if grep -q "$OLD_URL" "$DUMP" 2>/dev/null; then
    echo "  Также заменяю: $OLD_URL → $PROD_URL_NO_SLASH"
    sed -i -e "s|${OLD_URL}|${PROD_URL_NO_SLASH}|g" "$DUMP"
  fi
done

rm -f "${DUMP}.bak"

# 3. Проверка подключения к Timeweb
echo "→ [3/4] Проверка подключения к Managed MySQL на Timeweb..."
if ! docker run --rm mysql:8.0 \
  mysql -h "$PROD_DB_HOST" -P "$PROD_DB_PORT" -u "$PROD_DB_USER" -p"$PROD_DB_PASS" \
  --ssl-mode=REQUIRED -e "SELECT 1" "$PROD_DB_NAME" > /dev/null 2>&1; then
  echo "❌ Не удалось подключиться к $PROD_DB_HOST:$PROD_DB_PORT"
  echo "   Проверьте WORDPRESS_DB_* в .env и доступность MySQL."
  exit 1
fi
echo "  Подключение OK"

# 4. Импорт
echo "→ [4/4] Импорт дампа в Managed MySQL..."
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
