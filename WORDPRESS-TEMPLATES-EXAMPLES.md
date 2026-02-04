# Примеры PHP шаблонов для WordPress

> **Версия:** 1.0.0  
> **Дата:** 2025-01-15

Этот документ содержит примеры PHP шаблонов для миграции на WordPress. Используйте их как основу для создания реальных шаблонов.

---

## 📄 style.css (заголовок темы)

```css
/*
Theme Name: OOR Webstudio
Theme URI: https://outofrecords.com
Author: OOR Development Team
Author URI: https://outofrecords.com
Description: Имиджевый сайт Out of Records с поддержкой ACF
Version: 1.0.0
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 8.0
License: Proprietary
Text Domain: oor
*/

/* Все стили находятся в /assets/css/ */
```

---

## ⚙️ functions.php

```php
<?php
/**
 * OOR Webstudio Theme Functions
 */

// Защита от прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

// Версия темы
define('OOR_THEME_VERSION', '1.0.0');

// Подключение вспомогательных файлов
require_once get_template_directory() . '/inc/cpt.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/body-classes.php';

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

// Отключение Gutenberg
add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);

// Отключение стандартных стилей WordPress
add_action('wp_enqueue_scripts', function() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-block-style');
}, 100);
```

---

## 📑 header.php

```php
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="preloader-active">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php wp_head(); ?>
</head>
<body <?php body_class('preloader-active'); ?>>
    <?php wp_body_open(); ?>
    
    <!-- Preloader -->
    <div id="preloader" class="oor-preloader">
        <div class="oor-preloader-progress-bar" id="preloader-progress-bar"></div>
        <div class="oor-preloader-content">
            <button id="enter-button" class="oor-enter-button">Войти</button>
        </div>
    </div>
    
    <!-- Splash Screen -->
    <div id="splash-screen" class="oor-splash-screen">
        <img id="splash-gif" class="oor-splash-gif" 
             src="<?php echo get_template_directory_uri(); ?>/public/assets/splash.gif" 
             alt="Splash" width="400" height="400">
        <button id="enter-button-splash" class="oor-splash-enter-button rolling-button">
            <span class="tn-atom">[  ВОЙТИ В OOR  ]</span>
        </button>
    </div>
    
    <!-- Header -->
    <header class="oor-header">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="oor-logo">
            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/logo.svg" 
                 alt="<?php bloginfo('name'); ?>" 
                 width="73" height="20" 
                 class="oor-media-cover">
        </a>
        
        <div class="oor-header-right">
            <nav class="oor-nav">
                <div class="oor-nav-list" role="navigation" aria-label="Main">
                    <?php
                    // Статическое меню (можно заменить на wp_nav_menu)
                    $menu_items = [
                        ['url' => home_url('/'), 'text' => 'Main', 'slug' => 'main'],
                        ['url' => home_url('/manifest'), 'text' => 'Манифест', 'slug' => 'manifest'],
                        ['url' => home_url('/artists'), 'text' => 'Артисты', 'slug' => 'artists'],
                        ['url' => home_url('/studio'), 'text' => 'Студия', 'slug' => 'studio'],
                        ['url' => home_url('/services'), 'text' => 'Услуги', 'slug' => 'services'],
                    ];
                    
                    foreach ($menu_items as $item) {
                        $active = (is_page($item['slug']) || 
                                  (is_front_page() && $item['slug'] === 'main')) 
                                  ? 'oor-nav-link--active' : '';
                        echo sprintf(
                            '<div class="oor-nav-item">' .
                            '<a href="%s" class="oor-nav-link rolling-button %s" data-menu-item="%s">' .
                            '<span class="tn-atom">%s</span></a><span>/</span>' .
                            '</div>',
                            esc_url($item['url']),
                            esc_attr($active),
                            esc_attr($item['slug']),
                            esc_html($item['text'])
                        );
                    }
                    ?>
                </div>
            </nav>
            
            <button class="oor-btn-small" id="contact-button">
                <span class="oor-btn-small-text">Связаться</span>
                <div class="oor-btn-small-icon">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" 
                         alt="" width="17" height="17">
                </div>
            </button>
            
            <div class="oor-burger-menu" id="mobile-menu-toggle">
                <div class="oor-burger-icon">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/burger-icon.svg" 
                         alt="Menu" width="24" height="24">
                </div>
            </div>
        </div>
    </header>
    
    <main id="main-content">
```

---

## 📑 footer.php

```php
    </main>
    
    <footer class="oor-footer">
        <div class="oor-container">
            <div class="oor-grid">
                <div class="oor-col-6">
                    <p class="oor-footer-text">
                        &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Все права защищены.
                    </p>
                </div>
                <div class="oor-col-6">
                    <div class="oor-footer-links">
                        <!-- Социальные сети или другие ссылки -->
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Fullscreen Video Modal -->
    <div class="oor-fullscreen-video" id="fullscreen-video">
        <video class="oor-fullscreen-video-element" controls 
               poster="<?php echo get_template_directory_uri(); ?>/public/assets/video-cover.avif">
            <source src="<?php echo get_template_directory_uri(); ?>/public/assets/OUTOFREC_reel_v4_nologo-large.webm" 
                    type="video/webm">
            <source src="<?php echo get_template_directory_uri(); ?>/public/assets/OUTOFREC_reel_v4_nologo-large.mp4" 
                    type="video/mp4">
        </video>
        <button class="oor-fullscreen-close" id="fullscreen-close">
            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" 
                 alt="Закрыть" width="17" height="17">
        </button>
    </div>
    
    <?php wp_footer(); ?>
</body>
</html>
```

---

## 🏠 front-page.php (Главная страница)

```php
<?php
/**
 * Template Name: Главная
 * Главная страница сайта
 */

get_header();
?>

<div class="oor-hero-section">
    <div class="oor-hero-main">
        <?php
        // Hero Video фон
        $hero_bg_video = get_field('hero_background_video');
        $hero_modal_video = get_field('hero_modal_video');
        ?>
        
        <?php if ($hero_bg_video): ?>
            <video class="oor-hero-video" autoplay muted loop playsinline 
                   preload="metadata" 
                   poster="<?php echo get_template_directory_uri(); ?>/public/assets/video-cover.avif">
                <?php
                $video_url = is_array($hero_bg_video) ? $hero_bg_video['url'] : $hero_bg_video;
                $video_ext = pathinfo($video_url, PATHINFO_EXTENSION);
                ?>
                <?php if ($video_ext === 'webm'): ?>
                    <source src="<?php echo esc_url($video_url); ?>" type="video/webm">
                <?php endif; ?>
                <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                <div class="oor-hero-video-fallback"></div>
            </video>
        <?php endif; ?>
        
        <!-- Кликабельный оверлей для открытия полноэкранного видео -->
        <div class="oor-hero-video-overlay" id="hero-video-overlay">
            <?php if ($hero_modal_video): ?>
                <?php
                $modal_video_url = is_array($hero_modal_video) ? $hero_modal_video['url'] : $hero_modal_video;
                ?>
                <video class="oor-hero-video-preview" 
                       src="<?php echo esc_url($modal_video_url); ?>" 
                       muted loop playsinline></video>
            <?php endif; ?>
        </div>
        
        <!-- Hero контент -->
        <div class="oor-hero-content">
            <h1 class="oor-hero-title">OUT OF RECORDS</h1>
            <p class="oor-hero-description">Музыкальная студия нового поколения</p>
        </div>
    </div>
</div>

<!-- Артисты в слайдере -->
<?php
$artists_slider = get_field('artists_slider');
if ($artists_slider):
?>
    <section class="oor-artists-slider-section">
        <div class="oor-container">
            <div class="oor-artists-slider" id="artists-slider">
                <?php foreach ($artists_slider as $item): 
                    $artist = $item['artist']; // Post Object
                    if (!$artist) continue;
                    
                    $artist_url = get_permalink($artist->ID);
                    $artist_name = get_the_title($artist->ID);
                    $artist_image = get_field('artist_image', $artist->ID);
                ?>
                    <div class="slide">
                        <a href="<?php echo esc_url($artist_url); ?>" 
                           class="slide-media text-cuberto-cursor-2" 
                           data-text="Все артисты">
                            <?php if ($artist_image): ?>
                                <picture>
                                    <source srcset="<?php echo esc_url($artist_image['sizes']['medium']); ?> 1x, 
                                                    <?php echo esc_url($artist_image['sizes']['large']); ?> 2x" 
                                            type="image/avif">
                                    <source srcset="<?php echo esc_url($artist_image['sizes']['medium']); ?> 1x, 
                                                    <?php echo esc_url($artist_image['sizes']['large']); ?> 2x" 
                                            type="image/webp">
                                    <img src="<?php echo esc_url($artist_image['url']); ?>" 
                                         srcset="<?php echo esc_url($artist_image['url']); ?> 1x, 
                                                 <?php echo esc_url($artist_image['sizes']['large']); ?> 2x" 
                                         alt="<?php echo esc_attr($artist_name); ?>" 
                                         draggable="false">
                                </picture>
                            <?php endif; ?>
                        </a>
                        <span class="artist-name"><?php echo esc_html($artist_name); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- События -->
<?php
$events_section = get_field('events_section');
if ($events_section):
?>
    <section class="oor-events-section">
        <div class="oor-container">
            <div class="oor-events-grid">
                <?php foreach ($events_section as $event): 
                    $poster = $event['event_poster'];
                    $sold_out = $event['sold_out'];
                    $ticket_text = $event['buy_ticket_text'] ?: 'Купить билет';
                    $ticket_url = $event['ticket_url'];
                ?>
                    <div class="oor-event-card">
                        <?php if ($poster): ?>
                            <img src="<?php echo esc_url($poster['url']); ?>" 
                                 alt="<?php echo esc_attr($poster['alt']); ?>"
                                 loading="lazy" 
                                 decoding="async">
                        <?php endif; ?>
                        
                        <?php if ($sold_out): ?>
                            <span class="oor-event-sold-out">Распродано</span>
                        <?php elseif ($ticket_url): ?>
                            <a href="<?php echo esc_url($ticket_url); ?>" 
                               class="oor-event-ticket-btn" 
                               target="_blank" 
                               rel="noopener">
                                <?php echo esc_html($ticket_text); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php get_footer(); ?>
```

---

## 🎤 single-artist.php (Страница артиста)

```php
<?php
/**
 * Template для страницы артиста
 */

get_header();

while (have_posts()):
    the_post();
    
    $artist_image = get_field('artist_image');
    $short_description = get_field('short_description');
    $full_description = get_field('full_description');
    $social_links = get_field('social_links');
    $tracks = get_field('tracks');
?>

<div class="oor-artist-page-content">
    <div class="oor-container">
        <div class="oor-grid">
            <!-- Изображение артиста -->
            <div class="oor-col-6">
                <?php if ($artist_image): ?>
                    <img src="<?php echo esc_url($artist_image['url']); ?>" 
                         alt="<?php echo esc_attr($artist_image['alt'] ?: get_the_title()); ?>"
                         loading="eager"
                         fetchpriority="high">
                <?php endif; ?>
            </div>
            
            <!-- Информация об артисте -->
            <div class="oor-col-6">
                <h1 class="oor-artist-title"><?php the_title(); ?></h1>
                
                <?php if ($short_description): ?>
                    <p class="oor-artist-short-description">
                        <?php echo esc_html($short_description); ?>
                    </p>
                <?php endif; ?>
                
                <?php if ($full_description): ?>
                    <div class="oor-artist-full-description">
                        <?php echo esc_html($full_description); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Социальные сети -->
                <?php if ($social_links): ?>
                    <div class="oor-artist-social">
                        <?php foreach ($social_links as $link): 
                            $platform = $link['platform'];
                            $url = $link['url'];
                        ?>
                            <a href="<?php echo esc_url($url); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="oor-artist-social-link">
                                <?php echo esc_html($platform); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Треки -->
        <?php if ($tracks): ?>
            <div class="oor-artist-tracks">
                <h2 class="oor-artist-tracks-title">Треки</h2>
                <div class="oor-artist-tracks-grid">
                    <?php foreach ($tracks as $track): 
                        $cover = $track['track_cover'];
                        $name = $track['track_name'];
                        $mp3 = $track['track_mp3'];
                    ?>
                        <div class="oor-artist-track">
                            <?php if ($cover): ?>
                                <img src="<?php echo esc_url($cover['url']); ?>" 
                                     alt="<?php echo esc_attr($name); ?>"
                                     loading="lazy">
                            <?php endif; ?>
                            
                            <div class="oor-artist-track-info">
                                <span class="oor-artist-track-name"><?php echo esc_html($name); ?></span>
                                
                                <?php if ($mp3): 
                                    $mp3_url = is_array($mp3) ? $mp3['url'] : $mp3;
                                ?>
                                    <audio controls>
                                        <source src="<?php echo esc_url($mp3_url); ?>" type="audio/mpeg">
                                    </audio>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
endwhile;
get_footer();
```

---

## 📅 archive-event.php (Архив событий)

```php
<?php
/**
 * Template для архива событий
 */

get_header();
?>

<div class="oor-events-archive">
    <div class="oor-container">
        <h1 class="oor-events-archive-title">События</h1>
        
        <div class="oor-events-grid">
            <?php if (have_posts()): 
                while (have_posts()): 
                    the_post();
                    
                    $poster = get_field('event_poster');
                    $sold_out = get_field('sold_out');
                    $ticket_text = get_field('buy_ticket_text') ?: 'Купить билет';
                    $ticket_url = get_field('ticket_url');
                    $event_date = get_field('event_date');
                    $event_location = get_field('event_location');
            ?>
                <article class="oor-event-card">
                    <?php if ($poster): ?>
                        <img src="<?php echo esc_url($poster['url']); ?>" 
                             alt="<?php echo esc_attr($poster['alt'] ?: get_the_title()); ?>"
                             loading="lazy"
                             decoding="async">
                    <?php endif; ?>
                    
                    <div class="oor-event-card-content">
                        <h2 class="oor-event-card-title">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        
                        <?php if ($event_date): ?>
                            <p class="oor-event-card-date">
                                <?php echo esc_html(date_i18n('d.m.Y', strtotime($event_date))); ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($event_location): ?>
                            <p class="oor-event-card-location">
                                <?php echo esc_html($event_location); ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($sold_out): ?>
                            <span class="oor-event-sold-out">Распродано</span>
                        <?php elseif ($ticket_url): ?>
                            <a href="<?php echo esc_url($ticket_url); ?>" 
                               class="oor-event-ticket-btn" 
                               target="_blank" 
                               rel="noopener">
                                <?php echo esc_html($ticket_text); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php 
                endwhile;
            else:
            ?>
                <p>Событий пока нет.</p>
            <?php endif; ?>
        </div>
        
        <?php
        // Пагинация
        the_posts_pagination([
            'prev_text' => '←',
            'next_text' => '→',
        ]);
        ?>
    </div>
</div>

<?php get_footer(); ?>
```

---

## 🎮 page-dawgs.php (Страница DAWGS)

```php
<?php
/**
 * Template Name: DAWGS
 * Страница DAWGS с ACF полями
 */

get_header();

$main_image = get_field('main_image');
$players = get_field('players');
$partners = get_field('partners');
?>

<div class="oor-dawgs-page">
    <div class="oor-container">
        <!-- Главное изображение -->
        <?php if ($main_image): ?>
            <div class="oor-dawgs-hero">
                <img src="<?php echo esc_url($main_image['url']); ?>" 
                     alt="<?php echo esc_attr($main_image['alt']); ?>"
                     loading="eager"
                     fetchpriority="high">
            </div>
        <?php endif; ?>
        
        <!-- Игроки -->
        <?php if ($players): ?>
            <section class="oor-dawgs-players">
                <h2 class="oor-dawgs-section-title">Игроки</h2>
                <div class="oor-dawgs-players-grid">
                    <?php foreach ($players as $player): 
                        $name = $player['player_name'];
                        $description = $player['player_description'];
                        $image = $player['player_image'];
                    ?>
                        <div class="oor-dawgs-player">
                            <?php if ($image): ?>
                                <img src="<?php echo esc_url($image['url']); ?>" 
                                     alt="<?php echo esc_attr($name); ?>"
                                     loading="lazy">
                            <?php endif; ?>
                            
                            <h3 class="oor-dawgs-player-name"><?php echo esc_html($name); ?></h3>
                            
                            <?php if ($description): ?>
                                <p class="oor-dawgs-player-description">
                                    <?php echo esc_html($description); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        
        <!-- Партнеры -->
        <?php if ($partners): ?>
            <section class="oor-dawgs-partners">
                <h2 class="oor-dawgs-section-title">Партнеры</h2>
                <div class="oor-dawgs-partners-grid">
                    <?php foreach ($partners as $partner): 
                        $name = $partner['partner_name'];
                        $image = $partner['partner_image'];
                        $description = $partner['partner_description'];
                    ?>
                        <div class="oor-dawgs-partner">
                            <?php if ($image): ?>
                                <img src="<?php echo esc_url($image['url']); ?>" 
                                     alt="<?php echo esc_attr($name); ?>"
                                     loading="lazy">
                            <?php endif; ?>
                            
                            <h3 class="oor-dawgs-partner-name"><?php echo esc_html($name); ?></h3>
                            
                            <?php if ($description): ?>
                                <p class="oor-dawgs-partner-description">
                                    <?php echo esc_html($description); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
```

---

## 📻 page-talk-show.php (Страница Talk-show)

```php
<?php
/**
 * Template Name: Talk-show
 * Страница Talk-show с ACF полями
 */

get_header();

$video_1 = get_field('video_1');
$video_2 = get_field('video_2');
$podcast_image = get_field('podcast_image');
$podcast_name = get_field('podcast_name');
$podcast_url = get_field('podcast_url');
?>

<div class="oor-talk-show-page">
    <div class="oor-container">
        <!-- Видео -->
        <section class="oor-talk-show-videos">
            <?php if ($video_1): 
                $video_1_url = is_array($video_1) ? $video_1['url'] : $video_1;
                $is_external_url = filter_var($video_1_url, FILTER_VALIDATE_URL) && 
                                   !strpos($video_1_url, wp_upload_dir()['baseurl']);
            ?>
                <div class="oor-talk-show-video-1">
                    <?php if ($is_external_url): ?>
                        <!-- Внешнее видео (YouTube, Vimeo) -->
                        <iframe src="<?php echo esc_url($video_1_url); ?>" 
                                frameborder="0" 
                                allowfullscreen></iframe>
                    <?php else: ?>
                        <!-- Локальное видео -->
                        <video controls>
                            <source src="<?php echo esc_url($video_1_url); ?>" type="video/mp4">
                        </video>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($video_2): 
                $video_2_url = is_array($video_2) ? $video_2['url'] : $video_2;
                $is_external_url = filter_var($video_2_url, FILTER_VALIDATE_URL) && 
                                   !strpos($video_2_url, wp_upload_dir()['baseurl']);
            ?>
                <div class="oor-talk-show-video-2">
                    <?php if ($is_external_url): ?>
                        <iframe src="<?php echo esc_url($video_2_url); ?>" 
                                frameborder="0" 
                                allowfullscreen></iframe>
                    <?php else: ?>
                        <video controls>
                            <source src="<?php echo esc_url($video_2_url); ?>" type="video/mp4">
                        </video>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
        
        <!-- Подкаст -->
        <?php if ($podcast_image || $podcast_name || $podcast_url): ?>
            <section class="oor-talk-show-podcast">
                <?php if ($podcast_image): ?>
                    <img src="<?php echo esc_url($podcast_image['url']); ?>" 
                         alt="<?php echo esc_attr($podcast_name ?: 'Подкаст'); ?>"
                         loading="lazy">
                <?php endif; ?>
                
                <?php if ($podcast_name): ?>
                    <h2 class="oor-talk-show-podcast-name">
                        <?php echo esc_html($podcast_name); ?>
                    </h2>
                <?php endif; ?>
                
                <?php if ($podcast_url): ?>
                    <a href="<?php echo esc_url($podcast_url); ?>" 
                       class="oor-talk-show-podcast-link" 
                       target="_blank" 
                       rel="noopener">
                        Слушать подкаст
                    </a>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
```

---

## ⚙️ inc/enqueue.php

```php
<?php
/**
 * Подключение стилей и скриптов
 */

function oor_enqueue_scripts() {
    $theme_uri = get_template_directory_uri();
    $version = OOR_THEME_VERSION;
    
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
    
    // JavaScript
    // GSAP (критический, загружается синхронно)
    wp_enqueue_script(
        'gsap',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
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
    
    wp_enqueue_script(
        'oor-preloader',
        $theme_uri . '/assets/js/modules/preloader.js',
        ['oor-error-handler'],
        $version,
        false
    );
    
    // Скрипты с defer
    $defer_scripts = [
        'oor-main' => ['gsap'],
        'oor-cursor' => [],
        'oor-scrollbar' => [],
        'oor-slider' => [],
        'oor-mobile-menu' => [],
        'oor-menu-sync' => [],
    ];
    
    foreach ($defer_scripts as $handle => $deps) {
        wp_enqueue_script(
            $handle,
            $theme_uri . '/assets/js/' . str_replace('oor-', '', $handle) . '.js',
            $deps,
            $version,
            true
        );
    }
    
    // Локализация для путей
    wp_localize_script('oor-main', 'oorPaths', [
        'base' => $theme_uri,
        'assets' => $theme_uri . '/public/assets',
        'fonts' => $theme_uri . '/public/fonts',
        'css' => $theme_uri . '/assets/css',
        'js' => $theme_uri . '/assets/js'
    ]);
    
    // Добавить defer атрибут
    add_filter('script_loader_tag', function($tag, $handle) {
        $defer_handles = array_keys($defer_scripts);
        if (in_array($handle, $defer_handles)) {
            return str_replace(' src', ' defer src', $tag);
        }
        return $tag;
    }, 10, 2);
}
add_action('wp_enqueue_scripts', 'oor_enqueue_scripts');
```

---

## ⚙️ inc/body-classes.php

```php
<?php
/**
 * Управление body-классами
 */

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
    } elseif (is_page('contacts')) {
        $classes[] = 'oor-contacts-page';
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

---

## ⚙️ inc/cpt.php

```php
<?php
/**
 * Регистрация Custom Post Types
 */

// Custom Post Type: Артисты
function oor_register_artist_post_type() {
    register_post_type('artist', [
        'labels' => [
            'name' => 'Артисты',
            'singular_name' => 'Артист',
            'add_new' => 'Добавить артиста',
            'add_new_item' => 'Добавить нового артиста',
            'edit_item' => 'Редактировать артиста',
            'new_item' => 'Новый артист',
            'view_item' => 'Просмотреть артиста',
            'search_items' => 'Искать артистов',
            'not_found' => 'Артисты не найдены',
            'not_found_in_trash' => 'В корзине артистов не найдено',
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'artists'],
        'supports' => ['title', 'thumbnail'],
        'menu_icon' => 'dashicons-microphone',
        'show_in_rest' => false, // Отключаем Gutenberg
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
            'edit_item' => 'Редактировать событие',
            'new_item' => 'Новое событие',
            'view_item' => 'Просмотреть событие',
            'search_items' => 'Искать события',
            'not_found' => 'События не найдены',
            'not_found_in_trash' => 'В корзине событий не найдено',
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'events'],
        'supports' => ['title', 'thumbnail'],
        'menu_icon' => 'dashicons-calendar-alt',
        'show_in_rest' => false,
    ]);
}
add_action('init', 'oor_register_event_post_type');
```

---

## 💡 Важные замечания

### Санитизация данных

**Всегда используйте функции санитизации:**
- `esc_html()` - для текста
- `esc_attr()` - для атрибутов
- `esc_url()` - для URL
- `wp_kses()` - для HTML (если разрешен)

### Изображения

**Используйте правильные функции WordPress:**
```php
// Вместо прямого вывода URL
$image_url = get_field('image')['url'];

// Используйте wp_get_attachment_image
echo wp_get_attachment_image($image_id, 'full', false, [
    'loading' => 'lazy',
    'decoding' => 'async'
]);
```

### Repeater поля

**Всегда проверяйте наличие данных:**
```php
if (have_rows('repeater_field')):
    while (have_rows('repeater_field')): the_row();
        $value = get_sub_field('sub_field');
        // ...
    endwhile;
endif;
```

---

**Эти примеры служат основой для создания реальных шаблонов. Адаптируйте их под ваши конкретные потребности.**
