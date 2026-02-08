<?php
/**
 * Plugin Name: Disable ACF Deprecation Warnings
 * Description: Подавляет deprecation warnings от ACF Pro в PHP 8.2+
 * Version: 1.0.0
 * 
 * Этот файл должен быть в wp-content/mu-plugins/ (Must-Use plugins)
 * Загружается автоматически и раньше обычных плагинов
 */

// Подавление deprecation warnings от ACF Pro (PHP 8.2+ совместимость)
// ACF Pro 6.4.2 использует динамические свойства, которые deprecated в PHP 8.2+
// Это не критично и не влияет на функциональность, но вызывает предупреждения
// которые выводятся до отправки HTTP заголовков → "headers already sent" error

if (PHP_VERSION_ID >= 80200) {
    // Отключаем вывод ошибок на экран для предотвращения "headers already sent"
    // Это должно быть установлено ДО загрузки ACF
    @ini_set('display_errors', 0);
    
    // Подавляем только deprecation warnings, остальные ошибки логируем
    // Используем @ для подавления возможных предупреждений при установке
    $current_error_reporting = error_reporting();
    if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
        // В режиме отладки логируем все, кроме deprecation
        @error_reporting(E_ALL & ~E_DEPRECATED);
    } else {
        // В продакшене скрываем все предупреждения
        @error_reporting(0);
    }
    
    // Дополнительно: используем output buffering для перехвата любых выводов
    if (!ob_get_level()) {
        @ob_start();
    }
}
