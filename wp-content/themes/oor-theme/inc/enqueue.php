<?php
/**
 * Подключение стилей и скриптов
 */

function oor_enqueue_scripts() {
    $theme_uri = get_template_directory_uri();
    // На сервере с nip.io: единый канонический URL для скриптов и для window.oorPaths (иначе JS грузит ресурсы с неправильного хоста).
    if (defined('OOR_FORCE_CANONICAL_HOST') && OOR_FORCE_CANONICAL_HOST) {
        $theme_uri = str_replace('.nip.io.nip.io', '.nip.io', $theme_uri);
        $host = parse_url($theme_uri, PHP_URL_HOST);
        if ($host === '45.141.102.187') {
            $theme_uri = str_replace('45.141.102.187', OOR_FORCE_CANONICAL_HOST, $theme_uri);
            // Используем протокол текущего запроса: если сервер без SSL — открывают по HTTP, и ресурсы должны грузиться по HTTP (иначе ERR_SSL_PROTOCOL_ERROR).
            $protocol = is_ssl() ? 'https://' : 'http://';
            $theme_uri = preg_replace('#^https?://#', $protocol, $theme_uri);
        }
    }
    $version = OOR_THEME_VERSION;
    // Версия для CSS: при изменении components.css на сервере меняется и ver= (обход кэша)
    $css_version = $version;
    $components_path = get_template_directory() . '/assets/css/components.css';
    if (file_exists($components_path)) {
        $mtime = filemtime($components_path);
        if ($mtime) {
            $css_version = $version . '.' . $mtime;
        }
    }

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
            $css_version
        );
        $prev_handle = $handle;
    }
    
    // JavaScript
    // GSAP (критический, загружается синхронно; jsDelivr как основной CDN — реже 404 в локальной среде)
    wp_enqueue_script(
        'gsap',
        'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js',
        [],
        '3.12.5',
        false
    );
    
    // Критические скрипты (синхронно)
    wp_enqueue_script(
        'oor-error-handler',
        $theme_uri . '/assets/js/modules/error-handler.js',
        [],
        $version,
        false
    );
    
    // config.js должен загружаться рано, чтобы OOR_PATHS был доступен
    wp_enqueue_script(
        'oor-config',
        $theme_uri . '/assets/js/config.js',
        [],
        $version,
        false
    );
    
    wp_enqueue_script(
        'oor-preloader',
        $theme_uri . '/assets/js/modules/preloader.js',
        ['oor-error-handler', 'oor-config'],
        $version,
        false
    );
    
    // Navigation модуль (нужен для main.js)
    wp_enqueue_script(
        'oor-navigation',
        $theme_uri . '/assets/js/modules/navigation.js',
        [],
        $version,
        false
    );
    
    // Скрипты с defer
    $defer_scripts = [
        'oor-main' => ['gsap', 'oor-navigation'],
        'oor-cursor' => [],
        'oor-scrollbar' => [],
        'oor-slider' => [],
        'oor-mobile-slider' => [],
        'oor-mobile-menu' => [],
        'oor-menu-sync' => [],
    ];
    
    foreach ($defer_scripts as $handle => $deps) {
        $file_name = str_replace('oor-', '', $handle);
        $file_path = $theme_uri . '/assets/js/' . $file_name . '.js';
        
        wp_enqueue_script(
            $handle,
            $file_path,
            $deps,
            $version,
            true
        );
    }
    
    // Дополнительные скрипты
    $additional_scripts = [
        'oor-artist-page' => 'artist-page.js',
        'oor-events-slider' => 'events-slider.js',
        'oor-merch-filter' => 'merch-filter.js',
        'oor-merch-images' => 'merch-images.js',
        'oor-rolling-text' => 'rolling-text.js',
        'oor-scale-container' => 'scale-container.js',
        'oor-size-sync' => 'size-sync.js',
        'oor-studio-equipment-accordion' => 'studio-equipment-accordion.js',
        'oor-talk-show-parallax' => 'talk-show-parallax.js',
    ];
    
    foreach ($additional_scripts as $handle => $file) {
        wp_enqueue_script(
            $handle,
            $theme_uri . '/assets/js/' . $file,
            ['gsap'],
            $version,
            true
        );
    }
    
    // Become Artist script загружается только на странице "Стать артистом"
    if (is_page('become-artist')) {
        wp_enqueue_script(
            'oor-become-artist',
            $theme_uri . '/assets/js/become-artist.js',
            [],
            $version,
            false // Загружаем синхронно, чтобы обработать форму сразу
        );
    }
    
    // Локализация для путей (передается в window.oorPaths)
    // Передаем в config.js, который загружается рано
    wp_localize_script('oor-config', 'oorPaths', array(
        'base' => $theme_uri,
        'assets' => $theme_uri . '/public/assets',
        'fonts' => $theme_uri . '/public/fonts',
        'css' => $theme_uri . '/assets/css',
        'js' => $theme_uri . '/assets/js'
    ));
    
    // Также передаем в main.js для обратной совместимости
    wp_localize_script('oor-main', 'oorPaths', array(
        'base' => $theme_uri,
        'assets' => $theme_uri . '/public/assets',
        'fonts' => $theme_uri . '/public/fonts',
        'css' => $theme_uri . '/assets/css',
        'js' => $theme_uri . '/assets/js'
    ));
    
    // Добавить defer атрибут (кроме become-artist)
    add_filter('script_loader_tag', function($tag, $handle) use ($defer_scripts, $additional_scripts) {
        // Не добавляем defer для become-artist
        if ($handle === 'oor-become-artist') {
            return $tag;
        }
        
        $all_defer_handles = array_merge(array_keys($defer_scripts), array_keys($additional_scripts));
        if (in_array($handle, $all_defer_handles)) {
            return str_replace(' src', ' defer src', $tag);
        }
        return $tag;
    }, 10, 2);
}
add_action('wp_enqueue_scripts', 'oor_enqueue_scripts');
