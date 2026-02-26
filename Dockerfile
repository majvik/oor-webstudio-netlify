# Образ для Timeweb Cloud (сборка из GitHub по последнему коммиту).
# Один контейнер: WordPress (PHP-FPM) + Nginx. БД — внешняя (переменные WORDPRESS_DB_*).
FROM wordpress:php8.3-fpm

# Nginx
RUN apt-get update && apt-get install -y --no-install-recommends nginx netcat-openbsd \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /etc/nginx/sites-enabled/default \
    && ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# WP-CLI
RUN curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp \
    && echo '#!/bin/bash\nwp --allow-root "$@"' > /usr/local/bin/wp-cli && chmod +x /usr/local/bin/wp-cli

# Лимиты PHP
RUN echo "upload_max_filesize = 512M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 512M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 600" >> /usr/local/etc/php/conf.d/uploads.ini

# Конфиг Nginx (один контейнер) и PHP-FPM: сокет в /tmp (работает при запуске не от root)
COPY nginx.prod.conf /etc/nginx/conf.d/default.conf
RUN printf '[www]\nlisten = /tmp/php-fpm.sock\nlisten.owner = www-data\nlisten.group = www-data\nlisten.mode = 0666\n' > /usr/local/etc/php-fpm.d/zz-socket.conf

# Полный слепок wp-content из репозитория (темы, плагины, mu-plugins, uploads, языки и т.д.)
COPY wp-content/ /var/www/html/wp-content/
RUN chown -R www-data:www-data /var/www/html

# Запуск: entrypoint WordPress (создаёт wp-config из env), chown + chmod (если контейнер не root — chown не сработает, chmod даст доступ), затем php-fpm и nginx
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["sh", "-c", "chown -R www-data:www-data /var/www/html 2>/dev/null; chmod -R a+rX /var/www/html; php-fpm & i=0; while [ ! -S /tmp/php-fpm.sock ]; do i=$((i+1)); [ $i -ge 30 ] && break; sleep 0.5; done; exec nginx -g 'daemon off;'"]

EXPOSE 80
