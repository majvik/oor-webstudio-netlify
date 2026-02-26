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

# Лимиты PHP + OPcache для ускорения
RUN echo "upload_max_filesize = 512M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 512M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 600" >> /usr/local/etc/php/conf.d/uploads.ini
RUN docker-php-ext-install opcache \
    && printf 'opcache.enable=1\nopcache.memory_consumption=128\nopcache.max_accelerated_files=10000\nopcache.revalidate_freq=60\nopcache.interned_strings_buffer=16\n' > /usr/local/etc/php/conf.d/opcache.ini

# Конфиг Nginx: один server block (убираем конфликт server_name "_" и лишние конфиги)
RUN rm -f /etc/nginx/conf.d/*.conf /etc/nginx/sites-enabled/* \
    && sed -i '/sites-enabled/s/^/# /' /etc/nginx/nginx.conf
COPY nginx.prod.conf /etc/nginx/conf.d/default.conf
RUN sed -i 's/^listen = .*/listen = \/tmp\/php-fpm.sock/' /usr/local/etc/php-fpm.d/www.conf \
    && echo 'listen.owner = www-data' >> /usr/local/etc/php-fpm.d/www.conf \
    && echo 'listen.group = www-data' >> /usr/local/etc/php-fpm.d/www.conf \
    && echo 'listen.mode = 0666' >> /usr/local/etc/php-fpm.d/www.conf
RUN printf '[www]\nlisten = /tmp/php-fpm.sock\nlisten.owner = www-data\nlisten.group = www-data\nlisten.mode = 0666\ncatch_workers_output = yes\n' > /usr/local/etc/php-fpm.d/zz-socket.conf
RUN echo "log_errors = On" >> /usr/local/etc/php/conf.d/uploads.ini && echo "error_log = /dev/stderr" >> /usr/local/etc/php/conf.d/uploads.ini

# wp-content и uploads сохраняем ВНЕ /var/www/html — базовый образ
# объявляет VOLUME /var/www/html, и всё, что туда пишется при сборке,
# отбрасывается при запуске контейнера.
COPY wp-content/ /usr/src/wp-content-custom/
COPY wordpress-uploads/ /usr/src/wp-uploads/
RUN chown -R www-data:www-data /usr/src/wp-content-custom /usr/src/wp-uploads

# Скрипт запуска: вызывает docker-entrypoint.sh php-fpm (инициализация WP + запуск FPM), затем nginx
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

ENTRYPOINT []
CMD ["/usr/local/bin/start.sh"]

EXPOSE 80
