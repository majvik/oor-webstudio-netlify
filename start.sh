#!/bin/bash
set -e

# docker-entrypoint.sh с аргументом php-fpm запускает инициализацию WordPress:
# копирует ядро в /var/www/html, создаёт wp-config.php из переменных окружения,
# затем exec php-fpm. Запускаем в фоне — php-fpm остаётся работать как демон.
docker-entrypoint.sh php-fpm &

# Ждём появления сокета PHP-FPM
i=0
while [ ! -S /tmp/php-fpm.sock ]; do
    i=$((i+1))
    if [ $i -ge 60 ]; then
        echo "ERR: php-fpm socket not found after 30s" >&2
        break
    fi
    sleep 0.5
done

# Права на файлы WP (entrypoint мог создать новые)
chown -R www-data:www-data /var/www/html 2>/dev/null || true
chmod -R a+rX /var/www/html

sleep 1
exec nginx -g 'daemon off;'
