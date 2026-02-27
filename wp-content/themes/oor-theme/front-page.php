<?php
/**
 * Template Name: Главная
 * Главная страница сайта
 */

get_header();
?>

<!-- HERO Section -->
    <section class="oor-section-hero">
        <div class="oor-container">
            <div class="oor-grid oor-hero-title-block">
                <div class="oor-col-10">
                    <h1 class="oor-hero-title">Out of records</h1>
        </div>
                <div class="oor-col-1"></div>
                <div class="oor-col-1">
      </div>
    </div>
    </div>
        
        <div class="oor-hero-main">
            <?php
            // Получаем Hero видео из ACF
            $hero_background_video = get_field('hero_background_video');
            $hero_modal_video = get_field('hero_modal_video');
            
            // Функция для получения URL из ACF поля (поддержка массива и ID)
            $get_video_url = function($field_value) {
                if (!$field_value) return false;
                if (is_array($field_value)) {
                    // ACF File field возвращает массив с 'url' или 'ID'
                    if (isset($field_value['url'])) {
                        return $field_value['url'];
                    }
                    if (isset($field_value['ID'])) {
                        return wp_get_attachment_url($field_value['ID']);
                    }
                    return false;
                }
                // Если это число (ID вложения)
                if (is_numeric($field_value)) {
                    return wp_get_attachment_url($field_value);
                }
                // Если это уже URL (строка)
                if (is_string($field_value) && filter_var($field_value, FILTER_VALIDATE_URL)) {
                    return $field_value;
                }
                return false;
            };
            
            // Получаем URL видео
            $bg_video_url = $get_video_url($hero_background_video);
            $modal_video_url = $get_video_url($hero_modal_video);
            
            // Fallback на статичные файлы, если ACF поля не заполнены
            $bg_video_webm = $bg_video_url ? $bg_video_url : get_template_directory_uri() . '/public/assets/OUTOFREC_reel_v4_nologo.webm';
            $bg_video_mp4 = $bg_video_url ? $bg_video_url : get_template_directory_uri() . '/public/assets/OUTOFREC_reel_v4_nologo.mp4';
            $modal_video = $modal_video_url ? $modal_video_url : get_template_directory_uri() . '/public/assets/OUTOFREC_reel_v4_nologo.mp4';
            ?>
            <!-- Hero Video фон -->
            <video class="oor-hero-video" autoplay muted loop playsinline preload="metadata" poster="<?php echo get_template_directory_uri(); ?>/public/assets/video-cover.avif">
                <source src="<?php echo esc_url($bg_video_webm); ?>" type="video/webm">
                <source src="<?php echo esc_url($bg_video_mp4); ?>" type="video/mp4">
                <!-- Fallback для браузеров без поддержки видео -->
                <div class="oor-hero-video-fallback"></div>
            </video>
            
            <!-- Кликабельный оверлей для открытия полноэкранного видео -->
            <div class="oor-hero-video-overlay video-cuberto-cursor-1" id="hero-video-overlay" data-cursor-video="<?php echo esc_url($modal_video); ?>"></div>
            
            <!-- Plus иконки позиционируются относительно секции -->
            <div class="oor-hero-plus-top-left">
                <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="17" height="17" class="oor-media-cover">
            </div>
            <div class="oor-hero-plus-top-right">
                <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="17" height="17" class="oor-media-cover">
            </div>
            <div class="oor-hero-plus-bottom-left">
                <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="17" height="17" class="oor-media-cover">
      </div>
            <div class="oor-hero-plus-bottom-right">
                <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="17" height="17" class="oor-media-cover">
    </div>
            
            <div class="oor-container">
                <div class="oor-grid oor-hero-content">
                    <!-- Hero Text -->
                    <div class="oor-col-12 oor-hero-text">
                        <!-- Description -->
                        <div class="oor-hero-description">
                            <div class="oor-grid oor-hero-description-grid">
                                <div class="oor-col-6 oor-hero-description-title-wrapper">
                                    <h2 class="oor-hero-description-title">FEELING THE MUSIC</h2>
    </div>
                                <div class="oor-col-6 oor-social-icons">
                                    <?php
                                    $hero_telegram = function_exists('get_field') ? get_field('hero_telegram_url') : '';
                                    $hero_instagram = function_exists('get_field') ? get_field('hero_instagram_url') : '';
                                    $hero_telegram = is_string($hero_telegram) ? trim($hero_telegram) : '';
                                    $hero_instagram = is_string($hero_instagram) ? trim($hero_instagram) : '';
                                    $hero_telegram_url = $hero_telegram !== '' ? $hero_telegram : '#';
                                    $hero_instagram_url = $hero_instagram !== '' ? $hero_instagram : '#';
                                    ?>
                                    <a href="<?php echo esc_url($hero_telegram_url); ?>" class="oor-social-icon" target="_blank" rel="noopener noreferrer" aria-label="Telegram">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 2C17.523 2 22 6.47696 22 12C22 17.523 17.523 22 12 22C6.47696 22 2 17.523 2 12C2 6.47696 6.47696 2 12 2ZM15.4496 16.0761C15.6335 15.5117 16.4952 9.88739 16.6017 8.77913C16.6339 8.44348 16.5278 8.22043 16.32 8.12087C16.0687 8 15.6965 8.06043 15.2648 8.21609C14.6726 8.42957 7.10217 11.6439 6.66478 11.83C6.25 12.0061 5.85783 12.1983 5.85783 12.4765C5.85783 12.6722 5.97391 12.7822 6.29391 12.8965C6.62696 13.0152 7.46565 13.2696 7.96087 13.4061C8.43783 13.5378 8.98087 13.4235 9.28522 13.2343C9.60783 13.0339 13.3309 10.5426 13.5983 10.3243C13.8652 10.1061 14.0783 10.3857 13.86 10.6043C13.6417 10.8226 11.0861 13.303 10.7491 13.6465C10.34 14.0635 10.6304 14.4957 10.9048 14.6687C11.2183 14.8661 13.4726 16.3783 13.8122 16.6209C14.1517 16.8635 14.4961 16.9735 14.8113 16.9735C15.1265 16.9735 15.2926 16.5583 15.4496 16.0761Z" fill="white"/>
                                        </svg>
                                    </a>
                                    <a href="<?php echo esc_url($hero_instagram_url); ?>" class="oor-social-icon" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M7.99834 2.40002C4.91114 2.40002 2.3999 4.91359 2.3999 8.00159V16.0016C2.3999 19.0888 4.91347 21.6 8.00146 21.6H16.0015C19.0887 21.6 21.5999 19.0865 21.5999 15.9985V7.99846C21.5999 4.91126 19.0863 2.40002 15.9983 2.40002H7.99834ZM17.5999 5.60002C18.0415 5.60002 18.3999 5.95842 18.3999 6.40002C18.3999 6.84162 18.0415 7.20002 17.5999 7.20002C17.1583 7.20002 16.7999 6.84162 16.7999 6.40002C16.7999 5.95842 17.1583 5.60002 17.5999 5.60002ZM11.9999 7.20002C14.6471 7.20002 16.7999 9.35282 16.7999 12C16.7999 14.6472 14.6471 16.8 11.9999 16.8C9.3527 16.8 7.1999 14.6472 7.1999 12C7.1999 9.35282 9.3527 7.20002 11.9999 7.20002ZM11.9999 8.80002C11.1512 8.80002 10.3373 9.13717 9.73716 9.73728C9.13704 10.3374 8.7999 11.1513 8.7999 12C8.7999 12.8487 9.13704 13.6626 9.73716 14.2628C10.3373 14.8629 11.1512 15.2 11.9999 15.2C12.8486 15.2 13.6625 14.8629 14.2626 14.2628C14.8628 13.6626 15.1999 12.8487 15.1999 12C15.1999 11.1513 14.8628 10.3374 14.2626 9.73728C13.6625 9.13717 12.8486 8.80002 11.9999 8.80002Z" fill="white"/>
                                        </svg>
                                    </a>
          </div>
        </div>
      </div>
                        
                        <!-- Benefits -->
                        <div class="oor-hero-benefits">
                            <span class="oor-benefit-item">Независимый лейбл</span>
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-small.svg" alt="" width="1" height="16" class="oor-media-cover">
                            <span class="oor-benefit-item">Студия</span>
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-small.svg" alt="" width="1" height="16" class="oor-media-cover">
                            <span class="oor-benefit-item">Дистрибьютор</span>
      </div>
    </div>
      </div>
          </div>
        </div>
    </section>

    <!-- Artists Slider Section -->
    <div id="wsls">
    <section class="slider-section">
          <!-- Desktop overlay column -->
          <div class="slider-overlay">
            <div class="overlay-inner">
              <div class="overlay-top">Артисты</div>
              <div class="overlay-center" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="2" viewBox="0 0 18 2" fill="none">
                  <g style="mix-blend-mode:difference">
                    <path d="M0 1L18 1" stroke="black"/>
            </g>
          </svg>
        </div>
              <a class="overlay-bottom rolling-button" href="<?php echo esc_url(home_url('/artists')); ?>"><span class="tn-atom">Все артисты</span></a>
      </div>
    </div>

          <!-- Mobile heading above slider -->
          <h3 class="slider-heading">Артисты</h3>

          <div class="slider">
            <div class="slider-track">
              <div class="slider-wrapper">
              <?php
              // Получаем артистов из ACF Repeater
              $artists_slider = get_field('artists_slider');
              
              if ($artists_slider && is_array($artists_slider)) {
                  foreach ($artists_slider as $item) {
                      // Получаем изображение для слайдера (отдельное поле)
                      $slider_image = isset($item['slider_image']) ? $item['slider_image'] : null;
                      
                      // Получаем артиста для ссылки
                      $artist = isset($item['artist']) ? $item['artist'] : null;
                      
                      // Если нет изображения или артиста, пропускаем
                      if (!$slider_image || !$artist) {
                          continue;
                      }
                      
                      // Обрабатываем артиста (Post Object)
                      $artist_id = is_numeric($artist) ? $artist : (is_object($artist) ? $artist->ID : null);
                      if (!$artist_id) {
                          continue;
                      }
                      
                      // Получаем имя артиста: сначала из ACF поля (если есть), затем из заголовка поста
                      $artist_name = isset($item['artist_name']) && !empty($item['artist_name']) 
                          ? $item['artist_name'] 
                          : get_the_title($artist_id);
                      
                      $artist_url = get_permalink($artist_id);
                      
                      // Обрабатываем изображение для слайдера
                      // Поддержка разных форматов возврата ACF Image field
                      $image_id = null;
                      $image_url = null;
                      $image_alt = esc_attr($artist_name);
                      
                      if (is_numeric($slider_image)) {
                          // Если это ID вложения
                          $image_id = $slider_image;
                          $image_url = wp_get_attachment_image_url($image_id, 'full');
                          $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $image_alt;
                      } elseif (is_array($slider_image)) {
                          // Если это массив от ACF
                          if (isset($slider_image['ID'])) {
                              $image_id = $slider_image['ID'];
                              $image_url = isset($slider_image['url']) ? $slider_image['url'] : wp_get_attachment_image_url($image_id, 'full');
                              $image_alt = isset($slider_image['alt']) ? $slider_image['alt'] : $image_alt;
                          } elseif (isset($slider_image['url'])) {
                              $image_url = $slider_image['url'];
                              $image_alt = isset($slider_image['alt']) ? $slider_image['alt'] : $image_alt;
                          }
                      } elseif (is_string($slider_image)) {
                          // Если это URL
                          $image_url = $slider_image;
                      }
                      
                      if (!$image_url) {
                          continue; // Пропускаем, если нет изображения
                      }
                      
                      // Используем функцию для генерации picture элемента с вариантами 1x/2x
                      if ($image_id) {
                          // Если есть ID, используем нашу функцию для генерации вариантов
                          $picture_html = oor_picture_element($image_id, $image_alt, 'slide-image', ['draggable' => 'false']);
                      } else {
                          // Fallback: простое изображение без вариантов
                          $picture_html = sprintf(
                              '<img src="%s" alt="%s" draggable="false">',
                              esc_url($image_url),
                              esc_attr($image_alt)
                          );
                      }
                      ?><div class="slide"><a href="<?php echo esc_url($artist_url); ?>" class="slide-media text-cuberto-cursor-2" data-text="Все артисты"><?php echo $picture_html; ?></a><span class="artist-name"><?php echo esc_html($artist_name); ?></span></div><?php
                  }
              }
              ?>
          </div>
        </div>
      </div>

          <!-- Mobile link below slider -->
          <a class="slider-all-link rolling-button" href="<?php echo esc_url(home_url('/artists')); ?>"><span class="tn-atom">Все артисты</span></a>
        </section>
    </div>

    <!-- Musical Association Section -->
    <section class="oor-musical-association">
        <div class="oor-grid">
            <!-- Левая колонка - 6 колонок -->
            <div class="oor-col-6 oor-musical-association-left">
                <div class="oor-section-number">[02]</div>
                
                <h2 class="oor-musical-association-title">
          Независимое музыкальное объединение, созданное во имя смелых идей
                </h2>
                
                <div class="oor-line-divider">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-large.svg" alt="" width="18" height="0">
            </div>
                
                <p class="oor-musical-association-text">
                    Действуем в обход пыльных правил — продвигаем артистов, которые отличаются, экспериментируют и рискуют. Работаем на 360° и по отдельным проектам. Хотим, чтобы талант звучал громче мейнстрима
                </p>
                
                <div class="oor-line-divider">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-large.svg" alt="" width="18" height="0">
        </div>
                
                <p class="oor-musical-association-text">
                    Основали лейбл, агентство дистрибуции и премиальную студию, а еще серию городских культурных событий и музыкальное talk-шоу
        </p>
                
                <div class="oor-line-divider">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-large.svg" alt="" width="18" height="0">
                </div>
      </div>
            
            <!-- Правая колонка - 6 колонок -->
            <div class="oor-col-6 oor-musical-association-right">
                <div class="oor-musical-association-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="156" height="567" viewBox="0 0 156 567" fill="none">
                        <g style="mix-blend-mode:difference">
                            <path d="M0.00935997 55.8977C0.00936175 15.1822 35.2141 -5.27972e-06 78.2286 -3.39949e-06C121.243 -1.51927e-06 156 15.1822 156 55.8978L156 128.25C156 168.956 121.234 184.147 78.2286 184.147C35.2234 184.147 0.00935502 168.956 0.0093568 128.25L0.00935997 55.8977ZM24.969 128.25C24.969 148.501 48.5943 154.198 78.0047 154.198C107.415 154.198 131.04 148.501 131.04 128.25L131.04 55.8978C131.04 35.646 107.191 29.9582 78.0047 29.9582C48.8183 29.9582 24.969 35.6548 24.969 55.8978L24.969 128.25Z" fill="white"/>
                            <path d="M0.00935125 255.229C0.00935303 214.523 35.2234 199.332 78.2286 199.332C121.234 199.332 156 214.514 156 255.229L156 327.581C156 368.288 121.234 383.479 78.2286 383.479C35.2234 383.479 0.00934631 368.288 0.00934809 327.581L0.00935125 255.229ZM24.969 327.581C24.969 347.833 48.5943 353.53 78.0047 353.53C107.415 353.53 131.04 347.833 131.04 327.581L131.04 255.229C131.04 234.978 107.191 229.29 78.0047 229.29C48.8182 229.29 24.969 234.986 24.969 255.229L24.969 327.581Z" fill="white"/>
                            <path d="M156 406.893L156 514.264C156 550.97 133.718 563.202 105.857 563.202C84.0136 563.202 66.192 553.505 59.5019 533.041L9.54792e-06 567L1.09864e-05 534.092L55.2657 503.931L55.2657 436.225L1.52643e-05 436.225L1.6546e-05 406.902L156 406.902L156 406.893ZM131.04 436.216L80.4493 436.216L80.4493 513.84C80.4493 529.022 92.262 534.092 105.633 534.092C119.004 534.092 131.04 529.031 131.04 513.84L131.04 436.216Z" fill="white"/>
            </g>
          </svg>
        </div>
                
                <!-- Plus Large иконка -->
                <div class="oor-musical-association-plus">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="17" height="17">
      </div>
    </div>
          </div>
    </section>

    <section class="oor-challenge-section">
        <div class="oor-challenge-left">
            <div class="oor-challenge-text">
                <h2 class="oor-challenge-title">
                    Бросить вызов привычному
                </h2>
                
                <a href="<?php echo esc_url( home_url( '/manifest' ) ); ?>" class="oor-manifesto-button rolling-button">
                    <span class="oor-manifesto-text tn-atom">Манифест</span>
                    <div class="oor-manifesto-icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/arrow-right.svg" alt="Arrow" width="32" height="32">
        </div>
                </a>
      </div>
            
            <div class="oor-without-fear-text">
                <p>
                    Музыка попадает в сердца благодаря таланту, а не метрикам. Мы помогаем реализоваться в музыке без оглядки на цифры и алгоритмы — и остаться собой без страха непопулярности
              </p>
        </div>
            
            <div class="oor-without-fear-images">
                <a href="<?php echo esc_url( home_url( '/become-artist' ) ); ?>" class="oor-without-fear-image text-cuberto-cursor-1" data-text="Стать артистом">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-1.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-1@2x.avif 2x" type="image/avif">
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-1.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-1@2x.webp 2x" type="image/webp">
                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-1.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-1.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-1@2x.png 2x" alt="Without Fear Of Unpopularity">
                    </picture>
            </a>
                
                <a href="<?php echo esc_url( home_url( '/become-artist' ) ); ?>" class="oor-without-fear-image-2 text-cuberto-cursor-1" data-text="Стать артистом">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-2.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-2@2x.avif 2x" type="image/avif">
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-2.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-2@2x.webp 2x" type="image/webp">
                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-2.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-2.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/without-fear-of-unpopularity-2@2x.png 2x" alt="Without Fear Of Unpopularity 2">
                    </picture>
                </a>
            </div>
          </div>
        
        <div class="oor-challenge-gap"></div>
        
        <div class="oor-challenge-right">
            <div class="oor-quality-strength">
                <div class="oor-challenge-section-number">[03]</div>
                
                <div class="oor-quality-line-divider">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-large.svg" alt="Line" width="18" height="2">
          </div>
        </div>
            
            <div class="oor-quality-content">
                <div class="oor-quality-title">
                    <h3>Возвращаем в индустрию качество, А независимым артистам — cилу</h3>
      </div>
                
                <div class="oor-become-artist-container">
                    <a href="<?php echo esc_url( home_url( '/become-artist' ) ); ?>" class="oor-become-artist-button">
                        <span class="oor-become-artist-text">Стать артистом</span>
                        <div class="oor-become-artist-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-small.svg" alt="Plus" width="32" height="32">
                        </div>
                    </a>
                </div>
                
                <div class="oor-quality-strength-images">
                    <a href="<?php echo esc_url( home_url( '/become-artist' ) ); ?>" class="oor-quality-img-container-1 text-cuberto-cursor-1" data-text="Стать артистом">
                        <picture>
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-1.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-1@2x.avif 2x" type="image/avif">
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-1.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-1@2x.webp 2x" type="image/webp">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-1.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-1.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-1@2x.png 2x" alt="Quality Strength 1" data-parallax-scale="1.3">
                        </picture>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/become-artist' ) ); ?>" class="oor-quality-img-container-2 text-cuberto-cursor-1" data-text="Стать артистом">
                        <picture>
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-2.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-2@2x.avif 2x" type="image/avif">
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-2.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-2@2x.webp 2x" type="image/webp">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-2.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-2.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/quality-strength-img-2@2x.png 2x" alt="Quality Strength 2">
                        </picture>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Challenge To The Usual 2 Section -->
    <section class="oor-challenge-2-section">
        <!-- Counter [04] -->
        <div class="oor-challenge-2-counter">[04]</div>
        
        <!-- Plus Large 1 -->
        <div class="oor-challenge-2-plus-1">
            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="Plus Large" width="17" height="17">
    </div>
        
        <!-- Plus Large 2 -->
        <div class="oor-challenge-2-plus-2">
            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="Plus Large" width="17" height="17">
          </div>
        
        <div class="oor-grid">
            <!-- Challenge To The Usual 2 — Left - 7 колонок -->
            <div class="oor-col-7 oor-challenge-2-left">
                <!-- Challenge Studio Image - 4 колонки минус 8px (слева) -->
                <div class="oor-challenge-studio-image img-cuberto-cursor-1" data-cursor-img="<?php echo get_template_directory_uri(); ?>/public/assets/challenge-studio.png">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/challenge-studio.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/challenge-studio@2x.avif 2x" type="image/avif">
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/challenge-studio.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/challenge-studio@2x.webp 2x" type="image/webp">
                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/challenge-studio.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/challenge-studio.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/challenge-studio@2x.png 2x" alt="Challenge Studio">
                    </picture>
        </div>
                
                <!-- Challenge To The Usual 2 — Text (заголовок, описание и ссылка — как в рабочей статичной версии, два дочерних элемента в grid) -->
                <div class="oor-challenge-2-text">
                    <div class="oor-challenge-2-studio">
                        <h2 class="oor-challenge-2-studio-title">
                Студия Out Of Records
                        </h2>
                        <p class="oor-challenge-2-studio-text">
                            Легендарное оборудование и индивидуальный сетап под проект и жанр. Записывай авторскую музыку, песни, подкасты, саундтреки и другое
              </p>
            </div>
                    <a href="<?php echo esc_url( home_url( '/studio' ) ); ?>" class="oor-challenge-2-music-game">
                        <div class="oor-challenge-2-music-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/icons-linking.svg" alt="Music Game Icon" width="32" height="32">
              </div>
                        <p class="oor-challenge-2-music-text">
                            <span class="tn-atom">МУЗЫКА — ЭТО ИГРА. ИГРАТЬ ПО-СВОЕМУ ПРОЩЕ, ЕСЛИ НЕ БОЯТЬСЯ ПРОВАЛОВ И ПРАВИЛ</span>
              </p>
                    </a>
                </div>
        </div>
            
            <!-- Пустой промежуток - 2 колонки -->
            <div class="oor-col-2"></div>
            
            <!-- Challenge To The Usual 2 — Good Works - 3 колонки -->
            <div class="oor-col-3 oor-challenge-2-good-works">
                <!-- Good Works Image -->
                <div class="oor-challenge-2-good-works-image img-cuberto-cursor-2" data-cursor-img="<?php echo get_template_directory_uri(); ?>/public/assets/good-works.png">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/good-works.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/good-works@2x.avif 2x" type="image/avif">
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/good-works.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/good-works@2x.webp 2x" type="image/webp">
                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/good-works.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/good-works.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/good-works@2x.png 2x" alt="Good Works">
                    </picture>
      </div>
                
                <!-- Good Works Text -->
                <div class="oor-challenge-2-good-works-text">
                    <div class="oor-challenge-2-good-works-header">
                        <h3 class="oor-challenge-2-good-works-title">
                            МЕДИАСПОРТ
          </h3>
                        <div class="oor-challenge-2-good-works-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/icons-linking.svg" alt="Good Works Icon" width="32" height="32">
            </div>
          </div>
                    <p class="oor-challenge-2-good-works-description">
                        Медийная баскетбольная команда DAWGS играет матчи в составе Лиги Ставок Media Basket. В команде — мастера спорта, многократные чемпионы, артисты и блогеры, резиденты лейбла и Андрей Кириленко
          </p>
        </div>
      </div>
    </div>
    </section>

    <!-- OUT OF TALK Section -->
    <section class="oor-out-of-talk-section">
        <!-- OUT OF TALK Text -->
        <div class="oor-out-of-talk-text">
            <!-- OUT OF TALK Title -->
            <div class="oor-out-of-talk-title">
                <!-- Заголовок -->
                <h2 class="oor-out-of-talk-heading">TALK-ШОУ</h2>
                
                <!-- Параграф -->
                <p class="oor-out-of-talk-description">Честные разговоры с главными героями индустрии. Обсуждаем, что нового в музыке сейчас — и что будет дальше. Мнения и тренды, взлеты и провалы, истории успеха и беседы о разном </p>
      </div>
            
            <!-- Counter [05] -->
            <div class="oor-out-of-talk-counter">[05]</div>
            
            <!-- Plus Large Icon -->
            <div class="oor-out-of-talk-plus">
                <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="Plus Large" width="17" height="17">
        </div>
        </div>
        
        <!-- OUT OF TALK Media -->
        <div class="oor-out-of-talk-media">
            <!-- OUT OF TALK Move to Page -->
            <div class="oor-out-of-talk-move">
                <!-- Line Large -->
                <div class="oor-out-of-talk-line-1">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-large.svg" alt="Line Large" width="18" height="1">
      </div>
                
                <!-- Link -->
                <a href="<?php echo esc_url(home_url('/talk-show')); ?>" class="oor-out-of-talk-link rolling-button"><span class="tn-atom">Все выпуски ток-шоу</span></a>
                
                <!-- Line Large -->
                <div class="oor-out-of-talk-line-2">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-large.svg" alt="Line Large" width="18" height="1">
    </div>
      </div>
            
            <!-- OUT OF TALK Images -->
            <div class="oor-out-of-talk-images">
                <?php
                // Получаем изображения Talk-show из ACF Gallery
                $talk_show_images = get_field('talk_show_images');
                
                if ($talk_show_images && is_array($talk_show_images)) {
                    $image_classes = ['oor-out-of-talk-image-1', 'oor-out-of-talk-image-2', 'oor-out-of-talk-image-3'];
                    $index = 0;
                    
                    foreach ($talk_show_images as $image) {
                        if ($index >= 3) break; // Максимум 3 изображения
                        
                        $image_url = is_array($image) ? $image['url'] : wp_get_attachment_image_url($image, 'full');
                        $image_medium = is_array($image) && isset($image['sizes']['medium']) ? $image['sizes']['medium'] : $image_url;
                        $image_large = is_array($image) && isset($image['sizes']['large']) ? $image['sizes']['large'] : $image_url;
                        $image_alt = is_array($image) && isset($image['alt']) ? $image['alt'] : 'Out of Talk ' . ($index + 1);
                        $image_class = isset($image_classes[$index]) ? $image_classes[$index] : 'oor-out-of-talk-image-' . ($index + 1);
                        ?>
                        <a href="<?php echo esc_url(home_url('/talk-show')); ?>" class="<?php echo esc_attr($image_class); ?> text-cuberto-cursor-1" data-text="Все выпуски<br>ток-шоу">
                            <picture>
                                <source srcset="<?php echo esc_url($image_medium); ?> 1x, <?php echo esc_url($image_large); ?> 2x" type="image/avif">
                                <source srcset="<?php echo esc_url($image_medium); ?> 1x, <?php echo esc_url($image_large); ?> 2x" type="image/webp">
                                <img src="<?php echo esc_url($image_url); ?>" 
                                     srcset="<?php echo esc_url($image_url); ?> 1x, <?php echo esc_url($image_large); ?> 2x" 
                                     alt="<?php echo esc_attr($image_alt); ?>">
                            </picture>
                        </a>
                        <?php
                        $index++;
                    }
                }
                ?>
        </div>
        </div>
    </section>

    <!-- Events Section -->
    <section class="oor-events-section">
        <!-- Events Text -->
        <div class="oor-events-text">
            <!-- Events Title -->
            <div class="oor-events-title">
                <!-- Заголовок -->
                <h2 class="oor-events-heading">События</h2>
                
                <!-- Параграф -->
                <p class="oor-events-description">Культурные, музыкальные и междисциплинарные ивенты — оффлайн. Следи за апдейтами!</p>
      </div>
            
            <!-- Counter [06] -->
            <div class="oor-events-counter">[06]</div>
            
            <!-- Plus Large Icon -->
            <div class="oor-events-plus">
                <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="Plus Large" width="17" height="17">
    </div>
        </div>
        
        <!-- Events Media -->
        <div class="oor-events-media">
            <!-- Posters Block - 8 колонок -->
            <div class="oor-events-posters">
                <?php
                // Получаем события из ACF Repeater
                $events_section = get_field('events_section');
                
                if ($events_section && is_array($events_section)) {
                    $poster_classes = ['oor-events-poster-1', 'oor-events-poster-2'];
                    $index = 0;
                    
                    foreach ($events_section as $event) {
                        if ($index >= 2) break; // Максимум 2 постера
                        
                        $event_poster = $event['event_poster'];
                        $sold_out = $event['sold_out'];
                        $buy_ticket_text = $event['buy_ticket_text'] ?: 'купить билет';
                        $ticket_url = $event['ticket_url'];
                        
                        if (!$event_poster) {
                            $index++;
                            continue;
                        }
                        
                        $poster_url = is_array($event_poster) ? $event_poster['url'] : wp_get_attachment_image_url($event_poster, 'full');
                        $poster_alt = is_array($event_poster) && isset($event_poster['alt']) ? $event_poster['alt'] : 'Event ' . ($index + 1);
                        $poster_class = isset($poster_classes[$index]) ? $poster_classes[$index] : 'oor-events-poster-' . ($index + 1);
                        ?>
                        <div class="<?php echo esc_attr($poster_class); ?>">
                            <picture>
                                <source srcset="<?php echo esc_url($poster_url); ?>" type="image/avif">
                                <source srcset="<?php echo esc_url($poster_url); ?>" type="image/webp">
                                <img src="<?php echo esc_url($poster_url); ?>" alt="<?php echo esc_attr($poster_alt); ?>">
                            </picture>
                            <?php if ($sold_out) : ?>
                                <button class="oor-events-sold-out">Sold out</button>
                            <?php else : ?>
                                <a href="<?php echo esc_url($ticket_url ?: '#'); ?>" class="oor-events-buy-ticket" target="_blank" rel="noopener noreferrer">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-large.svg" alt="Line" width="18" height="1">
                                    <span><?php echo esc_html($buy_ticket_text); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php
                        $index++;
                    }
                }
                ?>
      </div>
            
            <!-- Gap - 1 колонка -->
            <div></div>
            
            <!-- Photo Block - 3 колонки -->
            <div class="oor-events-photo">
                <?php
                // Получаем третье изображение события (если есть)
                $events_section = get_field('events_section');
                $event_3_poster = null;
                
                if ($events_section && is_array($events_section) && isset($events_section[2])) {
                    $event_3_poster = $events_section[2]['event_poster'];
                }
                
                $event_3_assets = get_template_directory_uri() . '/public/assets';
                if ($event_3_poster) {
                    $event_3_url = is_array($event_3_poster) ? $event_3_poster['url'] : wp_get_attachment_image_url($event_3_poster, 'full');
                    $event_3_medium = is_array($event_3_poster) && isset($event_3_poster['sizes']['medium']) ? $event_3_poster['sizes']['medium'] : $event_3_url;
                    $event_3_large = is_array($event_3_poster) && isset($event_3_poster['sizes']['large']) ? $event_3_poster['sizes']['large'] : $event_3_url;
                    $event_3_alt = is_array($event_3_poster) && isset($event_3_poster['alt']) ? $event_3_poster['alt'] : 'Event 3';
                    ?>
                    <div class="oor-events-photo-image text-cuberto-cursor-1" data-text="Все события">
                        <picture>
                            <source srcset="<?php echo esc_url($event_3_medium); ?> 1x, <?php echo esc_url($event_3_large); ?> 2x" type="image/avif">
                            <source srcset="<?php echo esc_url($event_3_medium); ?> 1x, <?php echo esc_url($event_3_large); ?> 2x" type="image/webp">
                            <img src="<?php echo esc_url($event_3_url); ?>" 
                                 srcset="<?php echo esc_url($event_3_url); ?> 1x, <?php echo esc_url($event_3_large); ?> 2x" 
                                 alt="<?php echo esc_attr($event_3_alt); ?>">
                        </picture>
                    </div>
                    <?php
                } else {
                    // Fallback: статичные файлы темы (event-3.avif, event-3@2x.avif и др.)
                    ?>
                    <div class="oor-events-photo-image text-cuberto-cursor-1" data-text="Все события">
                        <picture>
                            <source srcset="<?php echo esc_url($event_3_assets); ?>/event-3.avif 1x, <?php echo esc_url($event_3_assets); ?>/event-3@2x.avif 2x" type="image/avif">
                            <source srcset="<?php echo esc_url($event_3_assets); ?>/event-3.webp 1x, <?php echo esc_url($event_3_assets); ?>/event-3@2x.webp 2x" type="image/webp">
                            <img src="<?php echo esc_url($event_3_assets); ?>/event-3.png" srcset="<?php echo esc_url($event_3_assets); ?>/event-3.png 1x, <?php echo esc_url($event_3_assets); ?>/event-3@2x.png 2x" alt="Event 3">
                        </picture>
                    </div>
                    <?php
                }
                ?>
                
                <!-- All Events Link -->
                <a href="<?php echo esc_url(home_url('/events')); ?>" class="oor-events-photo-link rolling-button"><span class="tn-atom">Все события</span></a>
    </div>
              </div>
    </section>

    <!-- Merch Section -->
    <?php
    $merch_section_url = (function_exists('wc_get_page_id') && wc_get_page_id('shop') > 0)
        ? get_permalink(wc_get_page_id('shop'))
        : home_url('/merch');
    ?>
    <section class="oor-merch-section">
        <!-- Merch Text -->
        <div class="oor-merch-text">
            <!-- Merch Move to Page -->
            <div class="oor-merch-move">
                <!-- Counter [07] -->
                <div class="oor-merch-counter">[07]</div>
                
                <!-- Title -->
                <h2 class="oor-merch-title">Мерч</h2>
                
                <!-- Line Large -->
                <div class="oor-merch-line-2">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-large.svg" alt="Line Large" width="18" height="1">
            </div>
                </div>
            
            <!-- Merch Content -->
            <div class="oor-merch-content">
                <!-- Plus Large Icon -->
                <div class="oor-merch-plus-icon">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="Plus Large" width="17" height="17">
              </div>
                
                <!-- Description -->
                <p class="oor-merch-description">Присоединяйся к Out Of Records!</p>
                
                <!-- Buttons Menu: категории из WooCommerce -->
                <div class="oor-merch-buttons">
                    <?php
                    $product_cats = get_terms([
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => true,
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                    ]);
                    if (!is_wp_error($product_cats) && !empty($product_cats)) {
                        foreach ($product_cats as $term) {
                            $url = add_query_arg('product_cat', $term->slug, $merch_section_url);
                            printf(
                                '<a href="%s" class="oor-merch-button">%s</a>',
                                esc_url($url),
                                esc_html($term->name)
                            );
                        }
                    } else {
                        // Fallback, если WooCommerce выключен или категорий нет
                        ?>
                        <a href="<?php echo esc_url($merch_section_url); ?>" class="oor-merch-button">Лонгсливы</a>
                        <a href="<?php echo esc_url($merch_section_url); ?>" class="oor-merch-button">Футболки</a>
                        <a href="<?php echo esc_url($merch_section_url); ?>" class="oor-merch-button">Кепки</a>
                        <a href="<?php echo esc_url($merch_section_url); ?>" class="oor-merch-button">Носки</a>
                        <a href="<?php echo esc_url($merch_section_url); ?>" class="oor-merch-button">Худи</a>
                        <?php
                    }
                    ?>
                </div>
              </div>
            </div>
        
        <!-- Merch Images Grid -->
        <div class="oor-merch-images-grid">
            <div class="oor-merch-images-wrapper">
                <?php
                // Получаем изображения мерч из ACF Gallery — используем oor_picture_element для качества и правильных 1x/2x, avif/webp/png
                $merch_images = get_field('merch_images');
                
                if ($merch_images && is_array($merch_images)) {
                    foreach ($merch_images as $image) {
                        $image_id = is_array($image) && isset($image['ID']) ? (int) $image['ID'] : (int) $image;
                        if ($image_id <= 0) {
                            continue;
                        }
                        $image_alt = is_array($image) && isset($image['alt']) ? $image['alt'] : 'Мерч';
                        echo '<a href="' . esc_url($merch_section_url) . '" class="oor-merch-image-item text-cuberto-cursor-1" data-text="Мерч">';
                        echo oor_picture_element($image_id, $image_alt, 'oor-merch-image-item-img', ['loading' => 'lazy']);
                        echo '</a>';
                    }
                }
                ?>
          </div>
            </div>
    </section>

<?php
get_footer();
?>
