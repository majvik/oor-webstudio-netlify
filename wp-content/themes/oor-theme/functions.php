<?php
/**
 * OOR Webstudio Theme Functions
 */

// Защита от прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

// Заглушки ACF при неактивном плагине — избегаем fatal error в шаблонах
if (!function_exists('get_field')) {
    function get_field($selector, $post_id = false) {
        return null;
    }
}
if (!function_exists('have_rows')) {
    function have_rows($selector, $post_id = false) {
        return false;
    }
}
if (!function_exists('get_sub_field')) {
    function get_sub_field($selector) {
        return null;
    }
}

// Версия темы
define('OOR_THEME_VERSION', '1.2.2');

/**
 * На сервере с nip.io: подмена URL с IP на канонический домен.
 * Работает при define('OOR_FORCE_CANONICAL_HOST', '45.141.102.187.nip.io') в wp-config
 * ИЛИ при заходе по https://45.141.102.187.nip.io — картинки из ACF/медиа и темы грузятся с правильным хостом.
 */
$oor_fix_canonical_url_filter = function($url) {
    return oor_fix_canonical_url($url);
};
add_filter('home_url', $oor_fix_canonical_url_filter, 1, 4);
add_filter('site_url', $oor_fix_canonical_url_filter, 1, 4);
add_filter('script_loader_src', $oor_fix_canonical_url_filter, 10, 2);
add_filter('style_loader_src', $oor_fix_canonical_url_filter, 10, 2);
add_filter('content_url', $oor_fix_canonical_url_filter, 10, 2);
add_filter('plugins_url', $oor_fix_canonical_url_filter, 10, 2);
add_filter('rest_url', $oor_fix_canonical_url_filter, 10, 2);
add_filter('theme_root_uri', $oor_fix_canonical_url_filter, 10, 1);
add_filter('admin_url', $oor_fix_canonical_url_filter, 10, 3);
add_filter('includes_url', $oor_fix_canonical_url_filter, 10, 2);
add_filter('upload_dir', function($uploads) {
    foreach (array('url', 'baseurl') as $key) {
        if (!empty($uploads[$key])) {
            $uploads[$key] = oor_fix_canonical_url($uploads[$key]);
        }
    }
    return $uploads;
}, 10, 1);
add_filter('wp_get_attachment_url', $oor_fix_canonical_url_filter, 10, 1);

/**
 * ACF: в поле «платформа» соцссылок артиста показывать и хранить "tiktok" вместо "other".
 */
add_filter('acf/load_field/name=platform', function($field) {
    if (!function_exists('acf_get_setting') || !is_array($field)) {
        return $field;
    }
    if (!empty($field['choices']) && isset($field['choices']['other'])) {
        $field['choices']['tiktok'] = $field['choices']['other'];
        unset($field['choices']['other']);
    }
    return $field;
});
add_filter('acf/load_value/name=platform', function($value, $post_id, $field) {
    if (get_post_type($post_id) === 'artist' && $value === 'other') {
        return 'tiktok';
    }
    return $value;
}, 10, 3);

/**
 * ACF медиа-модалка: кнопка выбора не должна показывать «Выпадающий список»
 * (исправление ошибочного перевода — принудительно «Выбрать»).
 */
add_filter('gettext_with_context', function($translated, $text, $context, $domain) {
    if ($domain === 'acf' && $context === 'verb' && $text === 'Select') {
        return 'Выбрать';
    }
    return $translated;
}, 10, 4);

add_filter('wp_calculate_image_srcset', function($sources) {
    if (!is_array($sources)) return $sources;
    foreach ($sources as $w => $data) {
        if (!empty($data['url'])) {
            $sources[$w]['url'] = oor_fix_canonical_url($data['url']);
        }
    }
    return $sources;
}, 10, 1);

/**
 * Приводит URL к каноническому хосту (nip.io). Вызывать для любых URL из ACF/мета/загрузок.
 * Учитывает OOR_FORCE_CANONICAL_HOST и текущий HTTP_HOST (если зашли по nip.io — подставляем его).
 */
function oor_fix_canonical_url($url) {
    if (!is_string($url) || $url === '') {
        return $url;
    }
    $url = str_replace('.nip.io.nip.io', '.nip.io', $url);
    $host = parse_url($url, PHP_URL_HOST);
    if ($host !== '45.141.102.187') {
        return $url;
    }
    $canonical = (defined('OOR_FORCE_CANONICAL_HOST') && OOR_FORCE_CANONICAL_HOST) ? OOR_FORCE_CANONICAL_HOST : null;
    if (!$canonical && !empty($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === '45.141.102.187.nip.io') {
        $canonical = '45.141.102.187.nip.io';
    }
    if ($canonical) {
        $url = str_replace('45.141.102.187', $canonical, $url);
        $protocol = is_ssl() ? 'https://' : 'http://';
        $url = preg_replace('#^https?://#', $protocol, $url);
    }
    return $url;
}

/**
 * Собирает HTML <picture> для fallback-изображения артиста только из существующих файлов (без 404).
 * $artist_slug — slug артиста, $base_name — имя без расширения (например main).
 */
function oor_artist_fallback_picture($artist_slug, $base_name = 'main', $img_class = 'oor-artist-image-main no-parallax', $alt = '') {
    $theme_dir = get_template_directory();
    $base_path = $theme_dir . '/public/assets/artists/' . $artist_slug . '/' . $base_name;
    $base_url = oor_theme_base_uri() . '/public/assets/artists/' . $artist_slug . '/' . $base_name;
    $sources = [];
    if (file_exists($base_path . '.avif')) {
        $sources['avif'] = ['1x' => $base_url . '.avif'];
        if (file_exists($base_path . '@2x.avif')) {
            $sources['avif']['2x'] = $base_url . '@2x.avif';
        }
    }
    if (file_exists($base_path . '.webp')) {
        $sources['webp'] = ['1x' => $base_url . '.webp'];
        if (file_exists($base_path . '@2x.webp')) {
            $sources['webp']['2x'] = $base_url . '@2x.webp';
        }
    }
    $img_src = null;
    $img_srcset = [];
    if (file_exists($base_path . '.png')) {
        $img_src = $base_url . '.png';
        $img_srcset[] = esc_url($base_url . '.png') . ' 1x';
        if (file_exists($base_path . '@2x.png')) {
            $img_srcset[] = esc_url($base_url . '@2x.png') . ' 2x';
        }
    }
    if (!$img_src && file_exists($base_path . '.jpg')) {
        $img_src = $base_url . '.jpg';
        $img_srcset[] = esc_url($base_url . '.jpg') . ' 1x';
        if (file_exists($base_path . '@2x.jpg')) {
            $img_srcset[] = esc_url($base_url . '@2x.jpg') . ' 2x';
        }
    }
    if (!$img_src) {
        return '';
    }
    $html = '<picture>';
    if (!empty($sources['avif'])) {
        $parts = [];
        if (!empty($sources['avif']['1x'])) $parts[] = esc_url($sources['avif']['1x']) . ' 1x';
        if (!empty($sources['avif']['2x'])) $parts[] = esc_url($sources['avif']['2x']) . ' 2x';
        if ($parts) $html .= '<source srcset="' . implode(', ', $parts) . '" type="image/avif">';
    }
    if (!empty($sources['webp'])) {
        $parts = [];
        if (!empty($sources['webp']['1x'])) $parts[] = esc_url($sources['webp']['1x']) . ' 1x';
        if (!empty($sources['webp']['2x'])) $parts[] = esc_url($sources['webp']['2x']) . ' 2x';
        if ($parts) $html .= '<source srcset="' . implode(', ', $parts) . '" type="image/webp">';
    }
    $html .= '<img src="' . esc_url($img_src) . '"';
    if (!empty($img_srcset)) {
        $html .= ' srcset="' . implode(', ', $img_srcset) . '"';
    }
    $html .= ' alt="' . esc_attr($alt) . '" class="' . esc_attr($img_class) . '">';
    $html .= '</picture>';
    return $html;
}

/**
 * Базовый URL темы для ресурсов (картинки, скрипты). На сервере с nip.io возвращает канонический URL.
 * Использовать вместо get_template_directory_uri() там, где критично правильный хост (артисты, медиа).
 * Учитывает OOR_FORCE_CANONICAL_HOST и текущий HTTP_HOST (если зашли по 45.141.102.187.nip.io — подставляем его).
 */
function oor_theme_base_uri() {
    $uri = get_template_directory_uri();
    $uri = str_replace('.nip.io.nip.io', '.nip.io', $uri);
    $host = parse_url($uri, PHP_URL_HOST);
    $request_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $canonical = (defined('OOR_FORCE_CANONICAL_HOST') && OOR_FORCE_CANONICAL_HOST) ? OOR_FORCE_CANONICAL_HOST : null;
    if (!$canonical && $request_host === '45.141.102.187.nip.io') {
        $canonical = '45.141.102.187.nip.io';
    }
    if ($canonical && $host === '45.141.102.187') {
        $uri = str_replace('45.141.102.187', $canonical, $uri);
        $protocol = is_ssl() ? 'https://' : 'http://';
        $uri = preg_replace('#^https?://#', $protocol, $uri);
    }
    return $uri;
}

// Подавление deprecation warnings от ACF Pro (PHP 8.2+ совместимость)
// ACF Pro 6.4.2 использует динамические свойства, которые deprecated в PHP 8.2+
// Это не критично и не влияет на функциональность, но вызывает предупреждения
// которые выводятся до отправки HTTP заголовков → "headers already sent" error
if (PHP_VERSION_ID >= 80200) {
    // Отключаем вывод ошибок на экран для предотвращения "headers already sent"
    // Логирование остается активным (если WP_DEBUG_LOG включен)
    ini_set('display_errors', 0);
    
    // Подавляем только deprecation warnings, остальные ошибки логируем
    if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
        // В режиме отладки логируем все, кроме deprecation
        error_reporting(E_ALL & ~E_DEPRECATED);
    } else {
        // В продакшене скрываем все предупреждения
        error_reporting(0);
    }
}

// Настройка ACF для автоматической загрузки и синхронизации JSON из acf-json/
add_filter('acf/settings/save_json', function($path) {
    $path = get_stylesheet_directory() . '/acf-json';
    // Создаем папку если её нет
    if (!file_exists($path)) {
        wp_mkdir_p($path);
    }
    return $path;
});

add_filter('acf/settings/load_json', function($paths) {
    // Удаляем стандартный путь ACF
    unset($paths[0]);
    // Добавляем путь к папке темы
    $theme_path = get_stylesheet_directory() . '/acf-json';
    if (file_exists($theme_path)) {
        $paths[] = $theme_path;
    }
    return $paths;
});

// Автоматическая синхронизация ACF полей при активации темы
add_action('after_setup_theme', function() {
    // Проверяем, что ACF активен
    if (function_exists('acf_get_setting')) {
        // Принудительно загружаем JSON файлы при загрузке темы
        $json_path = get_stylesheet_directory() . '/acf-json';
        if (is_dir($json_path)) {
            // ACF автоматически загрузит JSON файлы при следующем обращении к Field Groups
            // Это происходит автоматически через фильтр load_json выше
        }
    }
});

// Улучшенная синхронизация: автоматически обновляем JSON при сохранении Field Group
add_action('acf/update_field_group', function($field_group) {
    // ACF автоматически сохранит в JSON благодаря фильтру save_json
    // Дополнительная логика не требуется
}, 10, 1);

// ACF Options: страница настроек темы (футер — email и соцсети)
add_action('acf/init', function() {
    if (!function_exists('acf_add_options_page')) {
        return;
    }
    acf_add_options_page([
        'page_title' => 'Настройки темы (футер)',
        'menu_title' => 'Футер',
        'menu_slug'  => 'acf-options-footer',
        'capability' => 'edit_posts',
        'redirect'   => false,
    ]);
});

// Группы полей «Футер — email и соцсети» и «Главная страница» (включая hero-соцсети) хранятся в БД.

// «Продолжить покупки» / «Return to shop» ведёт на мерч (каталог товаров)
add_filter('woocommerce_return_to_shop_redirect', function() {
    return function_exists('wc_get_page_id') && wc_get_page_id('shop') > 0
        ? get_permalink(wc_get_page_id('shop'))
        : home_url('/merch');
});
add_filter('woocommerce_continue_shopping_redirect', function() {
    return function_exists('wc_get_page_id') && wc_get_page_id('shop') > 0
        ? get_permalink(wc_get_page_id('shop'))
        : home_url('/merch');
});

// Включение поддержки AVIF и WebP изображений
add_filter('mime_types', function($mimes) {
    // Добавляем поддержку AVIF
    $mimes['avif'] = 'image/avif';
    // Добавляем поддержку WebP (обычно уже есть, но на всякий случай)
    $mimes['webp'] = 'image/webp';
    return $mimes;
});

// Включение поддержки AVIF и WebP в загрузке файлов
add_filter('upload_mimes', function($mimes) {
    $mimes['avif'] = 'image/avif';
    $mimes['webp'] = 'image/webp';
    return $mimes;
}, 10, 1);

// Включение AVIF и WebP в список поддерживаемых форматов для генерации миниатюр
add_filter('image_size_names_choose', function($sizes) {
    return $sizes;
});

// Подключение вспомогательных файлов
require_once get_template_directory() . '/inc/cpt.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/body-classes.php';
require_once get_template_directory() . '/inc/image-processing.php';

// Поддержка тем WordPress
add_theme_support('post-thumbnails');
add_theme_support('title-tag');
add_theme_support('html5', [
    'search-form',
    'comment-form',
    'comment-list',
    'gallery',
    'caption'
]);

// Поддержка WooCommerce
add_action('after_setup_theme', function () {
    add_theme_support('woocommerce');
});

/**
 * Цена без текстового мусора: только перечёркнутая старая и актуальная.
 * Убирает «Первоначальная цена составляла», «Текущая цена:» и т.п.
 */
add_filter('woocommerce_get_price_html', function ($price_html, $product) {
    if (!is_a($product, 'WC_Product')) {
        return $price_html;
    }
    $regular = $product->get_regular_price();
    $current = $product->get_price();
    $on_sale = $product->is_on_sale() && $regular !== '' && $current !== '' && (float) $current < (float) $regular;
    if ($on_sale) {
        $price_html = '<del aria-hidden="true">' . wc_price($regular) . '</del> <ins>' . wc_price($current) . '</ins>';
    } else {
        $price_html = wc_price($current);
    }
    return $price_html;
}, 999, 2);

/** На страницах WooCommerce убираем стандартные хлебные крошки и сайдбар (поиск, страницы и т.д.). */
add_action('init', function () {
    if (!function_exists('woocommerce_breadcrumb')) {
        return;
    }
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
    remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
}, 20);

/**
 * Страница товара: без табов (wc-tabs), порядок в summary — описание, доп. информация (атрибуты), похожие товары, артикул внизу.
 */
add_action('wp', function () {
    if (!function_exists('woocommerce_output_product_data_tabs')) {
        return;
    }
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

    add_action('woocommerce_single_product_summary', function () {
        echo '<div id="tab-description" class="woocommerce-Tabs-panel woocommerce-Tabs-panel--description panel entry-content wc-tab">';
        wc_get_template('single-product/tabs/description.php');
        echo '</div>';
    }, 35);

    add_action('woocommerce_single_product_summary', 'woocommerce_output_related_products', 45);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 50);
}, 20);

/** У блока описания товара убираем заголовок «Описание». */
add_filter('woocommerce_product_description_heading', function () {
    return '';
});

/** Галерея и изображение товара — в максимальном качестве (full). */
add_filter('woocommerce_gallery_image_size', function () {
    return 'full';
});
add_filter('woocommerce_gallery_full_size', function () {
    return 'full';
});
add_filter('woocommerce_product_thumbnails_large_size', function () {
    return 'full';
});

/**
 * Опции вариаций (размер и т.д.) — баблы как в product.html (XL, L, S, M), не селект.
 * Скрытый select остаётся в DOM для работы формы вариаций WooCommerce.
 */
add_filter('woocommerce_dropdown_variation_attribute_options_html', function ($html, $args) {
    $options = $args['options'];
    $product = $args['product'];
    $attribute = $args['attribute'];
    $selected = $args['selected'];
    $name = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title($attribute);
    $id = $args['id'] ? $args['id'] : sanitize_title($attribute);

    if (empty($options) && $product && $attribute) {
        $attributes = $product->get_variation_attributes();
        $options = isset($attributes[$attribute]) ? $attributes[$attribute] : array();
    }
    if (empty($options)) {
        return $html;
    }

    $options_for_buttons = array();
    if ($product && taxonomy_exists($attribute)) {
        $terms = wc_get_product_terms($product->get_id(), $attribute, array('fields' => 'all'));
        foreach ($terms as $term) {
            if (in_array($term->slug, $options, true)) {
                $options_for_buttons[$term->slug] = apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute, $product);
            }
        }
    } else {
        foreach ($options as $option) {
            $options_for_buttons[$option] = apply_filters('woocommerce_variation_option_name', $option, null, $attribute, $product);
        }
    }

    $label = wc_attribute_label($attribute);
    $select_class = 'oor-variation-select-hidden';
    if (strpos($html, ' class="') !== false) {
        $html_select = str_replace(' class="', ' class="' . esc_attr($select_class) . ' ', $html);
    } else {
        $html_select = str_replace('<select ', '<select class="' . esc_attr($select_class) . '" ', $html);
    }

    $out = '<div class="oor-product-size oor-variation-attribute-wrap" data-attribute_name="attribute_' . esc_attr(sanitize_title($attribute)) . '">';
    $out .= '<span class="oor-variation-select-wrapper" aria-hidden="true">' . $html_select . '</span>';
    $out .= '<p class="oor-product-size-label">' . esc_html($label) . ':</p>';
    $out .= '<div class="oor-product-size-options">';
    foreach ($options_for_buttons as $value => $display) {
        $is_selected = (string) $selected === (string) $value;
        $btn_class = 'oor-product-size-btn';
        if ($is_selected) {
            $btn_class .= ' oor-product-size-btn--active';
        }
        $out .= '<button type="button" class="' . esc_attr($btn_class) . '" data-value="' . esc_attr($value) . '" data-attribute="' . esc_attr(sanitize_title($attribute)) . '">' . esc_html($display) . '</button>';
    }
    $out .= '</div></div>';

    return $out;
}, 10, 2);

// Отключение Gutenberg
add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);

// Отключение стандартных стилей WordPress
add_action('wp_enqueue_scripts', function() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-block-style');
}, 100);

// Отключение Gravatar (не нужен, так как нет блога и комментариев)
// Правильное отключение get_avatar - возвращаем пустую строку вместо false
add_filter('get_avatar', function($avatar, $id_or_email, $size, $default, $alt, $args) {
    return '';
}, 1, 6);

// Правильное отключение pre_get_avatar_data - возвращаем массив с пустыми данными
// Фильтр получает только 2 аргумента: $args (массив) и $id_or_email
add_filter('pre_get_avatar_data', function($args, $id_or_email) {
    // Извлекаем значения из массива $args
    $size = isset($args['size']) ? $args['size'] : 96;
    $default = isset($args['default']) ? $args['default'] : '';
    $force_default = isset($args['force_default']) ? $args['force_default'] : false;
    $rating = isset($args['rating']) ? $args['rating'] : 'G';
    
    return [
        'url' => '',
        'found_avatar' => false,
        'size' => $size,
        'default' => $default,
        'force_default' => $force_default,
        'rating' => $rating
    ];
}, 1, 2);

// Отключение загрузки скриптов Gravatar
add_filter('avatar_defaults', '__return_empty_array');

// Блокировка запросов к Gravatar
add_filter('get_avatar_url', function($url, $id_or_email, $args) {
    return '';
}, 1, 3);

// Отключение комментариев (если они не используются)
add_filter('comments_open', '__return_false', 20);
add_filter('pings_open', '__return_false', 20);

// Удаление ссылок на Gravatar из wp_head
add_action('init', function() {
    remove_action('wp_head', 'wp_generator');
}, 1);

// Чекаут: всегда используем шаблон со шорткодом [woocommerce_checkout], а не блок — чтобы поля billing были в DOM
// Мерч: для страницы с slug merch принудительно archive-product.php (главный запрос не трогаем — иначе 404)
// Страница товара: принудительно single-product.php темы (чтобы не подгружался блоковый/другой шаблон)
add_filter('template_include', function($template) {
    if (function_exists('is_checkout') && is_checkout()) {
        if (!(function_exists('is_wc_endpoint_url') && is_wc_endpoint_url())) {
            $our = get_stylesheet_directory() . '/page-checkout.php';
            if (file_exists($our)) {
                return $our;
            }
        }
        return $template;
    }
    $theme_dir = get_stylesheet_directory();
    if (function_exists('is_product') && is_product()) {
        $single = $theme_dir . '/woocommerce/single-product.php';
        if (file_exists($single)) {
            return $single;
        }
    }
    $merch_template = $theme_dir . '/woocommerce/archive-product.php';
    if ((function_exists('is_shop') && is_shop() || is_page('merch')) && file_exists($merch_template)) {
        return $merch_template;
    }
    return $template;
}, 99);

// Чекаут: при пустой корзине всё равно показываем форму (поля в DOM), редирект отключён
add_filter('woocommerce_checkout_redirect_empty_cart', '__return_false', 1);

/**
 * Рендерит поле чекаута без вызова woocommerce_form_field (кроме country/state).
 * Поля text/email/tel/textarea выводятся напрямую — так их не может «съесть» ни фильтр, ни баг WC.
 *
 * @param string $key   Ключ поля (billing_first_name и т.д.).
 * @param array  $field Аргументы поля из get_checkout_fields().
 * @param mixed  $value Значение поля.
 */
function oor_render_checkout_field( $key, $field, $value ) {
	// Защита от нескалярных значений (массив/объект) — иначе esc_attr/esc_textarea могут сломать вывод.
	if ( ! is_scalar( $value ) && $value !== null ) {
		$value = '';
	}
	if ( $value === null ) {
		$value = '';
	}

	$type = isset( $field['type'] ) ? $field['type'] : 'text';

	// country/state — сложная логика (страны, регионы), оставляем WooCommerce.
	if ( $type === 'country' || $type === 'state' ) {
		woocommerce_form_field( $key, $field, $value );
		return;
	}

	// Остальные поля — выводим HTML сами, без woocommerce_form_field.
	$id          = isset( $field['id'] ) ? $field['id'] : $key;
	$label       = isset( $field['label'] ) ? $field['label'] : '';
	$required    = ! empty( $field['required'] );
	$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field_class = isset( $field['class'] ) ? $field['class'] : array();
	$container_cls = is_array( $field_class ) ? implode( ' ', $field_class ) : $field_class;
	$field_input_class = isset( $field['input_class'] ) ? $field['input_class'] : array();
	$input_cls   = is_array( $field_input_class ) ? implode( ' ', $field_input_class ) : $field_input_class;
	$field_label_class = isset( $field['label_class'] ) ? $field['label_class'] : array();
	$label_cls   = is_array( $field_label_class ) ? implode( ' ', $field_label_class ) : $field_label_class;
	$sort        = isset( $field['priority'] ) ? $field['priority'] : '';
	$autocomplete = isset( $field['autocomplete'] ) ? $field['autocomplete'] : '';

	// Тип инпута: email, tel, textarea или text.
	if ( ! in_array( $type, array( 'email', 'tel', 'textarea' ), true ) ) {
		$type = 'text';
	}

	$req_mark   = $required ? '&nbsp;<span class="required" aria-hidden="true">*</span>' : '';
	$req_attr   = $required ? ' aria-required="true" required' : '';
	$name_esc   = esc_attr( $key );
	$id_esc     = esc_attr( $id );
	$value_esc  = esc_attr( $value );
	$ph_esc     = esc_attr( $placeholder );
	$ac_attr    = $autocomplete ? ' autocomplete="' . esc_attr( $autocomplete ) . '"' : '';
	$label_esc  = $label ? wp_kses_post( $label ) . $req_mark : '';
	$label_cls_esc = esc_attr( ( $required ? 'required_field ' : '' ) . $label_cls );

	$container_class = 'form-row ' . esc_attr( $container_cls );
	$container_id    = $id_esc . '_field';
	$data_priority   = $sort !== '' ? ' data-priority="' . esc_attr( $sort ) . '"' : '';

	echo '<p class="' . $container_class . '" id="' . esc_attr( $container_id ) . '"' . $data_priority . '>';
	if ( $label_esc ) {
		echo '<label for="' . $id_esc . '" class="' . $label_cls_esc . '">' . $label_esc . '</label>';
	}
	echo '<span class="woocommerce-input-wrapper">';

	if ( $type === 'textarea' ) {
		echo '<textarea name="' . $name_esc . '" id="' . $id_esc . '" class="input-text ' . esc_attr( $input_cls ) . '" placeholder="' . $ph_esc . '" rows="2" cols="5"' . $req_attr . $ac_attr . '>' . esc_textarea( $value ) . '</textarea>';
	} else {
		echo '<input type="' . esc_attr( $type ) . '" name="' . $name_esc . '" id="' . $id_esc . '" class="input-text ' . esc_attr( $input_cls ) . '" value="' . $value_esc . '" placeholder="' . $ph_esc . '"' . $req_attr . $ac_attr . ' />';
	}

	echo '</span></p>';
}

// Чекаут: у полей адреса всегда должен быть тип, по которому woocommerce_form_field выведет инпут (text, email, tel и т.д.).
// Иначе в разметке остаётся только подпись без <input>. Приоритет 99999 — чтобы сработать после любых плагинов.
add_filter('woocommerce_form_field_args', function($args, $key, $value) {
    $address_keys = array('billing_first_name', 'billing_last_name', 'billing_address_1', 'billing_city', 'billing_postcode', 'billing_state', 'billing_email', 'billing_phone', 'shipping_first_name', 'shipping_last_name', 'shipping_address_1', 'shipping_city', 'shipping_postcode', 'shipping_state');
    if (!in_array($key, $address_keys, true)) {
        return $args;
    }
    $valid_types = array('text', 'password', 'datetime', 'datetime-local', 'date', 'month', 'time', 'week', 'number', 'email', 'url', 'tel', 'country', 'state', 'textarea', 'checkbox', 'select', 'radio', 'hidden');
    $type = isset($args['type']) ? $args['type'] : '';
    if ($type === '' || !in_array($type, $valid_types, true)) {
        if (strpos($key, 'email') !== false) {
            $args['type'] = 'email';
        } elseif (strpos($key, 'phone') !== false) {
            $args['type'] = 'tel';
        } else {
            $args['type'] = 'text';
        }
    }
    return $args;
}, 99999, 3);

// Чекаут: не даём фильтрам (в т.ч. типа minimal_checkout_fields_only с приоритетом 9999) убрать поля billing —
// если billing пустой, восстанавливаем стандартные поля. Приоритет 99999 чтобы сработать последним.
add_filter('woocommerce_checkout_fields', function($fields) {
    if (!empty($fields['billing']) || !function_exists('WC') || !WC()->countries) {
        return $fields;
    }
    $country = WC()->countries->get_base_country();
    $allowed = WC()->countries->get_allowed_countries();
    if (empty($allowed)) {
        $allowed = array($country => '');
    }
    $country = array_key_exists($country, $allowed) ? $country : key($allowed);
    $fields['billing'] = WC()->countries->get_address_fields($country, 'billing_');
    return $fields;
}, 99999);

// Вставка инпутов в строки чекаута, если их нет в DOM (после рендера или после address-i18n/update_checkout).
add_action('wp_footer', function() {
    if (!function_exists('is_checkout') || !is_checkout()) {
        return;
    }
    $fields = array(
        'billing_first_name'  => array( 'type' => 'text', 'required' => true ),
        'billing_last_name'   => array( 'type' => 'text', 'required' => true ),
        'billing_address_1'  => array( 'type' => 'text', 'required' => true ),
        'billing_address_2'  => array( 'type' => 'text', 'required' => false ),
        'billing_city'       => array( 'type' => 'text', 'required' => true ),
        'billing_state'      => array( 'type' => 'text', 'required' => true ),
        'billing_postcode'   => array( 'type' => 'text', 'required' => true ),
        'billing_email'      => array( 'type' => 'email', 'required' => true ),
        'billing_phone'      => array( 'type' => 'tel', 'required' => false ),
        'shipping_first_name'  => array( 'type' => 'text', 'required' => true ),
        'shipping_last_name'   => array( 'type' => 'text', 'required' => true ),
        'shipping_address_1'  => array( 'type' => 'text', 'required' => true ),
        'shipping_address_2'  => array( 'type' => 'text', 'required' => false ),
        'shipping_city'       => array( 'type' => 'text', 'required' => true ),
        'shipping_state'      => array( 'type' => 'text', 'required' => true ),
        'shipping_postcode'   => array( 'type' => 'text', 'required' => true ),
    );
    $json = wp_json_encode( $fields );
    if ( $json === false ) {
        $json = '{}';
    }
    ?>
    <script>
        (function() {
            var section = '.oor-checkout-section';
            var fieldConfig = <?php echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON for JS ?>;

            function injectMissingInputs() {
                var wrap = document.querySelector(section);
                if (!wrap) return;

                Object.keys(fieldConfig).forEach(function(name) {
                    var row = wrap.querySelector('#' + name + '_field');
                    if (!row) return;
                    var existingInput = row.querySelector('input, select, textarea');
                    if (existingInput) return;

                    var cfg = fieldConfig[name];
                    var wrapper = row.querySelector('.woocommerce-input-wrapper');
                    if (!wrapper) {
                        wrapper = document.createElement('span');
                        wrapper.className = 'woocommerce-input-wrapper';
                        row.appendChild(wrapper);
                    }

                    var input = document.createElement('input');
                    input.type = cfg.type;
                    input.name = name;
                    input.id = name;
                    input.className = 'input-text';
                    if (cfg.required) {
                        input.setAttribute('required', 'required');
                        input.setAttribute('aria-required', 'true');
                    }
                    wrapper.appendChild(input);
                    row.classList.add('oor-injected-field');
                });
            }

            function run() {
                injectMissingInputs();
                setTimeout(injectMissingInputs, 100);
                setTimeout(injectMissingInputs, 400);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', run);
            } else {
                run();
            }
            window.addEventListener('load', run);

            if (typeof jQuery !== 'undefined') {
                jQuery(document.body).on('updated_checkout country_to_state_changed wc_address_i18n_ready', function() {
                    setTimeout(run, 0);
                });
            }
        })();
    </script>
    <?php
}, 999);
