#!/bin/bash
set -e

# 1. docker-entrypoint.sh php-fpm: инициализирует WordPress
#    (копирует ядро в /var/www/html, создаёт wp-config.php), затем exec php-fpm.
docker-entrypoint.sh php-fpm &

# 2. Ждём появления сокета PHP-FPM
i=0
while [ ! -S /tmp/php-fpm.sock ]; do
    i=$((i+1))
    if [ $i -ge 60 ]; then
        echo "ERR: php-fpm socket not found after 30s" >&2
        break
    fi
    sleep 0.5
done

# 3. Накладываем наш wp-content поверх дефолтного (symlink — мгновенно, без cp)
if [ -d /usr/src/wp-content-custom ]; then
    echo "Linking custom wp-content..."
    for item in /usr/src/wp-content-custom/*; do
        base=$(basename "$item")
        rm -rf "/var/www/html/wp-content/$base"
        ln -sf "$item" "/var/www/html/wp-content/$base"
    done
fi

if [ -d /usr/src/wp-uploads ]; then
    echo "Linking uploads..."
    rm -rf /var/www/html/wp-content/uploads
    ln -sf /usr/src/wp-uploads /var/www/html/wp-content/uploads
fi

# 4. SSL для Managed MySQL + отключение WP-Cron на каждый запрос (вместо этого — системный cron)
if [ -f /var/www/html/wp-config.php ]; then
    if ! grep -q 'MYSQL_CLIENT_FLAGS' /var/www/html/wp-config.php; then
        sed -i "/\/\* That's all/i define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);" /var/www/html/wp-config.php
    fi
    if ! grep -q 'DISABLE_WP_CRON' /var/www/html/wp-config.php; then
        sed -i "/\/\* That's all/i define('DISABLE_WP_CRON', true);" /var/www/html/wp-config.php
    fi
fi

# 5. Создаём каталог для nginx fastcgi кеша
mkdir -p /tmp/nginx-cache
chown www-data:www-data /tmp/nginx-cache

# 6. Права (только на wp-config и корень, без рекурсии по всем файлам)
chown www-data:www-data /var/www/html/wp-config.php 2>/dev/null || true
chown -R www-data:www-data /var/www/html/wp-content/cache 2>/dev/null || true

# 7. WooCommerce + переводы в фоне (ждём готовности WP, не блокируем nginx)
(
    WC_VERSION="${WC_VERSION:-10.6.1}"

    # Ждём wp-config.php (docker-entrypoint создаёт его асинхронно)
    for _i in $(seq 1 30); do
        [ -f /var/www/html/wp-config.php ] && break
        sleep 1
    done

    # Ждём доступности БД
    for _i in $(seq 1 30); do
        wp db check --allow-root >/dev/null 2>&1 && break
        sleep 2
    done

    echo "Installing WooCommerce $WC_VERSION..."
    if wp plugin is-installed woocommerce --allow-root 2>/dev/null; then
        wp plugin activate woocommerce --allow-root 2>/dev/null || true
    else
        rm -rf /var/www/html/wp-content/plugins/woocommerce 2>/dev/null
        wp plugin install woocommerce --version="$WC_VERSION" --activate --allow-root 2>&1 || \
            echo "WooCommerce install failed" >&2
    fi

    echo "Installing translations..."
    wp language core install ru_RU --allow-root 2>/dev/null || true
    wp language plugin install --all ru_RU --allow-root 2>/dev/null || true
    echo "Plugin setup complete."
) &

# 8. Фоновый wp-cron каждые 5 минут (заменяет встроенный pseudo-cron)
(while true; do
    sleep 300
    php /var/www/html/wp-cron.php >/dev/null 2>&1 || true
done) &

exec nginx -g 'daemon off;'
