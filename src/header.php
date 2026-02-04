<!DOCTYPE html>
<html <?php language_attributes(); ?> class="preloader-active">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/svg+xml" href="<?php echo esc_url(get_template_directory_uri() . '/public/assets/logo.svg'); ?>">
    <link rel="alternate icon" href="<?php echo esc_url(get_template_directory_uri() . '/public/assets/logo.svg'); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class('preloader-active'); ?>>
    <?php wp_body_open(); ?>
    
    <!-- Preloader -->
    <div id="preloader" class="oor-preloader">
        <div class="oor-preloader-progress-bar" id="preloader-progress-bar"></div>
        <div class="oor-preloader-content">
        </div>
    </div>
    
    <!-- Splash Screen -->
    <div id="splash-screen" class="oor-splash-screen">
        <?php 
        $splash_gif_path = get_template_directory() . '/public/assets/splash.gif';
        $splash_gif_url = get_template_directory_uri() . '/public/assets/splash.gif';
        // Проверяем существование файла, если нет - используем fallback
        if (!file_exists($splash_gif_path)) {
            $splash_gif_url = get_template_directory_uri() . '/public/assets/splash-last-frame.png';
        }
        ?>
        <img id="splash-gif" class="oor-splash-gif" 
             src="<?php echo esc_url($splash_gif_url); ?>" 
             alt="Splash" 
             width="400" 
             height="400"
             loading="eager"
             fetchpriority="high"
             onerror="this.onerror=null; this.src='<?php echo esc_url(get_template_directory_uri() . '/public/assets/splash-last-frame.png'); ?>';">
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
                    // Меню навигации
                    $menu_items = [
                        ['url' => home_url('/'), 'text' => 'Main', 'slug' => 'main'],
                        ['url' => home_url('/manifest'), 'text' => 'Манифест', 'slug' => 'manifest'],
                        ['url' => home_url('/artists'), 'text' => 'Артисты', 'slug' => 'artists'],
                        ['url' => home_url('/studio'), 'text' => 'Студия', 'slug' => 'studio'],
                        ['url' => home_url('/services'), 'text' => 'Услуги', 'slug' => 'services'],
                        ['url' => '#', 'text' => 'DAWGS', 'slug' => 'dawgs'],
                        ['url' => home_url('/talk-show'), 'text' => 'Talk-шоу', 'slug' => 'talk-show'],
                        ['url' => home_url('/events'), 'text' => 'События', 'slug' => 'events'],
                        ['url' => home_url('/merch'), 'text' => 'Мерч', 'slug' => 'merch'],
                        ['url' => home_url('/contacts'), 'text' => 'Контакты', 'slug' => 'contacts'],
                    ];
                    
                    foreach ($menu_items as $index => $item) {
                        $active = (is_page($item['slug']) || 
                                  (is_front_page() && $item['slug'] === 'main') ||
                                  (is_post_type_archive('artist') && $item['slug'] === 'artists') ||
                                  (is_post_type_archive('event') && $item['slug'] === 'events')) 
                                  ? 'oor-nav-link--active' : '';
                        
                        echo sprintf(
                            '<div class="oor-nav-item">' .
                            '<a href="%s" class="oor-nav-link rolling-button %s" data-menu-item="%s">' .
                            '<span class="tn-atom">%s</span></a>%s' .
                            '</div>',
                            esc_url($item['url']),
                            esc_attr($active),
                            esc_attr($item['slug']),
                            esc_html($item['text']),
                            ($index < count($menu_items) - 1) ? '<span>/</span>' : ''
                        );
                    }
                    ?>
                </div>
            </nav>
            
            <button class="oor-btn-small" data-action="become-artist">
                <span class="oor-btn-small-text">Стать артистом</span>
                <span class="oor-btn-small-icon">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-small.svg" 
                         alt="" width="17" height="17">
                </span>
            </button>
            
            <!-- Бургер-меню для мобильных устройств -->
            <button class="oor-burger-menu" id="burger-menu" aria-label="Открыть меню">
                <img src="<?php echo get_template_directory_uri(); ?>/public/assets/burger-icon.svg" 
                     alt="" 
                     class="oor-burger-icon" 
                     width="24" 
                     height="24">
            </button>
        </div>
        
        <!-- Мобильное меню (сайдбар) -->
        <div class="oor-mobile-menu" id="mobile-menu">
            <button id="close-menu" aria-label="Закрыть меню">
                <img src="<?php echo get_template_directory_uri(); ?>/public/assets/close-icon.svg" 
                     alt="" 
                     width="24" 
                     height="24">
            </button>
            <div class="oor-mobile-menu-content">
                <!-- Основные пункты меню -->
                <div class="oor-mobile-menu-main">
                    <?php
                    foreach ($menu_items as $item) {
                        echo sprintf(
                            '<a href="%s" class="oor-mobile-menu-link rolling-button" data-menu-item="%s">%s</a>',
                            esc_url($item['url']),
                            esc_attr($item['slug']),
                            esc_html($item['text'])
                        );
                    }
                    ?>
                </div>
                
                <!-- Кнопка "Стать артистом" -->
                <div class="oor-mobile-menu-bottom">
                    <button class="oor-mobile-menu-btn" data-action="become-artist">
                        <span class="oor-mobile-menu-btn-text">Стать артистом</span>
                        <span class="oor-mobile-menu-btn-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-small.svg" 
                                 alt="" 
                                 width="17" 
                                 height="17">
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    <main id="main-content">
