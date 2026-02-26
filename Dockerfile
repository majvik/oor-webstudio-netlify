# Образ для Timeweb Cloud (сборка из GitHub по последнему коммиту).
# Один контейнер: WordPress (PHP-FPM) + Nginx. БД — внешняя (переменные WORDPRESS_DB_*).
FROM wordpress:php8.3-fpm

# Nginx
RUN apt-get update && apt-get install -y --no-install-recommends nginx \
    && rm -rf /var/lib/apt/lists/* \
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

# Конфиг Nginx (один контейнер: fastcgi 127.0.0.1:9000)
COPY nginx.prod.conf /etc/nginx/conf.d/default.conf

# Полный слепок wp-content из репозитория (темы, плагины, mu-plugins, uploads, языки и т.д.)
COPY wp-content/ /var/www/html/wp-content/

# Запуск: entrypoint WordPress (создаёт wp-config из env), затем php-fpm в фоне и nginx в foreground
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["sh", "-c", "php-fpm & exec nginx -g 'daemon off;'"]

EXPOSE 80
