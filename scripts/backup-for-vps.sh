#!/bin/bash
# Создаёт архив для переноса проекта на VPS:
# - дамп MySQL
# - wp-content, wordpress-uploads, nginx.conf, ssl (если есть PEM)
# Запуск: из корня проекта — bash scripts/backup-for-vps.sh

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$PROJECT_ROOT"

BACKUP_DIR="backup-vps"
DATE=$(date +%Y%m%d-%H%M)
mkdir -p "$BACKUP_DIR"

# Загружаем .env если есть
if [ -f .env ]; then
  set -a
  source .env
  set +a
fi

DB_NAME="${DB_NAME:-wordpress}"
DB_ROOT_PASSWORD="${DB_ROOT_PASSWORD:-rootpassword}"

echo "📦 Бэкап для миграции на VPS"
echo "   Каталог: $BACKUP_DIR"
echo ""

# 1. Дамп MySQL (контейнеры должны быть запущены)
if docker compose ps db 2>/dev/null | grep -q "Up"; then
  echo "1/3 Экспорт базы данных..."
  docker compose exec -T db mysqldump -u root -p"$DB_ROOT_PASSWORD" \
    --single-transaction --routines --triggers \
    "$DB_NAME" > "$BACKUP_DIR/mysql-$DATE.sql"
  echo "   ✅ $BACKUP_DIR/mysql-$DATE.sql"
else
  echo "1/3 Контейнер db не запущен — пропускаем дамп MySQL."
  echo "   Запустите: docker compose up -d"
fi

# 2. Архив файлов
echo "2/3 Архив wp-content, wordpress-uploads, nginx.conf, ssl..."
TAR_FILE="$BACKUP_DIR/files-$DATE.tar.gz"
tar czf "$TAR_FILE" \
  --exclude='wp-content/cache' \
  --exclude='wp-content/upgrade' \
  --exclude='wp-content/wflogs' \
  --exclude='wp-content/debug.log' \
  wp-content \
  wordpress-uploads \
  nginx.conf \
  ssl 2>/dev/null || true
# ssl может не содержать .pem (в .gitignore) — тогда в архиве будет только папка
if [ -f "$TAR_FILE" ]; then
  echo "   ✅ $TAR_FILE"
else
  echo "   ⚠️ Не удалось создать архив (проверьте наличие wp-content, wordpress-uploads)"
fi

# 3. Копия .env.example (на VPS создают .env из него)
echo "3/3 Копия .env.example..."
cp .env.example "$BACKUP_DIR/.env.example"
echo "   ✅ $BACKUP_DIR/.env.example"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Бэкап готов в каталоге: $BACKUP_DIR"
echo ""
echo "Для переноса на VPS:"
echo "  1. Скопируйте содержимое $BACKUP_DIR на VPS"
echo "  2. На VPS: клонируйте репозиторий и подставьте файлы из бэкапа"
echo "  3. Следуйте инструкции в VPS-MIGRATION.md"
echo ""
