<?php
/**
 * Template for displaying single artist post
 * Шаблон для отображения страницы артиста
 */

get_header();

// Проверка, что это действительно артист
if (!have_posts()) {
    // Если пост не найден, редирект на страницу артистов
    wp_redirect(home_url('/artists'), 302);
    exit;
}

the_post();

// Проверка, что у артиста есть название
if (empty(get_the_title())) {
    // Если нет названия, редирект на страницу артистов
    wp_redirect(home_url('/artists'), 302);
    exit;
}
?>

<!-- Breadcrumbs -->
    <div class="oor-artist-breadcrumbs">
        <a href="<?php echo esc_url(home_url('/artists')); ?>" class="oor-artist-breadcrumb-link rolling-button"><span class="tn-atom">артисты</span></a>
        <span class="oor-artist-breadcrumb-dot"></span>
        <span class="oor-artist-breadcrumb-current"><?php echo esc_html(get_post_field('post_name', get_the_ID())); ?></span>
    </div>

    <!-- Main Content -->
    <main class="oor-artist-main">
        <!-- Left - Description with gradient background -->
        <div class="oor-artist-description-container">
            <h1 class="oor-artist-title"><?php echo esc_html(get_the_title()); ?></h1>
            <div class="oor-artist-description-wrapper">
                <div class="oor-artist-description-content" id="artist-description">
                    <?php
                    // Краткое описание
                    $short_description = get_field('artist_short_description');
                    if ($short_description) {
                        echo '<p>' . wp_kses_post($short_description) . '</p>';
                    }
                    
                    // Полное описание
                    $full_description = get_field('artist_full_description');
                    if ($full_description) {
                        // Разбиваем на параграфы, если есть переносы строк
                        $paragraphs = explode("\n", $full_description);
                        foreach ($paragraphs as $paragraph) {
                            $paragraph = trim($paragraph);
                            if ($paragraph) {
                                echo '<p class="oor-artist-description-expanded">' . wp_kses_post($paragraph) . '</p>';
                            }
                        }
                    }
                    ?>
                </div>
                <button class="oor-artist-description-toggle" id="description-toggle">подробнее</button>
            </div>

            <!-- Social Links -->
            <div class="oor-artist-social-links">
                <?php
                // Получаем социальные сети из ACF Repeater
                $social_links = get_field('artist_social_links');
                if ($social_links && is_array($social_links)) {
                    foreach ($social_links as $social) {
                        $platform = isset($social['platform']) ? $social['platform'] : '';
                        $url = isset($social['url']) ? $social['url'] : '#';
                        if ($platform && $url) {
                            echo '<a href="' . esc_url($url) . '" class="oor-artist-social-link rolling-button" target="_blank" rel="noopener noreferrer"><span class="tn-atom">' . esc_html($platform) . '</span></a>';
                        }
                    }
                }
                ?>
            </div>
        </div>

        <!-- Center - Artist Image (full height, pinned to bottom) -->
        <div class="oor-artist-image-container">
            <?php
            // Получаем изображение артиста из ACF
            $artist_image = get_field('artist_main_image');
            $artist_slug = get_post_field('post_name', get_the_ID());
            
            $image_id = null;
            $image_url = null;
            
            if ($artist_image) {
                // Обрабатываем разные форматы возврата ACF Image field
                if (is_numeric($artist_image)) {
                    // Если это ID вложения
                    $image_id = $artist_image;
                } elseif (is_array($artist_image) && isset($artist_image['ID'])) {
                    // Если это массив от ACF
                    $image_id = $artist_image['ID'];
                } elseif (is_array($artist_image) && isset($artist_image['url'])) {
                    // Если это массив с URL
                    $image_url = $artist_image['url'];
                }
            }
            
            // Если нет изображения из ACF, используем fallback из статичной папки
            if (!$image_id && !$image_url) {
                $fallback_base = get_template_directory_uri() . '/public/assets/artists/' . $artist_slug . '/main';
                // Проверяем существование файлов (используем PNG как основной fallback)
                $image_url = $fallback_base . '.png';
            }
            
            if ($image_id) {
                // Используем функцию для генерации picture элемента с вариантами 1x/2x
                echo oor_picture_element($image_id, get_the_title(), 'oor-artist-image-main no-parallax');
            } elseif ($image_url) {
                // Fallback: простое изображение из статичной папки
                $fallback_base = get_template_directory_uri() . '/public/assets/artists/' . $artist_slug . '/main';
                ?>
                <picture>
                    <source srcset="<?php echo esc_url($fallback_base . '.avif'); ?> 1x, <?php echo esc_url($fallback_base . '@2x.avif'); ?> 2x" type="image/avif">
                    <source srcset="<?php echo esc_url($fallback_base . '.webp'); ?> 1x, <?php echo esc_url($fallback_base . '@2x.webp'); ?> 2x" type="image/webp">
                    <img src="<?php echo esc_url($fallback_base . '.png'); ?>" 
                         srcset="<?php echo esc_url($fallback_base . '.png'); ?> 1x, <?php echo esc_url($fallback_base . '@2x.png'); ?> 2x" 
                         alt="<?php echo esc_attr(get_the_title()); ?>" 
                         class="oor-artist-image-main no-parallax">
                </picture>
                <?php
            } else {
                // Последний fallback: placeholder
                echo '<img src="' . esc_url(get_template_directory_uri() . '/public/assets/placeholder-artist.png') . '" alt="' . esc_attr(get_the_title()) . '" class="oor-artist-image-main no-parallax">';
            }
            ?>
        </div>

        <!-- Right Side - Tracks Grid (3 columns) -->
        <div class="oor-artist-tracks-container">
            <div class="oor-artist-tracks-grid">
                <?php
                // Получаем треки из ACF Repeater (поле называется 'tracks')
                $tracks = get_field('tracks');
                $artist_slug = get_post_field('post_name', get_the_ID());
                $tracks_base_path = get_template_directory_uri() . '/public/assets/artists/' . $artist_slug . '/tracks';
                
                // Если треки из ACF не заполнены, используем статичные пути из папки
                if (!$tracks || !is_array($tracks) || empty($tracks)) {
                    // Fallback: используем статичные треки из папки (если они есть)
                    // Это временное решение, пока треки не будут заполнены в ACF
                    $tracks = [];
                }
                
                // Выводим треки из ACF
                if ($tracks && is_array($tracks)) {
                    $track_index = 1;
                    foreach ($tracks as $track) {
                        // Получаем данные трека из ACF
                        // Поля: track_name, track_mp3, track_cover, track_year (если есть)
                        $track_name = isset($track['track_name']) ? $track['track_name'] : '';
                        $track_year = isset($track['track_year']) ? $track['track_year'] : '';
                        $track_audio = isset($track['track_mp3']) ? $track['track_mp3'] : (isset($track['track_audio']) ? $track['track_audio'] : ''); // Поддержка обоих вариантов
                        $track_cover = isset($track['track_cover']) ? $track['track_cover'] : null;
                        
                        if (!$track_name) {
                            continue; // Пропускаем треки без названия
                        }
                        
                        // Обрабатываем аудио файл
                        $audio_url = '';
                        if (is_numeric($track_audio)) {
                            $audio_url = wp_get_attachment_url($track_audio);
                        } elseif (is_array($track_audio) && isset($track_audio['url'])) {
                            $audio_url = $track_audio['url'];
                        } elseif (is_string($track_audio)) {
                            $audio_url = $track_audio;
                        }
                        
                        // Обрабатываем обложку трека
                        $cover_id = null;
                        if (is_numeric($track_cover)) {
                            $cover_id = $track_cover;
                        } elseif (is_array($track_cover) && isset($track_cover['ID'])) {
                            $cover_id = $track_cover['ID'];
                        }
                        ?>
                        <div class="oor-artist-track" data-track-id="<?php echo $track_index; ?>" data-track-src="<?php echo esc_url($audio_url); ?>">
                            <div class="oor-artist-track-cover">
                                <?php
                                if ($cover_id) {
                                    // Используем функцию для генерации picture элемента
                                    echo oor_picture_element($cover_id, $track_name, 'oor-artist-track-image no-parallax');
                                } else {
                                    // Fallback: простое изображение
                                    $cover_url = is_array($track_cover) && isset($track_cover['url']) ? $track_cover['url'] : '';
                                    if ($cover_url) {
                                        echo '<img src="' . esc_url($cover_url) . '" alt="' . esc_attr($track_name) . '" class="oor-artist-track-image no-parallax">';
                                    }
                                }
                                ?>
                                <div class="oor-artist-track-overlay">
                                    <svg class="oor-artist-track-progress" width="180" height="180" viewBox="0 0 180 180">
                                        <circle class="oor-artist-track-progress-bg" cx="90" cy="90" r="85" fill="none" stroke="rgba(255, 255, 255, 0.3)" stroke-width="2"/>
                                        <circle class="oor-artist-track-progress-fill" cx="90" cy="90" r="85" fill="none" stroke="#000" stroke-width="2" stroke-dasharray="534.07" stroke-dashoffset="534.07" transform="rotate(-90 90 90)"/>
                                    </svg>
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/artist-page/play-track.svg" alt="Play" class="oor-artist-track-play-icon">
                                </div>
                            </div>
                            <div class="oor-artist-track-info">
                                <span class="oor-artist-track-name"><?php echo esc_html($track_name); ?></span>
                                <span class="oor-artist-track-year"><?php echo esc_html($track_year); ?></span>
                            </div>
                        </div>
                        <?php
                        $track_index++;
                    }
                }
                
                // Если треков из ACF нет, используем fallback на статичные файлы из папки
                if (empty($tracks)) {
                    // Получаем список треков из папки public/assets/artists/{slug}/tracks/
                    $tracks_dir = get_template_directory() . '/public/assets/artists/' . $artist_slug . '/tracks';
                    $static_tracks = [];
                    
                    if (is_dir($tracks_dir)) {
                        $track_dirs = array_filter(glob($tracks_dir . '/*'), 'is_dir');
                        foreach ($track_dirs as $track_dir) {
                            $track_slug = basename($track_dir);
                            $audio_file = $track_dir . '/audio.mp3';
                            
                            if (file_exists($audio_file)) {
                                // Пытаемся получить название трека из имени папки или файла
                                $track_name = ucfirst(str_replace(['-', '_'], ' ', $track_slug));
                                $static_tracks[] = [
                                    'track_name' => $track_name,
                                    'track_year' => '', // Год неизвестен для статичных треков
                                    'track_audio' => get_template_directory_uri() . '/public/assets/artists/' . $artist_slug . '/tracks/' . $track_slug . '/audio.mp3',
                                    'track_cover' => null, // Обложка будет из статичной папки
                                    'track_slug' => $track_slug
                                ];
                            }
                        }
                    }
                    
                    // Выводим статичные треки
                    if (!empty($static_tracks)) {
                        $track_index = 1;
                        foreach ($static_tracks as $track) {
                            $track_slug = $track['track_slug'];
                            $cover_base = get_template_directory_uri() . '/public/assets/artists/' . $artist_slug . '/tracks/' . $track_slug . '/cover';
                            ?>
                            <div class="oor-artist-track" data-track-id="<?php echo $track_index; ?>" data-track-src="<?php echo esc_url($track['track_audio']); ?>">
                                <div class="oor-artist-track-cover">
                                    <picture>
                                        <source srcset="<?php echo esc_url($cover_base . '.avif'); ?> 1x, <?php echo esc_url($cover_base . '@2x.avif'); ?> 2x" type="image/avif">
                                        <source srcset="<?php echo esc_url($cover_base . '.webp'); ?> 1x, <?php echo esc_url($cover_base . '@2x.webp'); ?> 2x" type="image/webp">
                                        <img src="<?php echo esc_url($cover_base . '.png'); ?>" 
                                             srcset="<?php echo esc_url($cover_base . '.png'); ?> 1x, <?php echo esc_url($cover_base . '@2x.png'); ?> 2x" 
                                             alt="<?php echo esc_attr($track['track_name']); ?>" 
                                             class="oor-artist-track-image no-parallax">
                                    </picture>
                                    <div class="oor-artist-track-overlay">
                                        <svg class="oor-artist-track-progress" width="180" height="180" viewBox="0 0 180 180">
                                            <circle class="oor-artist-track-progress-bg" cx="90" cy="90" r="85" fill="none" stroke="rgba(255, 255, 255, 0.3)" stroke-width="2"/>
                                            <circle class="oor-artist-track-progress-fill" cx="90" cy="90" r="85" fill="none" stroke="#000" stroke-width="2" stroke-dasharray="534.07" stroke-dashoffset="534.07" transform="rotate(-90 90 90)"/>
                                        </svg>
                                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/artist-page/play-track.svg" alt="Play" class="oor-artist-track-play-icon">
                                    </div>
                                </div>
                                <div class="oor-artist-track-info">
                                    <span class="oor-artist-track-name"><?php echo esc_html($track['track_name']); ?></span>
                                    <span class="oor-artist-track-year"><?php echo esc_html($track['track_year']); ?></span>
                                </div>
                            </div>
                            <?php
                            $track_index++;
                        }
                    }
                }
                ?>
            </div>
        </div>
    </main>

    <!-- Player -->
    <div class="oor-artist-player">
        <div class="oor-artist-player-left">
            <div class="oor-artist-player-controls">
                <button class="oor-artist-player-btn" id="player-prev">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/artist-page/prev-icon.svg" alt="Previous" width="24" height="24">
                </button>
                <button class="oor-artist-player-btn oor-artist-player-btn-play" id="player-play-pause">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/artist-page/play-icon.svg" alt="Play" width="24" height="24" class="oor-artist-player-play-icon">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/artist-page/pause-icon.svg" alt="Pause" width="24" height="24" class="oor-artist-player-pause-icon">
                </button>
                <button class="oor-artist-player-btn" id="player-next">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/artist-page/next-icon.svg" alt="Next" width="24" height="24">
                </button>
            </div>
            <div class="oor-artist-player-track-name" id="player-track-name">Бьется</div>
        </div>
        <div class="oor-artist-player-center">
            <div class="oor-artist-player-progress-bar">
                <div class="oor-artist-player-progress-fill" id="player-progress"></div>
                <div class="oor-artist-player-progress-handle" id="player-handle"></div>
            </div>
        </div>
        <div class="oor-artist-player-right">
            <div class="oor-artist-player-time" id="player-time">00:00</div>
            <div class="oor-artist-player-volume">
                <button class="oor-artist-player-volume-btn" id="player-volume-btn">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/artist-page/volume-icon.svg" alt="Volume" width="24" height="24">
                </button>
                <div class="oor-artist-player-volume-bar-wrapper">
                    <div class="oor-artist-player-volume-bar">
                        <div class="oor-artist-player-volume-fill" id="player-volume-fill"></div>
                        <div class="oor-artist-player-volume-handle" id="player-volume-handle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- OOR JavaScript -->
    <!-- External deps (load first) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    
    <!-- UI Scaling для больших мониторов (загружаем рано) -->
    <script src="/src/js/scale-container.js?v=20260115223720"></script>
    
    <!-- OOR JavaScript модули -->
    <script src="/src/js/modules/error-handler.js?v=20260115223720"></script>
    <script src="/src/js/modules/preloader.js?v=20260115223720"></script>
    <script src="/src/js/modules/navigation.js?v=20260115223720"></script>
    <script src="/src/js/mobile-menu.js?v=20260115223720"></script>
    <script src="/src/js/menu-sync.js?v=20260115223720"></script>
    <script src="/src/js/main.js?v=20260115223720"></script>
    
    <!-- Rolling text effect script -->
    <script defer src="/src/js/rolling-text.js?v=20260115223720"></script>
    
    <!-- MouseFollower + Custom cursor (объединенный) -->
    <script src="/src/js/cursor.js?v=20260115223720"></script>
    
    <!-- Кастомный декоративный скроллбар -->
    <div class="custom-scrollbar" id="customScrollbar">
      <div class="custom-scrollbar-track">
        <div class="custom-scrollbar-thumb" id="scrollbarThumb"></div>
    </div>
  </div>
    
    <!-- Custom scrollbar script -->
    <script src="/src/js/scrollbar.js?v=20260115223720"></script>

    <!-- Artist page script -->
    <script src="/src/js/artist-page.js?v=20260115223720"></script>

<?php
get_footer();
?>
