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
                    <span class="oor-hero-year">©<?php echo date("Y"); ?></span>
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
                                <a href="#" class="oor-social-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <g opacity="0.7">
                                            <path d="M10.4803 1.06781C5.74513 1.75949 0.975952 5.72125 0.975952 12.0347C0.975952 18.1696 5.93096 23.04 11.9972 23.04C18.8314 23.04 24.862 16.3977 22.5431 8.74406C22.4433 8.41526 22.046 8.28527 21.7781 8.50031L19.7241 10.1484C19.5849 10.2598 19.5232 10.4382 19.5553 10.6134C19.6374 11.0637 19.68 11.5286 19.68 12C19.68 16.369 16.0368 19.8823 11.6213 19.6706C8.01598 19.4978 4.63954 16.2417 4.34626 12.6441C4.01554 8.58662 6.84306 5.12295 10.6331 4.44375C10.8664 4.40247 11.04 4.20789 11.04 3.97125V1.54219C11.04 1.24507 10.7741 1.02509 10.4803 1.06781ZM14.4085 1.24219C14.1469 1.24075 13.92 1.44709 13.92 1.72219V8.6775C13.3546 8.35014 12.7003 8.16 12 8.16C9.87937 8.16 8.16001 9.87936 8.16001 12C8.16001 14.1206 9.87937 15.84 12 15.84C14.1207 15.84 15.84 14.1206 15.84 12V5.35219C16.6138 5.80003 17.3046 6.37407 17.8772 7.05375C18.086 7.30191 18.4745 7.27769 18.6478 7.00313L19.9753 4.90313C20.0934 4.71689 20.0727 4.46903 19.9181 4.31063C19.4026 3.78167 17.6179 1.99545 14.5219 1.25625C14.4837 1.24719 14.4458 1.2424 14.4085 1.24219Z" fill="white"/>
              </g>
            </svg>
                                </a>
                                <a href="#" class="oor-social-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <g opacity="0.7">
                                            <path d="M14.4 5.28003C12.6713 5.28003 11.04 6.13878 10.08 7.53003V17.28H20.4C22.3687 17.28 24 15.6488 24 13.68C24 11.7113 22.3687 10.08 20.4 10.08C20.16 10.08 19.92 10.1213 19.68 10.17C19.44 7.4344 17.1844 5.28003 14.4 5.28003ZM8.16 7.68003C7.82437 7.68003 7.48875 7.72128 7.2 7.77003V17.28H8.16V7.68003ZM8.64 7.68003V17.28H9.6V7.92003C9.31125 7.8244 8.97563 7.72878 8.64 7.68003ZM6.72 7.92003C6.38437 8.0644 6.04875 8.20878 5.76 8.40003V17.28H6.72V7.92003ZM5.28 8.79003C4.89563 9.12565 4.56 9.55503 4.32 10.035V17.28H5.28V8.79003ZM3.12 10.56C3.03562 10.5657 2.95125 10.5807 2.88 10.605V17.235C3.02437 17.2838 3.21563 17.28 3.36 17.28H3.84V10.605C3.69563 10.5563 3.50437 10.56 3.36 10.56C3.28875 10.56 3.20437 10.5544 3.12 10.56ZM2.4 10.71C2.06437 10.8057 1.72875 10.95 1.44 11.19V16.65C1.72875 16.8413 2.06437 17.0344 2.4 17.13V10.71ZM0.96 11.565C0.384375 12.1894 0 13.0088 0 13.92C0 14.8313 0.384375 15.6507 0.96 16.275V11.565Z" fill="white"/>
              </g>
            </svg>
                                </a>
                                <a href="#" class="oor-social-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <g opacity="0.7" clip-path="url(#clip0_0_45)">
                                            <path d="M12.0043 0.951355C5.91452 0.951355 0.959961 5.90591 0.959961 11.9957C0.959961 18.0854 5.91452 23.04 12.0043 23.04C18.094 23.04 23.0486 18.0859 23.0486 11.9957C23.0486 5.90543 18.094 0.951355 12.0043 0.951355ZM16.679 16.9598C16.5403 17.1682 16.3118 17.2805 16.079 17.2805C15.9417 17.2805 15.803 17.2416 15.6801 17.1595C14.4931 16.368 12.48 15.84 10.8 15.8405C9.01724 15.8414 7.68092 16.2792 7.66748 16.2835C7.29116 16.4112 6.88268 16.2058 6.75692 15.8285C6.63116 15.4512 6.83516 15.0432 7.21244 14.9179C7.2758 14.8968 8.78636 14.4019 10.8 14.401C12.48 14.4 14.8166 14.8531 16.4793 15.9614C16.8105 16.1822 16.8998 16.6291 16.679 16.9598ZM18.1152 14.0126C17.9592 14.2632 17.6899 14.401 17.4148 14.401C17.266 14.401 17.1153 14.3611 16.98 14.2762C14.8276 12.935 12.6158 12.6514 10.7102 12.6682C8.55884 12.6874 6.83852 13.0978 6.80924 13.1064C6.37388 13.2302 5.91596 12.9758 5.79164 12.5386C5.66732 12.1003 5.9222 11.6448 6.35996 11.521C6.49292 11.483 8.20988 11.0606 10.5604 11.041C12.7036 11.0232 15.3744 11.3338 17.8521 12.8774C18.2376 13.1174 18.3561 13.6262 18.1152 14.0126ZM19.548 10.5662C19.3689 10.8706 19.0483 11.04 18.719 11.04C18.5539 11.04 18.3868 10.9973 18.2337 10.908C15.7252 9.43536 12.6753 9.12287 10.5585 9.11999C10.5484 9.11999 10.5384 9.11999 10.5283 9.11999C7.96844 9.11999 5.99708 9.57023 5.9774 9.57503C5.45996 9.69359 4.94492 9.37391 4.82492 8.85743C4.70492 8.34143 5.02508 7.82591 5.54108 7.70543C5.62988 7.68479 7.73612 7.19999 10.5283 7.19999C10.5393 7.19999 10.5504 7.19999 10.5614 7.19999C12.9158 7.20335 16.3267 7.56143 19.2062 9.25199C19.6632 9.52079 19.8163 10.1093 19.548 10.5662Z" fill="white"/>
              </g>
              <defs>
                <clipPath id="clip0_0_45">
                                                <rect width="24" height="24" fill="white"/>
                </clipPath>
              </defs>
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
                
                <!-- Buttons Menu -->
                <div class="oor-merch-buttons">
                    <button class="oor-merch-button">Лонгсливы</button>
                    <button class="oor-merch-button">Футболки</button>
                    <button class="oor-merch-button">Кепки</button>
                    <button class="oor-merch-button">Носки</button>
                    <button class="oor-merch-button">Худи</button>
                </div>
              </div>
            </div>
        
        <!-- Merch Images Grid -->
        <div class="oor-merch-images-grid">
            <div class="oor-merch-images-wrapper">
                <?php
                // Получаем изображения мерч из ACF Gallery
                $merch_images = get_field('merch_images');
                
                if ($merch_images && is_array($merch_images)) {
                    foreach ($merch_images as $image) {
                        $image_url = is_array($image) ? $image['url'] : wp_get_attachment_image_url($image, 'full');
                        $image_medium = is_array($image) && isset($image['sizes']['medium']) ? $image['sizes']['medium'] : $image_url;
                        $image_large = is_array($image) && isset($image['sizes']['large']) ? $image['sizes']['large'] : $image_url;
                        $image_alt = is_array($image) && isset($image['alt']) ? $image['alt'] : 'Merch';
                        ?>
                        <a href="<?php echo esc_url($merch_section_url); ?>" class="oor-merch-image-item text-cuberto-cursor-1" data-text="Мерч">
                            <picture>
                                <source srcset="<?php echo esc_url($image_medium); ?> 1x, <?php echo esc_url($image_large); ?> 2x" type="image/avif">
                                <source srcset="<?php echo esc_url($image_medium); ?> 1x, <?php echo esc_url($image_large); ?> 2x" type="image/webp">
                                <img src="<?php echo esc_url($image_url); ?>" 
                                     srcset="<?php echo esc_url($image_url); ?> 1x, <?php echo esc_url($image_large); ?> 2x" 
                                     alt="<?php echo esc_attr($image_alt); ?>">
                            </picture>
                        </a>
                        <?php
                    }
                }
                ?>
          </div>
            </div>
    </section>

<?php
get_footer();
?>
