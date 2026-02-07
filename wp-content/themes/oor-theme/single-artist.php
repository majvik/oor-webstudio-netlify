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
                    $post_id = get_the_ID();
                    $short_description = get_field('artist_short_description', $post_id) ?: get_field('short_description', $post_id);
                    $full_description = get_field('artist_full_description', $post_id) ?: get_field('full_description', $post_id);
                    // ACF может вернуть HTML (WYSIWYG). Сохраняем границы абзацев (</p><p> → перенос), затем убираем теги — чтобы не вставлять <p> внутрь наших <p>.
                    $full_plain = $full_description ? $full_description : '';
                    $full_plain = preg_replace('#\s*</p>\s*<p[^>]*>\s*#i', "\n", $full_plain);
                    $full_plain = wp_strip_all_tags($full_plain);
                    $full_plain = preg_replace('/[\r\n]+/', "\n", $full_plain);
                    $full_paragraphs = $full_plain !== '' ? array_filter(array_map('trim', explode("\n", $full_plain))) : [];

                    if ($short_description) {
                        echo '<p class="oor-artist-description-short">' . esc_html($short_description) . '</p>';
                        $expanded_paragraphs = $full_paragraphs;
                    } elseif (!empty($full_paragraphs)) {
                        echo '<p class="oor-artist-description-short">' . esc_html(reset($full_paragraphs)) . '</p>';
                        $expanded_paragraphs = array_slice($full_paragraphs, 1);
                    } else {
                        $expanded_paragraphs = [];
                    }

                    foreach ($expanded_paragraphs as $paragraph) {
                        if ($paragraph !== '') {
                            echo '<p class="oor-artist-description-expanded" style="display:none">' . esc_html($paragraph) . '</p>';
                        }
                    }
                    $has_expanded = !empty($expanded_paragraphs);
                    ?>
                </div>
                <?php if ($has_expanded) : ?>
                <button type="button" class="oor-artist-description-toggle" id="description-toggle" aria-label="подробнее">подробнее</button>
                <?php endif; ?>
            </div>

            <!-- Social Links -->
            <div class="oor-artist-social-links">
                <?php
                $social_links = get_field('artist_social_links', $post_id) ?: get_field('social_links', $post_id);
                if ($social_links && is_array($social_links)) {
                    foreach ($social_links as $social) {
                        $platform = isset($social['platform']) ? $social['platform'] : (isset($social['name']) ? $social['name'] : '');
                        $url = isset($social['url']) ? $social['url'] : (isset($social['link']) ? $social['link'] : '#');
                        if ($platform && $url && $url !== '#') {
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
            // Получаем изображение артиста из ACF (как на странице всех артистов: artist_image / artist_main_image)
            $artist_slug = get_post_field('post_name', get_the_ID());
            $artist_image = get_field('artist_main_image');
            if (!$artist_image) {
                $artist_image = get_field('artist_image', get_the_ID());
            }
            if (!$artist_image && has_post_thumbnail(get_the_ID())) {
                $artist_image = get_post_thumbnail_id(get_the_ID());
            }
            
            $image_id = null;
            $image_url = null;
            $image_url_from_acf = false;
            
            if ($artist_image) {
                if (is_numeric($artist_image)) {
                    $image_id = (int) $artist_image;
                } elseif (is_array($artist_image)) {
                    if (isset($artist_image['ID'])) {
                        $image_id = (int) $artist_image['ID'];
                    } elseif (isset($artist_image['id'])) {
                        $image_id = (int) $artist_image['id'];
                    }
                    if (!$image_id && isset($artist_image['url'])) {
                        $image_url = function_exists('oor_fix_canonical_url') ? oor_fix_canonical_url($artist_image['url']) : $artist_image['url'];
                        $image_url_from_acf = true;
                    }
                }
            }
            
            // Если нет изображения из ACF/миниатюры — fallback из статичной папки темы
            if (!$image_id && !$image_url) {
                $fallback_base = oor_theme_base_uri() . '/public/assets/artists/' . $artist_slug . '/main';
                $image_url = $fallback_base . '.png';
            }
            
            if ($image_id) {
                // Картинка из ACF/медиабиблиотеки — используем wp_get_attachment_image (URL уже через фильтры), чтобы гарантированно отображалось
                $img = wp_get_attachment_image($image_id, 'full', false, ['class' => 'oor-artist-image-main no-parallax', 'alt' => esc_attr(get_the_title())]);
                if ($img) {
                    echo $img;
                } else {
                    echo oor_picture_element($image_id, get_the_title(), 'oor-artist-image-main no-parallax');
                }
            } elseif ($image_url_from_acf && $image_url) {
                // ACF вернул только URL (без ID) — одна картинка с правильным путём
                echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr(get_the_title()) . '" class="oor-artist-image-main no-parallax">';
            } elseif ($image_url) {
                // Fallback: только существующие файлы из папки артиста (без 404)
                $fallback_html = function_exists('oor_artist_fallback_picture') ? oor_artist_fallback_picture($artist_slug, 'main', 'oor-artist-image-main no-parallax', get_the_title()) : '';
                if ($fallback_html) {
                    echo $fallback_html;
                } else {
                    echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr(get_the_title()) . '" class="oor-artist-image-main no-parallax">';
                }
            } else {
                // Последний fallback: placeholder
                echo '<img src="' . esc_url(oor_theme_base_uri() . '/public/assets/placeholder-artist.png') . '" alt="' . esc_attr(get_the_title()) . '" class="oor-artist-image-main no-parallax">';
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
                $tracks_base_path = oor_theme_base_uri() . '/public/assets/artists/' . $artist_slug . '/tracks';
                
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
                                    <img src="<?php echo esc_url(oor_theme_base_uri() . '/public/assets/artist-page/play-track.svg'); ?>" alt="Play" class="oor-artist-track-play-icon">
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
                                    'track_audio' => oor_theme_base_uri() . '/public/assets/artists/' . $artist_slug . '/tracks/' . $track_slug . '/audio.mp3',
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
                            $cover_base = oor_theme_base_uri() . '/public/assets/artists/' . $artist_slug . '/tracks/' . $track_slug . '/cover';
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
                                        <img src="<?php echo esc_url(oor_theme_base_uri() . '/public/assets/artist-page/play-track.svg'); ?>" alt="Play" class="oor-artist-track-play-icon">
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
                    <img src="<?php echo esc_url(oor_theme_base_uri() . '/public/assets/artist-page/prev-icon.svg'); ?>" alt="Previous" width="24" height="24">
                </button>
                <button class="oor-artist-player-btn oor-artist-player-btn-play" id="player-play-pause">
                    <img src="<?php echo esc_url(oor_theme_base_uri() . '/public/assets/artist-page/play-icon.svg'); ?>" alt="Play" width="24" height="24" class="oor-artist-player-play-icon">
                    <img src="<?php echo esc_url(oor_theme_base_uri() . '/public/assets/artist-page/pause-icon.svg'); ?>" alt="Pause" width="24" height="24" class="oor-artist-player-pause-icon">
                </button>
                <button class="oor-artist-player-btn" id="player-next">
                    <img src="<?php echo esc_url(oor_theme_base_uri() . '/public/assets/artist-page/next-icon.svg'); ?>" alt="Next" width="24" height="24">
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
                    <img src="<?php echo esc_url(oor_theme_base_uri() . '/public/assets/artist-page/volume-icon.svg'); ?>" alt="Volume" width="24" height="24">
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

    <!-- Скрипты темы подключаются через wp_enqueue_scripts (footer.php вызывает wp_footer()). -->

<?php
get_footer();
?>
