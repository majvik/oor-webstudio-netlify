<?php
/**
 * Автоматическая генерация изображений при загрузке
 * 
 * Когда загружается изображение @2x, автоматически генерируются:
 * - Версия 1x (половина размера)
 * - Все 3 формата для каждого размера: AVIF, WebP, PNG
 */

// Защита от прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Определяет, является ли файл @2x изображением
 */
function oor_is_2x_image($filename) {
    // Проверяем, содержит ли имя файла @2x перед расширением
    return preg_match('/@2x\.(png|jpg|jpeg|webp|avif)$/i', $filename);
}

/**
 * Получает базовое имя файла без @2x и расширения
 */
function oor_get_base_filename($filename) {
    // Удаляем @2x и расширение
    $base = preg_replace('/@2x\.(png|jpg|jpeg|webp|avif)$/i', '', $filename);
    // Если не было @2x, удаляем только расширение
    if ($base === $filename) {
        $base = preg_replace('/\.(png|jpg|jpeg|webp|avif)$/i', '', $filename);
    }
    return $base;
}

/**
 * Получает расширение файла
 */
function oor_get_file_extension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Конвертирует изображение в другой формат
 */
function oor_convert_image($source_path, $output_path, $format, $width = null, $height = null) {
    // Проверяем доступность Imagick (предпочтительно) или GD
    if (extension_loaded('imagick')) {
        return oor_convert_with_imagick($source_path, $output_path, $format, $width, $height);
    } elseif (function_exists('imagecreatefromstring')) {
        return oor_convert_with_gd($source_path, $output_path, $format, $width, $height);
    }
    
    return false;
}

/**
 * Конвертация с использованием Imagick
 */
function oor_convert_with_imagick($source_path, $output_path, $format, $width = null, $height = null) {
    try {
        $image = new Imagick($source_path);
        
        // Изменяем размер, если указан
        if ($width || $height) {
            $image->resizeImage(
                $width ?: 0,
                $height ?: 0,
                Imagick::FILTER_LANCZOS,
                1,
                true // сохраняем пропорции
            );
        }
        
        // Устанавливаем формат
        switch (strtolower($format)) {
            case 'avif':
                $image->setImageFormat('avif');
                $image->setImageCompressionQuality(100);
                break;
            case 'webp':
                $image->setImageFormat('webp');
                $image->setImageCompressionQuality(100);
                break;
            case 'png':
                $image->setImageFormat('png');
                break;
            case 'jpg':
            case 'jpeg':
                $image->setImageFormat('jpeg');
                $image->setImageCompressionQuality(100);
                break;
        }
        
        // Сохраняем
        $result = $image->writeImage($output_path);
        $image->destroy();
        
        return $result;
    } catch (Exception $e) {
        error_log('OOR Image Processing (Imagick): ' . $e->getMessage());
        return false;
    }
}

/**
 * Конвертация с использованием GD
 */
function oor_convert_with_gd($source_path, $output_path, $format, $width = null, $height = null) {
    // Определяем тип исходного изображения
    $image_info = getimagesize($source_path);
    if (!$image_info) {
        return false;
    }
    
    $source_type = $image_info[2];
    $source_width = $image_info[0];
    $source_height = $image_info[1];
    
    // Загружаем исходное изображение
    switch ($source_type) {
        case IMAGETYPE_JPEG:
            $source_image = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $source_image = imagecreatefrompng($source_path);
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagecreatefromwebp')) {
                $source_image = imagecreatefromwebp($source_path);
            } else {
                return false;
            }
            break;
        case IMAGETYPE_AVIF:
            if (function_exists('imagecreatefromavif')) {
                $source_image = imagecreatefromavif($source_path);
            } else {
                return false;
            }
            break;
        default:
            return false;
    }
    
    if (!$source_image) {
        return false;
    }
    
    // Вычисляем размеры для ресайза
    if ($width || $height) {
        $new_width = $width ?: ($source_width * ($height / $source_height));
        $new_height = $height ?: ($source_height * ($width / $source_width));
    } else {
        $new_width = $source_width;
        $new_height = $source_height;
    }
    
    // Создаем новое изображение
    $new_image = imagecreatetruecolor($new_width, $new_height);
    
    // Сохраняем прозрачность для PNG
    if ($source_type === IMAGETYPE_PNG) {
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        $transparent = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
        imagefill($new_image, 0, 0, $transparent);
    }
    
    // Ресайзим
    imagecopyresampled(
        $new_image,
        $source_image,
        0, 0, 0, 0,
        $new_width, $new_height,
        $source_width, $source_height
    );
    
    // Сохраняем в нужном формате
    $result = false;
    switch (strtolower($format)) {
        case 'avif':
            if (function_exists('imageavif')) {
                $result = imageavif($new_image, $output_path, 100);
            }
            break;
        case 'webp':
            if (function_exists('imagewebp')) {
                $result = imagewebp($new_image, $output_path, 100);
            }
            break;
        case 'png':
            $result = imagepng($new_image, $output_path, 0); // 0 = без сжатия (максимальное качество)
            break;
        case 'jpg':
        case 'jpeg':
            $result = imagejpeg($new_image, $output_path, 100);
            break;
    }
    
    // Освобождаем память
    imagedestroy($source_image);
    imagedestroy($new_image);
    
    return $result;
}

/**
 * Генерирует все варианты изображения при загрузке @2x
 */
function oor_generate_image_variants($attachment_id) {
    $file_path = get_attached_file($attachment_id);
    if (!$file_path || !file_exists($file_path)) {
        return false;
    }
    
    $filename = basename($file_path);
    $upload_dir = wp_upload_dir();
    $base_dir = $upload_dir['basedir'];
    $base_url = $upload_dir['baseurl'];
    
    // Проверяем, является ли это @2x изображением
    $is_2x = oor_is_2x_image($filename);
    $base_name = oor_get_base_filename($filename);
    $original_ext = oor_get_file_extension($filename);
    
    // Получаем размеры исходного изображения
    $image_info = getimagesize($file_path);
    if (!$image_info) {
        return false;
    }
    
    $original_width = $image_info[0];
    $original_height = $image_info[1];
    
    // Если это @2x, создаем версию 1x
    $width_1x = $is_2x ? intval($original_width / 2) : $original_width;
    $height_1x = $is_2x ? intval($original_height / 2) : $original_height;
    
    // Форматы для генерации
    $formats = ['avif', 'webp', 'png'];
    
    // Массив для хранения метаданных о связанных файлах
    $variants = [
        '1x' => [],
        '2x' => []
    ];
    
    // Генерируем варианты для 1x
    foreach ($formats as $format) {
        $variant_filename = $base_name . '.' . $format;
        $variant_path = dirname($file_path) . '/' . $variant_filename;
        
        // Пропускаем, если файл уже существует и это оригинал
        if ($variant_path === $file_path && $format === $original_ext) {
            continue;
        }
        
        // Генерируем 1x версию
        if ($is_2x) {
            $success = oor_convert_image($file_path, $variant_path, $format, $width_1x, $height_1x);
        } else {
            // Если загружен не @2x, просто конвертируем в другие форматы того же размера
            $success = oor_convert_image($file_path, $variant_path, $format);
        }
        
        if ($success && file_exists($variant_path)) {
            $variants['1x'][$format] = [
                'path' => $variant_path,
                'url' => str_replace($base_dir, $base_url, $variant_path),
                'filename' => $variant_filename
            ];
        }
    }
    
    // Генерируем варианты для 2x (если загружен @2x)
    if ($is_2x) {
        // Сначала сохраняем оригинал как вариант 2x
        $variants['2x'][$original_ext] = [
            'path' => $file_path,
            'url' => str_replace($base_dir, $base_url, $file_path),
            'filename' => $filename
        ];
        
        // Затем генерируем остальные форматы для 2x
        foreach ($formats as $format) {
            // Пропускаем, если это уже оригинальный формат
            if ($format === $original_ext) {
                continue;
            }
            
            $variant_filename = $base_name . '@2x.' . $format;
            $variant_path = dirname($file_path) . '/' . $variant_filename;
            
            // Генерируем 2x версию (оригинальный размер)
            $success = oor_convert_image($file_path, $variant_path, $format);
            
            if ($success && file_exists($variant_path)) {
                $variants['2x'][$format] = [
                    'path' => $variant_path,
                    'url' => str_replace($base_dir, $base_url, $variant_path),
                    'filename' => $variant_filename
                ];
            }
        }
    } else {
        // Если загружен не @2x, создаем @2x версию (увеличиваем в 2 раза)
        foreach ($formats as $format) {
            $variant_filename = $base_name . '@2x.' . $format;
            $variant_path = dirname($file_path) . '/' . $variant_filename;
            
            $success = oor_convert_image($file_path, $variant_path, $format, $original_width * 2, $original_height * 2);
            
            if ($success && file_exists($variant_path)) {
                $variants['2x'][$format] = [
                    'path' => $variant_path,
                    'url' => str_replace($base_dir, $base_url, $variant_path),
                    'filename' => $variant_filename
                ];
            }
        }
    }
    
    // Сохраняем метаданные о вариантах
    update_post_meta($attachment_id, '_oor_image_variants', $variants);
    
    return $variants;
}

/**
 * Хук для обработки загруженного изображения
 */
add_action('add_attachment', 'oor_process_uploaded_image');

function oor_process_uploaded_image($attachment_id) {
    // Проверяем, что это изображение
    if (!wp_attachment_is_image($attachment_id)) {
        return;
    }
    
    // Генерируем варианты
    oor_generate_image_variants($attachment_id);
}

/**
 * Получает варианты изображения
 */
function oor_get_image_variants($attachment_id) {
    $variants = get_post_meta($attachment_id, '_oor_image_variants', true);
    
    // Если варианты не найдены, пытаемся сгенерировать
    if (empty($variants)) {
        $variants = oor_generate_image_variants($attachment_id);
    }
    
    return $variants ?: [
        '1x' => [],
        '2x' => []
    ];
}

/**
 * Генерирует HTML для picture элемента с поддержкой всех форматов
 */
function oor_picture_element($attachment_id, $alt = '', $class = '', $attributes = []) {
    $variants = oor_get_image_variants($attachment_id);
    
    // Получаем оригинальный файл как fallback
    $original_file_path = get_attached_file($attachment_id);
    $original_url = wp_get_attachment_image_url($attachment_id, 'full');
    $original_filename = basename($original_file_path);
    
    // Определяем, является ли оригинал @2x
    $is_original_2x = oor_is_2x_image($original_filename);
    
    // Определяем формат оригинала
    $original_ext = oor_get_file_extension($original_filename);
    
    // Начинаем picture элемент
    $html = '<picture>';
    
    // AVIF source для 1x и 2x
    // ВАЖНО: Всегда используем 1x для обычных мониторов, 2x для high-DPI
    if (!empty($variants['1x']['avif']) && !empty($variants['2x']['avif'])) {
        // Оба варианта есть - используем оба (1x для обычных мониторов, 2x для high-DPI)
        $html .= sprintf(
            '<source srcset="%s 1x, %s 2x" type="image/avif">',
            esc_url($variants['1x']['avif']['url']),
            esc_url($variants['2x']['avif']['url'])
        );
    } elseif (!empty($variants['1x']['avif'])) {
        // Только 1x есть - используем для всех мониторов
        $html .= sprintf(
            '<source srcset="%s" type="image/avif">',
            esc_url($variants['1x']['avif']['url'])
        );
    } elseif (!empty($variants['2x']['avif'])) {
        // Только 2x есть - используем для high-DPI, но также как fallback для обычных
        // (браузер сам выберет подходящий вариант)
        $html .= sprintf(
            '<source srcset="%s 2x" type="image/avif">',
            esc_url($variants['2x']['avif']['url'])
        );
    }
    
    // WebP source для 1x и 2x
    // ВАЖНО: Всегда используем 1x для обычных мониторов, 2x для high-DPI
    if (!empty($variants['1x']['webp']) && !empty($variants['2x']['webp'])) {
        // Оба варианта есть - используем оба (1x для обычных мониторов, 2x для high-DPI)
        $html .= sprintf(
            '<source srcset="%s 1x, %s 2x" type="image/webp">',
            esc_url($variants['1x']['webp']['url']),
            esc_url($variants['2x']['webp']['url'])
        );
    } elseif (!empty($variants['1x']['webp'])) {
        // Только 1x есть - используем для всех мониторов
        $html .= sprintf(
            '<source srcset="%s" type="image/webp">',
            esc_url($variants['1x']['webp']['url'])
        );
    } elseif (!empty($variants['2x']['webp'])) {
        // Только 2x есть - используем для high-DPI, но также как fallback для обычных
        $html .= sprintf(
            '<source srcset="%s 2x" type="image/webp">',
            esc_url($variants['2x']['webp']['url'])
        );
    }
    
    // PNG/JPG fallback с srcset
    // ВАЖНО: Приоритет 1x для обычных мониторов, 2x для high-DPI
    $fallback_1x = null;
    $fallback_2x = null;
    
    // Ищем 1x вариант (приоритет для обычных мониторов)
    if (!empty($variants['1x']['png'])) {
        $fallback_1x = $variants['1x']['png']['url'];
    } elseif (!empty($variants['1x'][$original_ext])) {
        $fallback_1x = $variants['1x'][$original_ext]['url'];
    } elseif (!$is_original_2x) {
        // Если оригинал не @2x, используем его для 1x
        $fallback_1x = $original_url;
    }
    
    // Ищем 2x вариант (для high-DPI мониторов)
    if (!empty($variants['2x']['png'])) {
        $fallback_2x = $variants['2x']['png']['url'];
    } elseif (!empty($variants['2x'][$original_ext])) {
        $fallback_2x = $variants['2x'][$original_ext]['url'];
    } elseif ($is_original_2x) {
        // Если оригинал @2x, используем его для 2x
        $fallback_2x = $original_url;
    }
    
    // Формируем srcset
    // ВАЖНО: Всегда включаем 1x для обычных мониторов, если он есть
    $srcset_parts = [];
    if ($fallback_1x) {
        $srcset_parts[] = esc_url($fallback_1x) . ' 1x';
    }
    if ($fallback_2x) {
        $srcset_parts[] = esc_url($fallback_2x) . ' 2x';
    }
    
    // Если есть 1x, используем его как src (для обычных мониторов)
    // Если нет 1x, но есть 2x, используем 2x как src (браузер сам масштабирует)
    $src = $fallback_1x ?: ($fallback_2x ?: $original_url);
    $srcset = !empty($srcset_parts) ? implode(', ', $srcset_parts) : '';
    
    // Собираем атрибуты для img
    $img_attributes = array_merge([
        'src' => $src,
        'alt' => $alt,
        'class' => $class
    ], $attributes);
    
    // Добавляем srcset только если он не пустой
    if ($srcset) {
        $img_attributes['srcset'] = $srcset;
    }
    
    $img_attr_string = '';
    foreach ($img_attributes as $key => $value) {
        if ($value !== '') {
            $img_attr_string .= sprintf(' %s="%s"', esc_attr($key), esc_attr($value));
        }
    }
    
    $html .= sprintf('<img%s>', $img_attr_string);
    $html .= '</picture>';
    
    return $html;
}
