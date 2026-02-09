#!/bin/bash
# Исправление проблем «Здоровье сайта» на проде.
# Запуск на сервере: bash fix-site-health-on-server.sh
# Или с локальной машины: ssh root@45.141.102.187 'REMOTE_PATH=/opt/oor-webstudio SITE_HOST=45.141.102.187.nip.io DOCKER_CONTAINER=oor-wordpress bash -s' < scripts/fix-site-health-on-server.sh
set -e

REMOTE_PATH="${REMOTE_PATH:-/opt/oor-webstudio}"
SITE_HOST="${SITE_HOST:-45.141.102.187.nip.io}"
WP_CONFIG="${REMOTE_PATH}/wp-config.php"
DOCKER_CONTAINER="${DOCKER_CONTAINER:-}"

echo "→ Путь к проекту: $REMOTE_PATH"
echo "→ Домен сайта: $SITE_HOST"

# --- 1. wp-config: отключить вывод ошибок в браузере ---
if [ -n "$DOCKER_CONTAINER" ] && docker exec "$DOCKER_CONTAINER" test -f /var/www/html/wp-config.php 2>/dev/null; then
  echo "→ Режим Docker: контейнер $DOCKER_CONTAINER"
  # В .env на хосте выставить WP_DEBUG=0
  if [ -f "$REMOTE_PATH/.env" ]; then
    sed -i 's/WP_DEBUG=1/WP_DEBUG=0/g' "$REMOTE_PATH/.env"
    echo "✅ В .env установлено WP_DEBUG=0"
  fi
  # В контейнере добавить WP_DEBUG_DISPLAY и display_errors, если ещё нет
  if ! docker exec "$DOCKER_CONTAINER" grep -q "WP_DEBUG_DISPLAY" /var/www/html/wp-config.php 2>/dev/null; then
    docker exec "$DOCKER_CONTAINER" sed -i "/define( 'WP_DEBUG', !!getenv_docker('WORDPRESS_DEBUG', '') );/a define( 'WP_DEBUG_DISPLAY', false );\\n@ini_set( 'display_errors', 0 );" /var/www/html/wp-config.php
    echo "✅ В wp-config (в контейнере) добавлены WP_DEBUG_DISPLAY и display_errors"
  fi
elif [ -f "$WP_CONFIG" ]; then
  BACKUP="${WP_CONFIG}.bak.$(date +%Y%m%d%H%M%S)"
  cp -a "$WP_CONFIG" "$BACKUP"
  echo "→ Создан бэкап: $BACKUP"
  sed -i 's/define\s*(\s*['\''"]WP_DEBUG['\''"].*true/define( '\''WP_DEBUG'\'', false/g' "$WP_CONFIG"
  sed -i 's/define\s*(\s*['\''"]WP_DEBUG_DISPLAY['\''"].*true/define( '\''WP_DEBUG_DISPLAY'\'', false/g' "$WP_CONFIG"
  echo "✅ wp-config.php: отладочный вывод в браузере отключён"
else
  echo "⚠ wp-config не найден. Задайте DOCKER_CONTAINER=oor-wordpress (при Docker) или REMOTE_PATH с wp-config.php."
  exit 1
fi

# --- 2. /etc/hosts: loopback для домена (чтобы REST API / loopback не таймаутил) ---
if [ -w /etc/hosts ] || [ "$(id -u)" = "0" ]; then
  if ! grep -q "$SITE_HOST" /etc/hosts; then
    echo "127.0.0.1    $SITE_HOST" >> /etc/hosts
    echo "✅ В /etc/hosts добавлено: 127.0.0.1 $SITE_HOST"
  else
    echo "→ /etc/hosts уже содержит $SITE_HOST"
  fi
else
  echo "⚠ Нет прав на /etc/hosts. Добавьте вручную: 127.0.0.1 $SITE_HOST"
fi

# --- 3. Подсказка по cron (Action Scheduler) ---
echo ""
echo "--- Планировщик (cron) ---"
echo "Чтобы убрать предупреждение о пропущенных заданиях, добавьте в crontab -e (на сервере):"
echo ""
echo "  * * * * * curl -s -o /dev/null https://${SITE_HOST}/wp-cron.php?doing_wp_cron 2>/dev/null || true"
echo ""
echo "Или, если установлен WP-CLI в контейнере WordPress:"
echo "  * * * * * docker exec <wordpress-container> wp cron event run --due-now 2>/dev/null || true"
echo ""

# Перезапуск PHP-FPM при необходимости (если не Docker — раскомментировать и подставить сервис)
# systemctl reload php8.3-fpm 2>/dev/null || true
echo "Готово. Проверьте «Здоровье сайта» в админке WordPress."
