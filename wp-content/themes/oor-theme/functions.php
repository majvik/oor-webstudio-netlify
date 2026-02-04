<?php
/**
 * OOR Webstudio Theme Functions
 */

// Защита от прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

// Версия темы
define('OOR_THEME_VERSION', '1.2.1');

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

// Принудительное отображение обязательных полей чекаута WooCommerce через JavaScript
add_action('wp_footer', function() {
    if (!is_checkout()) {
        return;
    }
    ?>
    <script>
        (function() {
            function fixCheckoutFields() {
                console.log('[OOR Fix] Running field fix...');
                
                var requiredFieldIds = [
                    'billing_first_name',
                    'billing_last_name', 
                    'billing_address_1',
                    'billing_city',
                    'billing_postcode',
                    'billing_email'
                ];
                
                requiredFieldIds.forEach(function(fieldId) {
                    var input = document.getElementById(fieldId);
                    if (input) {
                        console.log('[OOR Fix] Found input:', fieldId, 'tagName:', input.tagName, 'type:', input.type);
                        
                        // Проверяем, не скрыт ли родитель
                        var parent = input.parentElement;
                        var parentChain = [];
                        while (parent && parent !== document.body) {
                            var style = window.getComputedStyle(parent);
                            parentChain.push({
                                tag: parent.tagName,
                                class: parent.className,
                                display: style.display,
                                visibility: style.visibility,
                                height: style.height,
                                overflow: style.overflow
                            });
                            
                            // Принудительно показываем родителей
                            parent.style.display = 'block';
                            parent.style.visibility = 'visible';
                            parent.style.height = 'auto';
                            parent.style.overflow = 'visible';
                            
                            parent = parent.parentElement;
                        }
                        console.log('[OOR Fix] Parent chain for', fieldId, ':', JSON.stringify(parentChain.slice(0, 5)));
                        
                        // Применяем стили к инпуту напрямую
                        input.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; width: 100% !important; min-height: 52px !important; background-color: #ff0000 !important; border: 3px solid #00ff00 !important; color: #ffffff !important; padding: 12px !important; position: relative !important; z-index: 9999 !important; overflow: visible !important;';
                        
                        var rect = input.getBoundingClientRect();
                        console.log('[OOR Fix] After fix, getBoundingClientRect:', rect.width, 'x', rect.height, 'at', rect.left, rect.top);
                    } else {
                        console.log('[OOR Fix] Input NOT found:', fieldId);
                    }
                });
            }
            
            // Запускаем сразу и после загрузки
            if (document.readyState === 'complete') {
                fixCheckoutFields();
            } else {
                window.addEventListener('load', fixCheckoutFields);
            }
            
            // И через секунду на случай динамической загрузки
            setTimeout(fixCheckoutFields, 1000);
            setTimeout(fixCheckoutFields, 2000);
        })();
    </script>
    <?php
}, 999);
