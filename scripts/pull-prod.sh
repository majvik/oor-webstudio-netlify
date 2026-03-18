#!/bin/bash
# Безопасный импорт: PROD → LOCAL только. Сервер не изменяется.
# Запуск из корня проекта: bash scripts/pull-prod.sh
# Требуется: .env с PROD_DB_ROOT_PASSWORD (и при необходимости REMOTE_*)

set -e
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$PROJECT_ROOT"

if [ ! -f .env ]; then
  echo "❌ Нет файла .env. Скопируйте .env.example в .env и задайте PROD_DB_ROOT_PASSWORD (и REMOTE_* при необходимости)."
  exit 1
fi

set -a
source .env
set +a

REMOTE_SSH="${REMOTE_SSH:-root@45.141.102.187}"
REMOTE_DB_CONTAINER="${REMOTE_DB_CONTAINER:-oor-mysql}"
REMOTE_PROJECT_PATH="${REMOTE_PROJECT_PATH:-/opt/oor-webstudio}"
REMOTE_UPLOADS_PATH="${REMOTE_UPLOADS_PATH:-$REMOTE_SSH:$REMOTE_PROJECT_PATH/wordpress-uploads}"
LOCAL_URL="${LOCAL_URL:-https://localhost:8443}"
PROD_URL="${PROD_URL:-https://outofrec.com}"
DB_NAME="${DB_NAME:-wordpress}"
DB_ROOT_PASSWORD="${DB_ROOT_PASSWORD:-rootpassword}"

if [ -z "${PROD_DB_ROOT_PASSWORD:-}" ]; then
  echo "❌ Задайте PROD_DB_ROOT_PASSWORD в .env (пароль root MySQL на сервере)."
  exit 1
fi

echo "📥 Односторонняя синхронизация PROD → LOCAL"
echo "   Сервер: $REMOTE_SSH"
echo "   Локальный URL после замены: $LOCAL_URL"
echo ""
echo "⚠️  ВНИМАНИЕ: локальная БД и (при FULL_UPLOADS_SYNC=1) uploads будут ПЕРЕЗАПИСАНЫ состоянием с сервера."
echo "   Если локалка новее и нужно обновить сервер — не запускайте этот скрипт. См. SAFETY-SYNC.md."
echo ""

BACKUP_FILE="local_backup_$(date +%F_%H%M).sql"
echo "🐘 1. Бэкап текущей локальной БД..."
if docker compose exec -T db mysqldump -u root -p"$DB_ROOT_PASSWORD" "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null; then
  echo "   ✅ Создан $BACKUP_FILE"
else
  rm -f "$BACKUP_FILE" 2>/dev/null
  echo "   ⚠️ Локальная БД не запущена или пуста — пропуск бэкапа"
fi

echo ""
echo "📡 2. Дамп БД с сервера и импорт локально..."
ssh -o StrictHostKeyChecking=accept-new "$REMOTE_SSH" "docker exec $REMOTE_DB_CONTAINER mysqldump -u root -p'$PROD_DB_ROOT_PASSWORD' --single-transaction --routines --triggers '$DB_NAME'" 2>/dev/null | docker compose exec -T db mysql -u root -p"$DB_ROOT_PASSWORD" "$DB_NAME"
echo "   ✅ Импорт завершён"

echo ""
echo "🔄 3. Search & Replace (PROD URL → LOCAL URL)..."
docker compose exec -T wordpress wp search-replace "$PROD_URL" "$LOCAL_URL" --all-tables --allow-root 2>/dev/null || \
  docker compose exec -T wordpress wp-cli search-replace "$PROD_URL" "$LOCAL_URL" --all-tables --allow-root
echo "   ✅ Готово"

echo ""
echo "📸 4. Синхронизация Uploads..."
mkdir -p "$PROJECT_ROOT/wordpress-uploads"
# Полная пересинхронизация (перезапись): FULL_UPLOADS_SYNC=1 bash scripts/pull-prod.sh
# По умолчанию подтягиваются только недостающие файлы (--ignore-existing).
RSYNC_EXTRA=""
if [ -n "${FULL_UPLOADS_SYNC:-}" ]; then
  echo "   (режим: полная перезапись из прода)"
  RSYNC_EXTRA=""
else
  echo "   (режим: только недостающие файлы; для перезаписи всех: FULL_UPLOADS_SYNC=1)"
  RSYNC_EXTRA="--ignore-existing"
fi
if rsync -avzP $RSYNC_EXTRA -e "ssh -o StrictHostKeyChecking=accept-new" "$REMOTE_UPLOADS_PATH/" "./wordpress-uploads/" 2>/dev/null; then
  echo "   ✅ Uploads обновлены"
else
  echo "   ⚠️ Не удалось rsync (проверьте REMOTE_UPLOADS_PATH и доступ по SSH). Путь: $REMOTE_UPLOADS_PATH"
fi

echo ""
echo "✨ Локалка приведена к состоянию сервера. Откройте $LOCAL_URL"
