# План оптимизации проекта перед миграцией на WordPress

> **Обновлено:** 2025-01-15  
> **Контекст:** Имиджевый сайт, еженедельное обновление контента, только ACF для редактирования, WooCommerce изолирован в `/merch/`  
> **⚠️ Срок миграции:** 2 недели (10 рабочих дней)

## 🎯 Приоритеты оптимизации

### КРИТИЧНО (сделать обязательно)
### ВАЖНО (рекомендуется)
### ЖЕЛАТЕЛЬНО (можно отложить)

---

## 1. СТРУКТУРА WORDPRESS ТЕМЫ И ACF

### КРИТИЧНО: Custom Post Types

**Рекомендация:** Использовать Custom Post Types для динамического контента

**Структура:**

1. **Custom Post Type: `artist` (Артисты)**
   - Позволяет легко добавлять/удалять артистов
   - Каждый артист = отдельный пост
   - URL структура: `/artists/{slug}/`
   - **Важно:** Есть страница со списком артистов (`artists.html` → `page-artists.php` или `archive-artist.php`)
   - **Критично:** После удаления артиста со страницы списка он удаляется безвозвратно (нет корзины)

2. **Custom Post Type: `event` (События)**
   - Для управления событиями
   - URL структура: `/events/{slug}/` или архив `/events/`

3. **Обычные страницы (Pages):**
   - Главная (Home)
   - Манифест (статическая)
   - Студия (статическая)
   - Услуги (статическая)
   - DAWGS (с ACF полями)
   - Talk-show (с ACF полями)
   - Контакты (статическая)

**Реализация в functions.php:**
```php
// Custom Post Type: Артисты
function oor_register_artist_post_type() {
    register_post_type('artist', [
        'labels' => [
            'name' => 'Артисты',
            'singular_name' => 'Артист',
            'add_new' => 'Добавить артиста',
            'add_new_item' => 'Добавить нового артиста',
            'edit_item' => 'Редактировать артиста',
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'artists'],
        'supports' => ['title', 'thumbnail'],
        'menu_icon' => 'dashicons-microphone',
    ]);
}
add_action('init', 'oor_register_artist_post_type');

// Custom Post Type: События
function oor_register_event_post_type() {
    register_post_type('event', [
        'labels' => [
            'name' => 'События',
            'singular_name' => 'Событие',
            'add_new' => 'Добавить событие',
            'add_new_item' => 'Добавить новое событие',
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'events'],
        'supports' => ['title', 'thumbnail'],
        'menu_icon' => 'dashicons-calendar-alt',
    ]);
}
add_action('init', 'oor_register_event_post_type');
```

### КРИТИЧНО: Структура ACF полей

**Главная страница (Page Template: `page-home.php`):**

```php
// ACF Field Group: "Главная страница"
// Location: Page Template is equal to Главная

// Hero Video
- hero_background_video (File) - видео в фоне
- hero_modal_video (File) - видео в модалке

// Артисты в слайдере (Repeater)
- artists_slider (Repeater)
  - artist (Post Object) - выбор артиста из CPT 'artist'
  - или можно использовать прямую связь через ACF Relationship

// Talk-show изображения
- talk_show_images (Gallery) - изображения TALK-ШОУ

// События (Repeater)
- events_section (Repeater)
  - event_poster (Image)
  - sold_out (True/False)
  - buy_ticket_text (Text)
  - ticket_url (URL) - ссылка на внешний магазин

// Мерч изображения
- merch_images (Gallery) - изображения в секции мерч
```

**Страница артиста (Single Template: `single-artist.php`):**

```php
// ACF Field Group: "Страница артиста"
// Location: Post Type is equal to artist

// Основная информация
- artist_image (Image) - главное изображение артиста
- short_description (Textarea) - краткое описание
- full_description (Textarea) - полное описание

// Социальные сети
- social_links (Repeater)
  - platform (Select) - Instagram, YouTube, VK, и т.д.
  - url (URL)

// Треки (Repeater)
- tracks (Repeater)
  - track_cover (Image) - обложка трека
  - track_name (Text) - название трека
  - track_mp3 (File) - MP3 файл
```

**Страница DAWGS (Page Template: `page-dawgs.php`):**

```php
// ACF Field Group: "DAWGS"
// Location: Page Template is equal to DAWGS

// Игроки (Repeater)
- players (Repeater)
  - player_name (Text)
  - player_description (Textarea)
  - player_image (Image)

// Партнеры (Repeater)
- partners (Repeater)
  - partner_name (Text)
  - partner_image (Image)
  - partner_description (Textarea)

// Главное изображение
- main_image (Image)
```

**Страница Talk-show (Page Template: `page-talk-show.php`):**

```php
// ACF Field Group: "Talk-show"
// Location: Page Template is equal to Talk-show

// Видео
- video_1 (File/URL) - первое видео
- video_2 (File/URL) - второе видео

// Подкаст
- podcast_image (Image)
- podcast_name (Text)
- podcast_url (URL)
```

**Страница событий (Archive Template: `archive-event.php`):**

```php
// ACF Field Group: "Событие"
// Location: Post Type is equal to event

- event_poster (Image)
- sold_out (True/False)
- buy_ticket_text (Text)
- ticket_url (URL) - ссылка на внешний магазин
- event_date (Date Picker)
- event_location (Text)
```

### КРИТИЧНО: Структура темы WordPress

**Создать структуру:**
```
/wp-content/themes/oor-theme/
├── style.css              # Главный файл темы с заголовком
├── functions.php          # Функции темы
├── index.php              # Главный шаблон
├── header.php             # Шапка (статическая)
├── footer.php             # Подвал (статическая)
├── front-page.php         # Главная страница
├── page.php               # Шаблон страницы по умолчанию
├── page-manifest.php      # Манифест (статическая)
├── page-studio.php        # Студия (статическая)
├── page-services.php      # Услуги (статическая)
├── page-dawgs.php         # DAWGS (с ACF)
├── page-talk-show.php     # Talk-show (с ACF)
├── page-contacts.php      # Контакты (статическая)
├── single-artist.php      # Страница артиста
├── archive-event.php      # Архив событий
├── single-event.php       # Страница события
├── template-parts/        # Части шаблонов
│   ├── hero.php
│   ├── artists-slider.php
│   ├── events-section.php
│   ├── talk-show-section.php
│   └── merch-section.php
├── assets/                # Копия /src
│   ├── css/
│   └── js/
└── inc/                   # Вспомогательные файлы
    ├── enqueue.php        # Подключение стилей и скриптов
    ├── body-classes.php   # Управление body-классами
    ├── paths.php          # Управление путями
    └── cpt.php            # Custom Post Types
```

---

## 2. ПРОИЗВОДИТЕЛЬНОСТЬ JAVASCRIPT

### КРИТИЧНО: Оптимизация селекторов DOM

**Проблема:** Множественные вызовы `querySelectorAll` без кэширования (55+ использований)

**Решение:**
- Создать утилиту для кэширования селекторов
- Использовать `WeakMap` для хранения кэша элементов
- Объединить повторяющиеся селекторы

**Файлы для изменения:**
- `src/js/main.js` (19 использований)
- `src/js/artist-page.js` (4 использования)
- `src/js/menu-sync.js` (6 использований)
- Остальные модули

**Пример оптимизации:**
```javascript
// Создать src/js/utils/dom-cache.js
const domCache = new WeakMap();

function cachedQuerySelector(selector, context = document) {
  if (!domCache.has(context)) {
    domCache.set(context, new Map());
  }
  const cache = domCache.get(context);
  if (!cache.has(selector)) {
    cache.set(selector, context.querySelector(selector));
  }
  return cache.get(selector);
}
```

### ВАЖНО: Удаление console.log из продакшена

**Проблема:** 20+ использований `console.log/warn` в коде

**Решение:**
- Создать обертку для логирования с проверкой окружения
- Для WordPress использовать `WP_DEBUG`

**Файлы:**
- `src/js/artist-page.js` (8 использований)
- `src/js/scale-container.js` (7 использований)
- `src/js/scrollbar.js` (1 использование)

**Пример:**
```javascript
// src/js/utils/logger.js
const isDev = typeof WP_DEBUG !== 'undefined' && WP_DEBUG;
export const logger = {
  log: (...args) => isDev && console.log(...args),
  warn: (...args) => isDev && console.warn(...args),
  error: (...args) => console.error(...args) // ошибки всегда логируем
};
```

### ВАЖНО: Оптимизация загрузки скриптов

**Текущее состояние:**
- ✅ `slider.js` - использует `defer`
- ✅ `merch-images.js` - использует `defer`
- ❌ `cursor.js` - без defer/async (блокирует рендеринг)
- ❌ `scrollbar.js` - без defer/async
- ❌ `main.js` - без defer/async

**Решение для WordPress:**
```php
// inc/enqueue.php
function oor_enqueue_scripts() {
    $theme_uri = get_template_directory_uri();
    $version = wp_get_theme()->get('Version');
    
    // Критические скрипты (синхронно)
    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', [], '3.12.5', false);
    wp_enqueue_script('oor-error-handler', $theme_uri . '/assets/js/modules/error-handler.js', [], $version, false);
    wp_enqueue_script('oor-preloader', $theme_uri . '/assets/js/modules/preloader.js', ['oor-error-handler'], $version, false);
    
    // Скрипты с defer
    wp_enqueue_script('oor-main', $theme_uri . '/assets/js/main.js', ['gsap'], $version, true);
    wp_enqueue_script('oor-cursor', $theme_uri . '/assets/js/cursor.js', [], $version, true);
    wp_enqueue_script('oor-scrollbar', $theme_uri . '/assets/js/scrollbar.js', [], $version, true);
    wp_enqueue_script('oor-slider', $theme_uri . '/assets/js/slider.js', [], $version, true);
    
    // Добавить defer атрибут
    add_filter('script_loader_tag', function($tag, $handle) {
        $defer_scripts = ['oor-main', 'oor-cursor', 'oor-scrollbar', 'oor-slider'];
        if (in_array($handle, $defer_scripts)) {
            return str_replace(' src', ' defer src', $tag);
        }
        return $tag;
    }, 10, 2);
}
```

### ВАЖНО: Lenis на всех страницах

**Решение:** Загружать Lenis динамически через `wp_enqueue_script` с правильными зависимостями

```php
// Lenis загружается динамически в preloader.js, но можно оптимизировать
wp_enqueue_script('lenis', 'https://cdn.jsdelivr.net/gh/studio-freight/lenis@1/bundled/lenis.min.js', [], '1.0.0', true);
```

---

## 3. ОПТИМИЗАЦИЯ CSS

### КРИТИЧНО: Уменьшение использования !important

**Проблема:** 828 использований `!important` в CSS

**Распределение:**
- `components.css`: 596 использований
- `utilities.css`: 127 использований
- `cursor.css`: 83 использования
- `slider.css`: 22 использования

**Решение:**
1. Пересмотреть специфичность селекторов
2. Использовать более специфичные селекторы вместо `!important`
3. Реорганизовать порядок загрузки CSS
4. Для WordPress: использовать правильный порядок `wp_enqueue_style`

**Приоритет:** Начать с `utilities.css` и `cursor.css` (меньше использований)

### ВАЖНО: Объединение CSS для продакшена

**Текущее состояние:** 12 отдельных CSS файлов

**Решение для WordPress:**
- Создать скрипт объединения CSS для продакшена
- Использовать `wp_enqueue_style` с объединенным файлом в продакшене
- В разработке оставить раздельные файлы

**Пример структуры:**
```
/assets/css/
  /dev/          # Раздельные файлы для разработки
  /dist/         # Объединенный файл для продакшена
    oor.min.css
```

**Скрипт объединения (Node.js):**
```javascript
// scripts/build-css.js
const fs = require('fs');
const path = require('path');

const cssFiles = [
  'reset.css',
  'tokens.css',
  'base.css',
  'grid.css',
  'layout.css',
  'fonts.css',
  'utilities.css',
  'slider.css',
  'scrollbar.css',
  'animations.css',
  'components.css',
  'cursor.css'
];

let combined = '';
cssFiles.forEach(file => {
  const content = fs.readFileSync(path.join(__dirname, '../src/css', file), 'utf8');
  combined += `/* ${file} */\n${content}\n\n`;
});

fs.writeFileSync(path.join(__dirname, '../assets/css/dist/oor.min.css'), combined);
```

---

## 4. ОПТИМИЗАЦИЯ ИЗОБРАЖЕНИЙ

### КРИТИЧНО: Lazy loading для изображений

**Проблема:** Нет lazy loading для изображений ниже fold

**Решение:**
```html
<!-- Нативные атрибуты -->
<img src="..." loading="lazy" decoding="async">

<!-- Для picture элементов -->
<picture>
  <source srcset="..." loading="lazy">
  <img src="..." loading="lazy" decoding="async">
</picture>
```

**Где применить:**
- Все изображения в галереях
- Изображения в слайдерах (кроме первого слайда)
- Изображения артистов
- Изображения событий
- Изображения DAWGS (игроки, партнеры)

**Для ACF полей в WordPress:**
```php
// В шаблонах использовать wp_get_attachment_image с lazy loading
echo wp_get_attachment_image($image_id, 'full', false, [
    'loading' => 'lazy',
    'decoding' => 'async'
]);
```

### ВАЖНО: Fetchpriority для критических изображений

**Решение:**
```html
<!-- Hero изображения -->
<img src="..." fetchpriority="high" loading="eager">

<!-- LCP изображения -->
<img src="..." fetchpriority="high" loading="eager">
```

### ВАЖНО: Оптимизация форматов изображений

**Текущее состояние:** ✅ Уже используется AVIF/WebP через `<picture>`

**Для WordPress:**
- Изображения оптимизируются вне WordPress
- Media Library хранит оригиналы
- Использовать `wp_get_attachment_image_srcset` для автоматического srcset

---

## 5. КЭШИРОВАНИЕ И ПРОИЗВОДИТЕЛЬНОСТЬ

### КРИТИЧНО: Настройка кэширования для VPS

**Рекомендации:**
1. **Object Cache (Memcached/Redis)**
   - Установить Memcached или Redis на VPS
   - Использовать плагин Object Cache (WP Redis, Memcached)
   - Кэшировать ACF поля и запросы

2. **Page Cache**
   - WP Rocket (платный, но лучший)
   - W3 Total Cache (бесплатный)
   - Или встроенное кэширование хостинга

3. **Database Query Cache**
   - Оптимизировать запросы ACF
   - Использовать transients для часто запрашиваемых данных

**Настройка для еженедельного обновления:**
```php
// functions.php
// Кэш на 7 дней для статического контента
define('WP_CACHE', true);
define('WP_CACHE_KEY_SALT', 'oor_');

// Transients для ACF данных
function oor_get_cached_acf_field($field_name, $post_id = null) {
    $cache_key = 'acf_' . $field_name . '_' . ($post_id ?: get_the_ID());
    $cached = get_transient($cache_key);
    
    if (false === $cached) {
        $cached = get_field($field_name, $post_id);
        set_transient($cache_key, $cached, WEEK_IN_SECONDS);
    }
    
    return $cached;
}
```

### ВАЖНО: Оптимизация запросов ACF

**Проблема:** ACF делает много запросов к БД

**Решение:**
- Использовать `get_fields()` вместо множественных `get_field()`
- Кэшировать результаты через transients
- Использовать Object Cache для ACF

```php
// Оптимизированная загрузка всех полей сразу
function oor_get_all_acf_fields($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $cache_key = 'acf_all_' . $post_id;
    
    $fields = wp_cache_get($cache_key);
    if (false === $fields) {
        $fields = get_fields($post_id);
        wp_cache_set($cache_key, $fields, '', 3600); // 1 час
    }
    
    return $fields;
}
```

---

## 6. WORDPRESS ФУНКЦИИ

### КРИТИЧНО: Функции для WordPress

**functions.php - основные функции:**

```php
<?php
// 1. Подключение стилей и скриптов
require_once get_template_directory() . '/inc/enqueue.php';

// 2. Управление body-классами
require_once get_template_directory() . '/inc/body-classes.php';

// 3. Управление путями
require_once get_template_directory() . '/inc/paths.php';

// 4. Custom Post Types
require_once get_template_directory() . '/inc/cpt.php';

// 5. Поддержка тем WordPress
add_theme_support('post-thumbnails');
add_theme_support('title-tag');
add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);

// 6. Отключение Gutenberg
add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);
```

**inc/enqueue.php:**
```php
function oor_enqueue_scripts() {
    $theme_uri = get_template_directory_uri();
    $version = wp_get_theme()->get('Version');
    
    // CSS (в правильном порядке)
    $css_files = [
        'reset' => 'reset.css',
        'tokens' => 'tokens.css',
        'base' => 'base.css',
        'grid' => 'grid.css',
        'layout' => 'layout.css',
        'fonts' => 'fonts.css',
        'utilities' => 'utilities.css',
        'slider' => 'slider.css',
        'scrollbar' => 'scrollbar.css',
        'animations' => 'animations.css',
        'components' => 'components.css',
        'cursor' => 'cursor.css',
    ];
    
    $prev_handle = null;
    foreach ($css_files as $handle => $file) {
        wp_enqueue_style(
            'oor-' . $handle,
            $theme_uri . '/assets/css/' . $file,
            $prev_handle ? ['oor-' . $prev_handle] : [],
            $version
        );
        $prev_handle = $handle;
    }
    
    // JS
    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', [], '3.12.5', false);
    
    // Критические скрипты
    wp_enqueue_script('oor-error-handler', $theme_uri . '/assets/js/modules/error-handler.js', [], $version, false);
    wp_enqueue_script('oor-preloader', $theme_uri . '/assets/js/modules/preloader.js', ['oor-error-handler'], $version, false);
    
    // Скрипты с defer
    wp_enqueue_script('oor-main', $theme_uri . '/assets/js/main.js', ['gsap'], $version, true);
    wp_enqueue_script('oor-cursor', $theme_uri . '/assets/js/cursor.js', [], $version, true);
    wp_enqueue_script('oor-scrollbar', $theme_uri . '/assets/js/scrollbar.js', [], $version, true);
    wp_enqueue_script('oor-slider', $theme_uri . '/assets/js/slider.js', [], $version, true);
    
    // Локализация для путей
    wp_localize_script('oor-main', 'oorPaths', [
        'base' => $theme_uri,
        'assets' => $theme_uri . '/public/assets',
        'fonts' => $theme_uri . '/public/fonts'
    ]);
    
    // Добавить defer атрибут
    add_filter('script_loader_tag', function($tag, $handle) {
        $defer_scripts = ['oor-main', 'oor-cursor', 'oor-scrollbar', 'oor-slider'];
        if (in_array($handle, $defer_scripts)) {
            return str_replace(' src', ' defer src', $tag);
        }
        return $tag;
    }, 10, 2);
}
add_action('wp_enqueue_scripts', 'oor_enqueue_scripts');
```

**inc/body-classes.php:**
```php
function oor_body_classes($classes) {
    // Статические страницы
    if (is_page('studio')) {
        $classes[] = 'oor-studio-page';
    } elseif (is_page('artists') || is_post_type_archive('artist')) {
        $classes[] = 'oor-artists-page';
    } elseif (is_page('manifest')) {
        $classes[] = 'oor-manifest-page';
    } elseif (is_page('services')) {
        $classes[] = 'oor-services-page';
    } elseif (is_page('dawgs')) {
        $classes[] = 'oor-dawgs-page';
    } elseif (is_page('talk-show')) {
        $classes[] = 'oor-talk-show-page';
    } elseif (is_page('merch')) {
        $classes[] = 'oor-merch-page';
    }
    
    // Custom Post Types
    if (is_singular('artist')) {
        $classes[] = 'oor-artist-page';
    } elseif (is_singular('event')) {
        $classes[] = 'oor-event-page';
    } elseif (is_post_type_archive('event')) {
        $classes[] = 'oor-events-page';
    }
    
    return $classes;
}
add_filter('body_class', 'oor_body_classes');
```

### ВАЖНО: Миграция путей

**Создать скрипт для автоматической замены:**
```javascript
// scripts/migrate-paths.js
// Заменяет абсолютные пути на WordPress-совместимые
```

**Или использовать поиск и замену:**
- `/public/` → `<?php echo get_template_directory_uri(); ?>/public/`
- `/src/` → `<?php echo get_template_directory_uri(); ?>/src/`

**Для шаблонов WordPress:**
```php
// Использовать везде вместо абсолютных путей
$theme_uri = get_template_directory_uri();
<img src="<?php echo $theme_uri; ?>/public/assets/logo.svg">
```

---

## 7. АНАЛИТИКА

### ВАЖНО: Оптимизация загрузки аналитики

**Google Analytics и Яндекс.Метрика:**

```php
// functions.php
function oor_add_analytics() {
    if (is_admin()) return;
    ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>
    
    <!-- Яндекс.Метрика -->
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
        ym(METRICA_ID, "init", {clickmap:true, trackLinks:true, accurateTrackBounce:true});
    </script>
    <?php
}
add_action('wp_footer', 'oor_add_analytics');
```

**Оптимизация:** Загружать асинхронно, не блокировать рендеринг

---

## 8. БЕЗОПАСНОСТЬ

### ВАЖНО: Санитизация данных ACF

**Для WordPress:**
- Использовать `esc_html()`, `esc_attr()`, `esc_url()` для вывода данных ACF
- Использовать `wp_kses()` для HTML контента (если будет)

**Примеры в шаблонах:**
```php
// Текстовые поля
echo esc_html(get_field('artist_name'));

// URL
$ticket_url = esc_url(get_field('ticket_url'));
echo '<a href="' . $ticket_url . '">Купить билет</a>';

// Изображения
$image = get_field('artist_image');
if ($image) {
    echo wp_get_attachment_image($image['ID'], 'full', false, [
        'loading' => 'lazy',
        'decoding' => 'async'
    ]);
}

// Repeater поля
if (have_rows('tracks')) {
    while (have_rows('tracks')) {
        the_row();
        $track_name = esc_html(get_sub_field('track_name'));
        $track_mp3 = esc_url(get_sub_field('track_mp3'));
        // ...
    }
}
```

### ВАЖНО: HTTPS обязательно

**Настройка:**
```php
// wp-config.php
define('FORCE_SSL_ADMIN', true);

// functions.php
if (!is_admin() && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')) {
    wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301);
    exit();
}
```

---

## 9. ДОКУМЕНТАЦИЯ

### КРИТИЧНО: Документация для администраторов

**Создать:**
1. **`WORDPRESS-SETUP.md`** - инструкция по установке темы
2. **`ACF-FIELDS-GUIDE.md`** - подробное описание всех полей ACF и как их редактировать
3. **`CONTENT-EDITING-GUIDE.md`** - пошаговые инструкции по редактированию контента

**Структура ACF-FIELDS-GUIDE.md:**
```markdown
# Руководство по редактированию контента через ACF

## Главная страница
### Видео в фоне
1. Перейти в редактирование главной страницы
2. Найти поле "Hero Background Video"
3. Загрузить новое видео через Media Library
...

## Добавление артиста
1. Перейти в "Артисты" → "Добавить новый"
2. Заполнить название (будет использоваться в URL)
3. Загрузить изображение артиста
4. Заполнить краткое и полное описание
5. Добавить треки через Repeater поле
...
```

---

## 10. CI/CD И ДЕПЛОЙ

### ВАЖНО: Настройка Git + CI/CD

**Рекомендации:**
- Использовать Git для версионирования темы
- Настроить CI/CD для автоматического деплоя
- Использовать GitHub Actions или GitLab CI

**Пример .github/workflows/deploy.yml:**
```yaml
name: Deploy WordPress Theme

on:
  push:
    branches: [ master ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Deploy to server
        uses: SamKirkland/FTP-Deploy-Action@4.0.0
        with:
          server: ${{ secrets.FTP_SERVER }}
          username: ${{ secrets.FTP_USERNAME }}
          password: ${{ secrets.FTP_PASSWORD }}
          local-dir: ./
          server-dir: /wp-content/themes/oor-theme/
```

---

## 📋 Чек-лист перед миграцией

### Структура WordPress
- [ ] Создана структура темы
- [ ] Зарегистрированы Custom Post Types (artist, event)
- [ ] Настроены функции темы (enqueue, body classes, paths)
- [ ] Созданы шаблоны для всех страниц
- [ ] Настроены body-классы для всех страниц

### ACF
- [ ] Созданы все Field Groups
- [ ] Настроены Repeater поля (артисты в слайдере, треки, игроки DAWGS, события)
- [ ] Протестировано редактирование всех полей
- [ ] Создана документация по редактированию

### Производительность
- [ ] Оптимизированы DOM селекторы
- [ ] Удалены console.log из продакшена
- [ ] Все некритические скрипты используют defer
- [ ] CSS минифицирован и объединен для продакшена
- [ ] Настроено кэширование (Memcached/Redis)
- [ ] Настроен Page Cache

### Изображения
- [ ] Добавлен lazy loading для всех изображений ниже fold
- [ ] Критические изображения используют fetchpriority="high"
- [ ] Все изображения оптимизированы (AVIF/WebP)

### Безопасность
- [ ] Все данные ACF санитизированы в шаблонах
- [ ] Настроен HTTPS
- [ ] Настроены автоматические бэкапы

### Документация
- [ ] Создана инструкция по установке темы
- [ ] Создано руководство по редактированию ACF полей
- [ ] Созданы пошаговые инструкции по редактированию контента

---

## 🚀 Приоритетный порядок выполнения

### Неделя 1: Структура, шаблоны и ACF (Дни 1-5)
- [ ] Создание структуры WordPress темы (День 1)
- [ ] Миграция путей (День 1)
- [ ] Регистрация Custom Post Types (День 1)
- [ ] Создание базовых шаблонов (День 2)
- [ ] Создание всех шаблонов страниц (Дни 3-5)
- [ ] Настройка ACF Field Groups (Дни 3-5, параллельно)
- [ ] Тестирование редактирования контента (Дни 3-5)

### Неделя 2: Оптимизация, тестирование и запуск (Дни 6-10)
- [ ] Оптимизация DOM селекторов (День 6)
- [ ] Добавление lazy loading (День 6)
- [ ] Удаление console.log (День 6)
- [ ] Настройка кэширования (Memcached/Redis) (День 7)
- [ ] Оптимизация загрузки скриптов (День 7)
- [ ] Функциональное тестирование (День 8)
- [ ] Кроссбраузерное тестирование (День 9)
- [ ] Настройка HTTPS и аналитики (День 9)
- [ ] Финальное тестирование и запуск (День 10)

---

**Дата создания:** 2025-01-15  
**Обновлено:** 2025-01-15  
**Версия:** 2.0.0
