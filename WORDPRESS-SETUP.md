# Установка и настройка WordPress темы

> **Версия:** 2.0.0  
> **Дата:** 2025-01-16  
> **Обновлено:** Добавлена информация об ACF Extended Pro

Пошаговая инструкция по установке и настройке WordPress темы для проекта OOR Webstudio.

---

## 🐳 Установка через Docker (рекомендуется для разработки)

### Требования

- Docker и Docker Compose установлены
- Порты 8080 и 8081 свободны

### Быстрый старт

```bash
# Запустить контейнеры
docker-compose up -d

# Проверить статус
docker-compose ps

# Просмотр логов
docker-compose logs -f wordpress

# Остановить
docker-compose down
```

### Доступ

- **WordPress:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081
- **MySQL:** `localhost:3306` (внутри Docker сети)

### Полезные команды

```bash
# Войти в контейнер WordPress
docker-compose exec wordpress bash

# Выполнить WP-CLI команду
docker-compose exec wordpress wp --allow-root <command>

# Перезапустить контейнеры
docker-compose restart

# Очистить все (включая данные)
docker-compose down -v
```

### Работа с файлами

Файлы темы находятся в `wp-content/themes/oor-theme/`:
- Изменения применяются сразу (hot reload)
- Файлы доступны и в контейнере, и на хосте

---

## 📦 Установка плагинов

### ACF Pro и ACF Extended Pro

Плагины уже добавлены в проект:
- `advanced-custom-fields-pro-6_4_2.zip` - ACF Pro (оплаченный, без обновлений)
- `acf-extended-pro_v0.9.1.zip` - ACF Extended Pro (оплаченный)

#### Установка через Docker

```bash
# Скопировать плагины в контейнер
docker-compose cp advanced-custom-fields-pro-6_4_2.zip wordpress:/tmp/acf-pro.zip
docker-compose cp acf-extended-pro_v0.9.1.zip wordpress:/tmp/acfe-pro.zip

# Распаковать и установить
docker-compose exec wordpress bash -c "cd /tmp && unzip -q -o acf-pro.zip -d /var/www/html/wp-content/plugins/ && unzip -q -o acfe-pro.zip -d /var/www/html/wp-content/plugins/"

# Активировать
docker-compose exec wordpress wp --allow-root plugin activate advanced-custom-fields-pro acf-extended-pro
```

#### Проверка установки

```bash
docker-compose exec wordpress wp --allow-root plugin list | grep -E "(acf|extended)"
```

Должны быть активны:
- `advanced-custom-fields-pro` (Active)
- `acf-extended-pro` (Active)

### Зачем нужен ACF Extended Pro?

**Полезные функции для проекта:**

1. **Улучшенный Repeater:**
   - Стилизованные кнопки "Добавить строку"
   - Блокировка строк (lock rows) - для фиксированного порядка
   - Улучшенный UI - удобнее редактировать артистов, треки, события

2. **Post Object улучшения:**
   - Inline создание/редактирование артистов прямо из слайдера
   - Удобнее работать с артистами в слайдере на главной странице

3. **Performance Mode:**
   - Оптимизация загрузки ACF полей
   - Полезно для производительности сайта

4. **Auto Sync (JSON/PHP):**
   - Автоматическая синхронизация полей
   - Удобно для разработки и версионирования

**Не критично, но полезно:**
- Улучшенный UX редактирования контента
- Оптимизация производительности
- Удобство работы с Repeater полями

---

## 🎨 Активация темы

```bash
# Активировать тему
docker-compose exec wordpress wp --allow-root theme activate oor-theme

# Проверить активную тему
docker-compose exec wordpress wp --allow-root theme list
```

---

## 📄 Создание страниц

### Основные страницы

```bash
# Главная страница (уже создана автоматически)
docker-compose exec wordpress wp --allow-root option update show_on_front page
docker-compose exec wordpress wp --allow-root option update page_on_front 2

# Создать остальные страницы
docker-compose exec wordpress wp --allow-root post create --post_type=page --post_title="Манифест" --post_name=manifest --post_status=publish
docker-compose exec wordpress wp --allow-root post create --post_type=page --post_title="Артисты" --post_name=artists --post_status=publish
docker-compose exec wordpress wp --allow-root post create --post_type=page --post_title="Студия" --post_name=studio --post_status=publish
docker-compose exec wordpress wp --allow-root post create --post_type=page --post_title="Услуги" --post_name=services --post_status=publish
docker-compose exec wordpress wp --allow-root post create --post_type=page --post_title="DAWGS" --post_name=dawgs --post_status=publish
docker-compose exec wordpress wp --allow-root post create --post_type=page --post_title="Talk-шоу" --post_name=talk-show --post_status=publish
docker-compose exec wordpress wp --allow-root post create --post_type=page --post_title="Контакты" --post_name=contacts --post_status=publish
```

### Назначение шаблонов

```bash
# Назначить шаблоны страницам
docker-compose exec wordpress wp --allow-root post meta update <ID> _wp_page_template page-manifest.php
docker-compose exec wordpress wp --allow-root post meta update <ID> _wp_page_template page-studio.php
# и т.д.
```

---

## 🔧 Настройка Custom Post Types

Custom Post Types уже зарегистрированы в `inc/cpt.php`:
- **Артисты** (`artist`) - появится в меню админ-панели
- **События** (`event`) - появится в меню админ-панели

Проверка:
```bash
docker-compose exec wordpress wp --allow-root post-type list
```

---

## 📋 Настройка ACF Field Groups

После активации ACF Pro нужно создать Field Groups вручную через админ-панель:

1. Перейдите в **Custom Fields → Field Groups**
2. Создайте Field Groups согласно [ACF-FIELDS-GUIDE.md](ACF-FIELDS-GUIDE.md)

### Основные Field Groups:

- **Главная страница** - Location: Page Template = Главная
- **Страница артиста** - Location: Post Type = artist
- **DAWGS** - Location: Page Template = DAWGS
- **Talk-show** - Location: Page Template = Talk-show
- **Событие** - Location: Post Type = event

### Использование ACF Extended Pro функций

При создании полей можно использовать улучшения ACF Extended Pro:

1. **Repeater поля:**
   - Включите "Stylised Button" для лучшего UI
   - Используйте "Lock Rows" для фиксированного порядка (если нужно)

2. **Post Object поля:**
   - Включите "Inline Edit" для создания/редактирования артистов прямо из слайдера
   - Полезно для поля "Artist" в слайдере на главной

3. **Performance Mode:**
   - Включите в настройках ACF Extended: **Settings → Performance Mode**
   - Улучшит производительность загрузки полей

---

## 🔗 Настройка permalinks

```bash
# Установить структуру постоянных ссылок
docker-compose exec wordpress wp --allow-root rewrite structure '/%postname%/' --hard

# Обновить правила rewrite
docker-compose exec wordpress wp --allow-root rewrite flush --hard
```

---

## 🎯 Настройка меню

1. Перейдите в **Внешний вид → Меню**
2. Создайте новое меню
3. Добавьте все страницы
4. Назначьте меню в "Расположение меню"

Или через WP-CLI:
```bash
# Создать меню
docker-compose exec wordpress wp --allow-root menu create "Main Menu"

# Добавить страницы в меню
docker-compose exec wordpress wp --allow-root menu item add-post "Main Menu" <page_id>
```

---

## ✅ Проверка установки

### Чек-лист

- [ ] Docker контейнеры запущены
- [ ] WordPress доступен на http://localhost:8080
- [ ] Тема `oor-theme` активирована
- [ ] ACF Pro установлен и активирован
- [ ] ACF Extended Pro установлен и активирован
- [ ] Custom Post Types зарегистрированы (Артисты, События)
- [ ] Все страницы созданы
- [ ] Шаблоны назначены страницам
- [ ] Permalinks настроены
- [ ] Проверка добавления/редактирования артистов и событий

---

## 🐛 Решение проблем

### Проблема: Страницы возвращают 404

**Решение:**
```bash
# Обновить permalinks
docker-compose exec wordpress wp --allow-root rewrite flush --hard

# Проверить настройки siteurl и home
docker-compose exec wordpress wp --allow-root option get siteurl
docker-compose exec wordpress wp --allow-root option get home
```

### Проблема: Плагины не активируются

**Решение:**
```bash
# Проверить права доступа
docker-compose exec wordpress ls -la /var/www/html/wp-content/plugins/

# Переустановить плагины
docker-compose exec wordpress wp --allow-root plugin install --force /tmp/acf-pro.zip
```

### Проблема: ACF поля не отображаются

**Решение:**
1. Проверьте, что ACF Pro активирован
2. Проверьте Location Rules в Field Groups
3. Убедитесь, что редактируете правильную страницу/пост

---

## 📚 Дополнительные ресурсы

- [ACF-FIELDS-GUIDE.md](ACF-FIELDS-GUIDE.md) - Руководство по редактированию контента
- [WORDPRESS-TEMPLATES-EXAMPLES.md](WORDPRESS-TEMPLATES-EXAMPLES.md) - Примеры PHP шаблонов
- [ACF Extended Documentation](https://www.acf-extended.com/features/getting-started/installation)

---

## 🔄 Обновление плагинов

### ACF Pro (ручное обновление)

Так как плагин без доступа к обновлениям:
1. Скачайте новую версию
2. Замените файлы в `/wp-content/plugins/advanced-custom-fields-pro/`
3. Или переустановите через WP-CLI

### ACF Extended Pro (ручное обновление)

1. Скачайте новую версию
2. Замените файлы в `/wp-content/plugins/acf-extended-pro/`
3. Или переустановите через WP-CLI

---

## ⚙️ Установка без Docker

Если Docker не используется:

1. Установите WordPress локально (XAMPP, MAMP, Local by Flywheel)
2. Скопируйте тему в `wp-content/themes/oor-theme/`
3. Установите плагины через админ-панель или вручную
4. Следуйте инструкциям выше (без `docker-compose exec wordpress`)

---

**Готово!** Теперь можно начинать работу с темой и создавать контент.
