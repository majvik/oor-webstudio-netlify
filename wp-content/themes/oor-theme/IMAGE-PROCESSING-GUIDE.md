# Руководство по автоматической обработке изображений

## Обзор

Система автоматически генерирует все необходимые варианты изображений при загрузке файла в WordPress Media Library.

## Как это работает

### При загрузке @2x изображения

Когда вы загружаете изображение с именем, содержащим `@2x` (например, `image@2x.png`, `photo@2x.avif`), система автоматически:

1. **Определяет, что это @2x изображение** по имени файла
2. **Создает версию 1x** (половина размера)
3. **Генерирует все 3 формата** для каждого размера:
   - AVIF
   - WebP
   - PNG (или оригинальный формат)

**Результат:** Из одного файла `image@2x.png` создаются:
- `image.avif` (1x)
- `image.webp` (1x)
- `image.png` (1x)
- `image@2x.avif` (2x)
- `image@2x.webp` (2x)
- `image@2x.png` (2x - оригинал)

### При загрузке обычного изображения (не @2x)

Если вы загружаете обычное изображение (например, `image.png`), система:

1. **Создает версию @2x** (увеличивает в 2 раза)
2. **Генерирует все 3 формата** для каждого размера

**Результат:** Из одного файла `image.png` создаются:
- `image.avif` (1x)
- `image.webp` (1x)
- `image.png` (1x - оригинал)
- `image@2x.avif` (2x)
- `image@2x.webp` (2x)
- `image@2x.png` (2x)

## Использование в шаблонах

### Функция `oor_picture_element()`

Генерирует полный HTML для `<picture>` элемента со всеми форматами и размерами:

```php
<?php
// Простое использование
$image_id = get_field('hero_image'); // ID изображения из ACF
echo oor_picture_element($image_id, 'Описание изображения');

// С дополнительными классами и атрибутами
echo oor_picture_element(
    $image_id,
    'Описание изображения',
    'my-custom-class',
    [
        'data-parallax-scale' => '1.3',
        'loading' => 'lazy',
        'decoding' => 'async'
    ]
);
?>
```

### Функция `oor_get_image_variants()`

Получает массив всех вариантов изображения:

```php
<?php
$variants = oor_get_image_variants($image_id);

// Структура $variants:
// [
//     '1x' => [
//         'avif' => ['url' => '...', 'path' => '...', 'filename' => '...'],
//         'webp' => ['url' => '...', 'path' => '...', 'filename' => '...'],
//         'png' => ['url' => '...', 'path' => '...', 'filename' => '...']
//     ],
//     '2x' => [
//         'avif' => ['url' => '...', 'path' => '...', 'filename' => '...'],
//         'webp' => ['url' => '...', 'path' => '...', 'filename' => '...'],
//         'png' => ['url' => '...', 'path' => '...', 'filename' => '...']
//     ]
// ]

// Пример использования для кастомного picture элемента
if (!empty($variants['1x']['avif']) && !empty($variants['2x']['avif'])) {
    echo '<source srcset="' . esc_url($variants['1x']['avif']['url']) . ' 1x, ' . esc_url($variants['2x']['avif']['url']) . ' 2x" type="image/avif">';
}
?>
```

## Примеры использования в шаблонах

### В `front-page.php` для ACF Gallery

```php
<?php
$gallery = get_field('talk_show_images');
if ($gallery) {
    foreach (array_slice($gallery, 0, 3) as $image) {
        echo '<div class="oor-out-of-talk-image">';
        echo oor_picture_element(
            $image['ID'],
            $image['alt'] ?: 'Talk Show Image',
            'oor-out-of-talk-img',
            ['loading' => 'lazy']
        );
        echo '</div>';
    }
}
?>
```

### В `single-artist.php` для главного изображения артиста

```php
<?php
$artist_image = get_field('artist_main_image');
if ($artist_image) {
    echo '<div class="artist-hero-image">';
    echo oor_picture_element(
        $artist_image['ID'],
        get_the_title() . ' - Main Image',
        'artist-main-img',
        [
            'data-parallax-scale' => '1.2',
            'loading' => 'eager',
            'fetchpriority' => 'high'
        ]
    );
    echo '</div>';
}
?>
```

### В `archive-event.php` для постеров событий

```php
<?php
$poster = get_field('event_poster');
if ($poster) {
    echo '<div class="event-poster">';
    echo oor_picture_element(
        $poster['ID'],
        get_the_title() . ' - Event Poster',
        'event-poster-img',
        ['loading' => 'lazy']
    );
    echo '</div>';
}
?>
```

## Технические детали

### Поддерживаемые форматы загрузки

- PNG
- JPEG/JPG
- WebP
- AVIF

### Используемые библиотеки

1. **Imagick** (предпочтительно) - если доступен
2. **GD** (fallback) - если Imagick недоступен

### Качество сжатия

- **AVIF:** 100% (максимальное качество)
- **WebP:** 100% (максимальное качество)
- **JPEG:** 100% (максимальное качество)
- **PNG:** Без потерь (compression level 0 - без сжатия)

### Хранение метаданных

Все варианты изображений сохраняются в метаданных WordPress attachment:
- Ключ: `_oor_image_variants`
- Формат: JSON массив с путями и URL всех вариантов

## Перегенерация вариантов

Если варианты не были созданы автоматически (например, при миграции), можно перегенерировать их:

```php
<?php
// В шаблоне или через WP-CLI
$image_id = 123; // ID изображения
$variants = oor_generate_image_variants($image_id);
?>
```

Или через WP-CLI:

```bash
docker-compose exec wordpress wp --allow-root eval '$id = 123; oor_generate_image_variants($id);'
```

## Отладка

Если варианты не генерируются:

1. **Проверьте логи PHP:**
   ```bash
   docker-compose exec wordpress tail -f /var/log/php-fpm/error.log
   ```

2. **Проверьте доступность библиотек:**
   ```bash
   docker-compose exec wordpress php -r "echo 'Imagick: ' . (extension_loaded('imagick') ? 'OK' : 'FAIL') . PHP_EOL; echo 'GD: ' . (extension_loaded('gd') ? 'OK' : 'FAIL') . PHP_EOL;"
   ```

3. **Проверьте права на запись:**
   ```bash
   docker-compose exec wordpress ls -la /var/www/html/wp-content/uploads/
   ```

## Производительность

- Генерация происходит **асинхронно** при загрузке файла
- Все варианты создаются **одновременно** в фоне
- Если генерация не удалась, оригинальное изображение все равно сохраняется
- Варианты генерируются **только один раз** при загрузке

## Ограничения

- Максимальный размер файла: 50MB (настроено в `Dockerfile.wordpress`)
- Требуется PHP с поддержкой Imagick или GD
- AVIF требует PHP 8.1+ с поддержкой GD или Imagick
