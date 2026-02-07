#!/bin/bash
# Восстановление на VPS после переноса.
# Запуск из корня проекта на VPS: bash scripts/restore-on-vps.sh
# Ожидает: импортированный дамп уже выполнен ИЛИ файл дампа передан аргументом.
# После импорта можно вызвать с OLD_URL и NEW_URL для search-replace.

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$PROJECT_ROOT"

if [ ! -f .env ]; then
  echo "❌ Файл .env не найден. Скопируйте .env.example в .env и заполните (пароли, WP_URL)."
  exit 1
fi

set -a
source .env
set +a

DB_NAME="${DB_NAME:-wordpress}"
DB_ROOT_PASSWORD="${DB_ROOT_PASSWORD:-rootpassword}"

# Импорт дампа: если передан аргумент — файл дампа
SQL_FILE="$1"
if [ -n "$SQL_FILE" ] && [ -f "$SQL_FILE" ]; then
  echo "📥 Импорт базы из $SQL_FILE..."
  docker compose exec -T db mysql -u root -p"$DB_ROOT_PASSWORD" "$DB_NAME" < "$SQL_FILE"
  echo "   ✅ Импорт завершён."
fi

# Замена URL в БД (обязательно после переноса)
OLD_URL="${OLD_URL:-http://localhost:8080}"
NEW_URL="${NEW_URL:-}"

if [ -z "$NEW_URL" ]; then
  echo ""
  echo "Чтобы заменить старые URL на новый домен, задайте переменные:"
  echo "  OLD_URL=\"http://localhost:8080\" NEW_URL=\"https://ваш-домен.ru\" $0"
  echo "  или отредактируйте WP_URL в .env и выполните:"
  echo "  docker compose exec wordpress wp-cli search-replace \"\$OLD_URL\" \"\$WP_URL\" --all-tables --allow-root"
  exit 0
fi

echo ""
echo "🔄 Замена URL в базе: $OLD_URL → $NEW_URL"
docker compose exec wordpress wp-cli search-replace "$OLD_URL" "$NEW_URL" --all-tables --allow-root
echo "   ✅ Готово."
echo ""
echo "Проверьте сайт в браузере и при необходимости замените другие варианты URL (например https://localhost:8443)."
