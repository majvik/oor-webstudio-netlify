#!/bin/bash
set -e

# 1. docker-entrypoint.sh php-fpm: инициализирует WordPress
#    (копирует ядро в /var/www/html, создаёт wp-config.php), затем exec php-fpm.
docker-entrypoint.sh php-fpm &

# 2. Ждём появления сокета PHP-FPM (значит entrypoint + php-fpm готовы)
i=0
while [ ! -S /tmp/php-fpm.sock ]; do
    i=$((i+1))
    if [ $i -ge 60 ]; then
        echo "ERR: php-fpm socket not found after 30s" >&2
        break
    fi
    sleep 0.5
done

# 3. Накладываем наш wp-content поверх дефолтного
#    (при сборке файлы сохранены в /usr/src/ — вне VOLUME)
if [ -d /usr/src/wp-content-custom ]; then
    echo "Copying custom wp-content..."
    cp -a /usr/src/wp-content-custom/. /var/www/html/wp-content/
fi

if [ -d /usr/src/wp-uploads ]; then
    echo "Copying uploads..."
    cp -a /usr/src/wp-uploads/. /var/www/html/wp-content/uploads/
fi

# 4. SSL для Managed MySQL на Timeweb (--require_secure_transport=ON)
if [ -f /var/www/html/wp-config.php ] && ! grep -q 'MYSQL_CLIENT_FLAGS' /var/www/html/wp-config.php; then
    sed -i "/\/\* That's all/i define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);" /var/www/html/wp-config.php
fi

# 5. Права
chown -R www-data:www-data /var/www/html 2>/dev/null || true
chmod -R a+rX /var/www/html

sleep 1
exec nginx -g 'daemon off;'
