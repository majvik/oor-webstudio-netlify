# Деплой на Timeweb Cloud через Dockerfile (из GitHub)

На Timeweb уже настроена сборка Docker по последнему коммиту в GitHub. В репозитории в **корне** лежит один **Dockerfile**: образ собирается из него, в одном контейнере работают Nginx и WordPress (PHP-FPM). База данных — отдельно (managed MySQL на Timeweb или своя).

## Что в репозитории

- **`Dockerfile`** (корень) — образ для Timeweb: WordPress PHP-FPM + Nginx. В образ копируется **весь `wp-content`** из репозитория, включая **`wp-content/uploads`** и **`wordpress-uploads`** (полный слепок: темы, плагины, mu-plugins, медиа). Каталоги `cache`, `upgrade`, `wflogs` в репо не хранятся.
- **`nginx.prod.conf`** — конфиг Nginx внутри контейнера: порт 80, PHP через `127.0.0.1:9000`. SSL снимается на стороне платформы (заголовок `X-Forwarded-Proto` передаётся в PHP).

## Другой домен на проде

В образ встроен mu-plugin **OOR Domain from environment**: он подставляет адрес сайта из переменных окружения и не трогает БД.

В настройках приложения в Timeweb задайте:

- **`WP_HOME`** — полный URL прода (например `https://ваш-сайт.ru`).
- **`WP_SITEURL`** — тот же URL (или отдельный адрес админки, если нужен).

Тогда ссылки и редиректы будут вести на новый домен без правки `siteurl`/`home` в базе.

## Настройка в Timeweb Cloud

1. **App Platform** → создать приложение → тип **Dockerfile**.
2. Подключить репозиторий GitHub (ветка `main`, по последнему коммиту собирается образ).
3. **Путь к Dockerfile:** корень репозитория, файл `Dockerfile`.
4. **Переменные окружения** (обязательно):
   - `WORDPRESS_DB_HOST`, `WORDPRESS_DB_USER`, `WORDPRESS_DB_PASSWORD`, `WORDPRESS_DB_NAME` — доступ к БД.
   - **`WP_HOME`** и **`WP_SITEURL`** — полный URL прода (например `https://ваш-сайт.ru`), чтобы сайт работал на новом домене без правки БД.
   - При необходимости: `WORDPRESS_DEBUG`, `WORDPRESS_CONFIG_EXTRA` и т.п.
5. Порт приложения: **80** (внутри контейнера слушает 80).
6. Деплой: при каждом push в `main` платформа пересобирает образ и перезапускает контейнер.

## База данных

Используется **внешний сервер с MySQL/MariaDB** (не в контейнере). Варианты:

- **Managed MySQL** в Timeweb (создать БД, взять хост/логин/пароль и прописать в `WORDPRESS_DB_*`).
- Либо свой MySQL на отдельном сервере — тогда `WORDPRESS_DB_HOST` = его адрес.

### Как попадают данные в Managed MySQL

Managed MySQL создаётся **пустой**. Контент (посты, настройки, пользователи) нужно один раз загрузить из текущего WordPress.

**Вариант А: перенос с локалки (docker-compose)**

1. Сделать дамп локальной БД:
   ```bash
   docker compose exec db mysqldump -u root -p"$DB_ROOT_PASSWORD" wordpress > wordpress-dump.sql
   ```
   (пароль из `.env`: `DB_ROOT_PASSWORD`.)

2. В дампе заменить старый URL на продовый (чтобы ссылки и редиректы работали):
   ```bash
   sed -i '' 's|https://localhost:8443|https://majvik-oor-webstudio-netlify-b925.twc1.net|g' wordpress-dump.sql
   ```
   Или вручную в редакторе.

3. Импортировать дамп в Managed MySQL:
   - В панели Timeweb у Managed MySQL есть **хост**, **порт**, **пользователь**, **пароль**, **имя БД**. Подключитесь с любой машины, где есть `mysql`:
     ```bash
     mysql -h <хост Timeweb> -P <порт> -u <пользователь> -p <имя_БД> < wordpress-dump.sql
     ```
   - Либо используйте веб-консоль/phpMyAdmin в Timeweb (если есть), загрузите туда `wordpress-dump.sql`.

4. В приложении (переменные окружения) укажите эти же хост, пользователь, пароль и имя БД в `WORDPRESS_DB_HOST`, `WORDPRESS_DB_USER`, `WORDPRESS_DB_PASSWORD`, `WORDPRESS_DB_NAME`. После деплоя WordPress будет использовать уже заполненную БД.

**Вариант Б: новая установка**

Если контент не нужен: оставьте Managed MySQL пустой, укажите в приложении `WORDPRESS_DB_*`. При первом заходе на сайт откроется стандартная установка WordPress («знаменитые 5 минут»). После неё данные будут уже в этой БД.

## Медиа (uploads)

В образ попадает **полный слепок**: в репозитории коммитятся `wp-content/uploads` и при необходимости `wordpress-uploads`, в Dockerfile копируется весь `wp-content/`, поэтому медиа из репо оказываются в образе и работают на проде без отдельного volume.

## Проверка

После деплоя сайт открывается по выданному Timeweb URL. В настройках WordPress (или через `WP_HOME`/`WP_SITEURL` в env) задайте итоговый домен.
