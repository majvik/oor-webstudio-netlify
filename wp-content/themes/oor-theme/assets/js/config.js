/**
 * Конфигурация путей для ресурсов
 * 
 * Для статического сайта: базовый путь пустой (абсолютные пути от корня)
 * Для WordPress: заменить на get_template_directory_uri() или относительные пути
 * 
 * Пример для WordPress:
 * const OOR_BASE_URL = '<?php echo get_template_directory_uri(); ?>';
 * или
 * const OOR_BASE_URL = '/wp-content/themes/oor-theme';
 */

// Базовый URL для ресурсов
// В WordPress будет переопределено через wp_localize_script
const OOR_BASE_URL = (typeof window !== 'undefined' && window.oorPaths && window.oorPaths.base) 
  ? window.oorPaths.base 
  : '';

// Пути к директориям
// В WordPress будет переопределено через wp_localize_script
const OOR_PATHS = (typeof window !== 'undefined' && window.oorPaths) 
  ? window.oorPaths 
  : {
      base: OOR_BASE_URL,
      public: OOR_BASE_URL + '/public',
      src: OOR_BASE_URL + '/src',
      assets: (OOR_BASE_URL ? OOR_BASE_URL + '/public/assets' : '/public/assets'),
      fonts: (OOR_BASE_URL ? OOR_BASE_URL + '/public/fonts' : '/public/fonts'),
      css: OOR_BASE_URL + '/src/css',
      js: OOR_BASE_URL + '/src/js'
    };

// Экспорт для использования в других модулях
if (typeof window !== 'undefined') {
  window.OOR_PATHS = OOR_PATHS;
}

// Для Node.js окружения (если нужно)
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { OOR_PATHS, OOR_BASE_URL };
}
