# Обновление структуры ACF поля для слайдера артистов

## Проблема

Сейчас в слайдере артистов на главной странице используется изображение из ACF полей артиста (`artist_image`), но нужно:
1. **Отдельное изображение для слайдера** - загружается отдельно в ACF поле главной страницы
2. **Ссылка на страницу артиста** - выбирается из CPT `artist`
3. **Разные изображения** - на главной и на странице артиста должны быть разные

## Решение

### Структура ACF поля

**Field Group: "Главная страница"**
- **Repeater:** `artists_slider`
  - **Image:** `slider_image` - изображение для слайдера на главной (отдельное поле)
  - **Post Object:** `artist` - выбор артиста из CPT `artist` (для ссылки)

**Field Group: "Страница артиста"**
- **Image:** `artist_main_image` - главное изображение артиста (используется на странице артиста)

## Как обновить в WordPress

1. Перейдите в **Custom Fields → Field Groups**
2. Найдите группу **"Главная страница"**
3. Откройте Repeater поле **"Артисты в слайдере"** (`artists_slider`)
4. Добавьте новое поле:
   - **Label:** "Изображение для слайдера"
   - **Name:** `slider_image`
   - **Type:** Image
   - **Return Format:** Image Array
   - **Preview Size:** Medium
5. Сохраните изменения

## Использование в шаблонах

### front-page.php (уже обновлен)

```php
$artists_slider = get_field('artists_slider');
foreach ($artists_slider as $item) {
    $slider_image = $item['slider_image']; // Отдельное изображение для слайдера
    $artist = $item['artist']; // Post Object для ссылки
    
    // Используем slider_image для отображения
    // Используем artist для ссылки и имени
}
```

### single-artist.php (нужно обновить)

```php
// Используем artist_main_image из ACF полей артиста
$artist_main_image = get_field('artist_main_image');
if ($artist_main_image) {
    echo oor_picture_element($artist_main_image['ID'], get_the_title());
}
```

## Важно

- **Изображения разные:** `slider_image` на главной ≠ `artist_main_image` на странице артиста
- **Ссылка берется из CPT:** поле `artist` - это Post Object, который выбирает артиста из CPT
- **Автоматическая генерация:** если загрузить `@2x` изображение, система автоматически создаст все варианты (1x/2x, AVIF/WebP/PNG)
