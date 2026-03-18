#!/usr/bin/env bash
# Быстрая синхронизация: PROD → LOCAL (БД + новые uploads).
# Использование:
#   bash scripts/sync.sh          — полная синхронизация (БД + uploads)
#   bash scripts/sync.sh db       — только БД
#   bash scripts/sync.sh uploads  — только uploads
#
# Требуется: .env с WORDPRESS_DB_* и SYNC_API_TOKEN
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$PROJECT_ROOT"

if [ ! -f .env ]; then
  echo "❌ Нет .env"
  exit 1
fi

set -a; . .env; set +a

MODE="${1:-all}"

PROD_URL="${PROD_WP_URL:-https://outofrec.com}"
SYNC_TOKEN="${SYNC_API_TOKEN:?Задайте SYNC_API_TOKEN в .env}"
LOCAL_URL="${LOCAL_URL:-https://localhost:8443}"
LOCAL_UPLOADS="$PROJECT_ROOT/wordpress-uploads"

# --- DB sync ---
sync_db() {
    echo ""
    echo "━━━ БД: Timeweb → Local ━━━"

    TW_HOST="${WORDPRESS_DB_PUBLIC_HOST:-${WORDPRESS_DB_HOST}}"
    TW_PORT="${WORDPRESS_DB_PORT:-3306}"
    TW_DB="${WORDPRESS_DB_NAME}"
    TW_USER="${WORDPRESS_DB_USER}"
    TW_PASS="${WORDPRESS_DB_PASSWORD}"
    LOCAL_DB="${DB_NAME:-wordpress}"
    LOCAL_DB_PASS="${DB_ROOT_PASSWORD:-rootpassword}"

    BACKUP="local_backup_$(date +%F_%H%M).sql"
    docker compose exec -T db mysqldump -u root -p"$LOCAL_DB_PASS" "$LOCAL_DB" > "$BACKUP" 2>/dev/null && \
        echo "  Бэкап: $BACKUP" || echo "  (бэкап пропущен)"

    echo "  Дамп с Timeweb..."
    DUMP=$(mktemp)
    docker run --rm mysql:8.0 mysqldump \
        -h "$TW_HOST" -P "$TW_PORT" -u "$TW_USER" -p"$TW_PASS" \
        --ssl-mode=REQUIRED --skip-lock-tables --quick --no-tablespaces \
        "$TW_DB" > "$DUMP" 2>/dev/null || true

    DUMP_SIZE=$(wc -c < "$DUMP" | tr -d ' ')
    if [ "$DUMP_SIZE" -lt 1000 ]; then
        echo "  ❌ Дамп слишком маленький (${DUMP_SIZE} байт)"
        rm -f "$DUMP"
        return 1
    fi
    echo "  Дамп: $(( DUMP_SIZE / 1024 )) KB"

    echo "  Импорт..."
    docker compose exec -T db mysql -u root -p"$LOCAL_DB_PASS" "$LOCAL_DB" < "$DUMP"
    rm -f "$DUMP"

    echo "  URL replace: $PROD_URL → $LOCAL_URL"
    docker compose exec -T wordpress wp search-replace \
        "$PROD_URL" "$LOCAL_URL" --all-tables --precise --allow-root 2>/dev/null || true

    echo "  ✅ БД синхронизирована"
}

# --- Uploads sync ---
sync_uploads() {
    echo ""
    echo "━━━ Uploads: Prod → Local (только новые) ━━━"

    echo "  Получаю список файлов с прода..."
    PROD_LIST=$(mktemp)
    curl -sf -H "X-Sync-Token: $SYNC_TOKEN" "$PROD_URL/wp-json/oor-sync/v1/uploads-list" > "$PROD_LIST"

    if [ ! -s "$PROD_LIST" ]; then
        echo "  ❌ Не удалось получить список (проверьте SYNC_API_TOKEN и доступ к $PROD_URL)"
        rm -f "$PROD_LIST"
        return 1
    fi

    PROD_COUNT=$(python3 -c "import json,sys; print(len(json.load(sys.stdin)))" < "$PROD_LIST")
    echo "  На проде: $PROD_COUNT файлов"

    # Локальный список
    LOCAL_LIST=$(mktemp)
    (cd "$LOCAL_UPLOADS" && find . -type f | sed 's|^\./||' | sort) > "$LOCAL_LIST" 2>/dev/null || true
    LOCAL_COUNT=$(wc -l < "$LOCAL_LIST" | tr -d ' ')
    echo "  Локально: $LOCAL_COUNT файлов"

    # Найти отсутствующие
    MISSING=$(python3 -c "
import json, sys
prod = set(json.load(open('$PROD_LIST')))
local = set(line.strip() for line in open('$LOCAL_LIST') if line.strip())
missing = sorted(prod - local)
if missing:
    print(json.dumps(missing))
else:
    print('')
")

    rm -f "$PROD_LIST" "$LOCAL_LIST"

    if [ -z "$MISSING" ]; then
        echo "  ✅ Uploads в sync, новых файлов нет"
        return 0
    fi

    MISSING_COUNT=$(echo "$MISSING" | python3 -c "import json,sys; print(len(json.load(sys.stdin)))")
    echo "  Новых файлов: $MISSING_COUNT"
    echo "  Скачиваю архив..."

    TAR=$(mktemp).tar.gz
    curl -sf -X POST \
        -H "X-Sync-Token: $SYNC_TOKEN" \
        -H "Content-Type: application/json" \
        -d "$MISSING" \
        "$PROD_URL/wp-json/oor-sync/v1/uploads-tar" \
        -o "$TAR"

    if [ ! -s "$TAR" ]; then
        echo "  ❌ Не удалось скачать архив"
        rm -f "$TAR"
        return 1
    fi

    TAR_SIZE=$(du -h "$TAR" | cut -f1)
    echo "  Архив: $TAR_SIZE"
    echo "  Распаковываю..."
    mkdir -p "$LOCAL_UPLOADS"
    tar xzf "$TAR" -C "$LOCAL_UPLOADS/"
    rm -f "$TAR"

    echo "  ✅ $MISSING_COUNT файлов загружено"
}

# --- Main ---
echo "🔄 Sync: PROD → LOCAL"
echo "   Прод: $PROD_URL"
echo "   Режим: $MODE"

case "$MODE" in
    db)      sync_db ;;
    uploads) sync_uploads ;;
    all)     sync_db; sync_uploads ;;
    *)       echo "Использование: sync.sh [db|uploads|all]"; exit 1 ;;
esac

echo ""
echo "✨ Готово! Откройте $LOCAL_URL"
