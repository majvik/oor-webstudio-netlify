<?php
/**
 * Template Name: Talk-шоу
 * Talk-шоу
 */

get_header();
?>

<!-- HERO Section -->
    <section class="oor-talk-show-hero">
        <div class="oor-container">
            <div class="oor-talk-show-hero-top">
                <div class="oor-talk-show-hero-left">
                    <h1 class="oor-talk-show-hero-title">OUT OF TALK</h1>
                </div>
                <div class="oor-talk-show-hero-right">
                    <div class="oor-talk-show-hero-video-small">
                        <video class="oor-talk-show-hero-video" autoplay muted loop playsinline preload="metadata" poster="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-episode-4.png">
                            <source src="<?php echo get_template_directory_uri(); ?>/public/assets/OUTOFREC_reel_v4_nologo.webm" type="video/webm">
                            <source src="<?php echo get_template_directory_uri(); ?>/public/assets/OUTOFREC_reel_v4_nologo.mp4" type="video/mp4">
                        </video>
                    </div>
                    <p class="oor-talk-show-hero-description">Обсуждаем музыкальную индустрию во всех ракурсах</p>
                </div>
            </div>
            
            <div class="oor-talk-show-hero-bottom">
                <div class="oor-talk-show-hero-video-large">
                    <video class="oor-talk-show-hero-video" autoplay muted loop playsinline preload="metadata" poster="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-hero-large.png">
                        <source src="<?php echo get_template_directory_uri(); ?>/public/assets/OUTOFREC_reel_v4_nologo.webm" type="video/webm">
                        <source src="<?php echo get_template_directory_uri(); ?>/public/assets/OUTOFREC_reel_v4_nologo.mp4" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>
        
        <div class="oor-talk-show-hero-line">
            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-large.svg" alt="" width="18" height="1">
        </div>
    </section>

    <!-- Episodes Section -->
    <section class="oor-talk-show-episodes">
        <div class="oor-container">
            <div class="oor-talk-show-episodes-header">
                <div class="oor-talk-show-episodes-index">[01]</div>
                <div class="oor-talk-show-episodes-plus-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="18" height="18">
                </div>
                <div class="oor-talk-show-episodes-plus-right">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="18" height="18">
                </div>
            </div>
            
            <div class="oor-talk-show-episodes-grid">
                <?php
                $episodes = get_field('talk_show_episodes');
                $default_url = 'https://www.youtube.com';
                $theme_uri = get_template_directory_uri();
                if (!is_array($episodes)) {
                    $episodes = [];
                }
                // Показываем до 4 карточек: из ACF или заглушки
                for ($i = 0; $i < 4; $i++) {
                    $title = 'Podcast title';
                    $subtitle = '';
                    $url = $default_url;
                    $image = null;
                    $fallback_img = $i + 1;
                    if (!empty($episodes[$i])) {
                        $row = $episodes[$i];
                        if (!empty($row['episode_title'])) $title = $row['episode_title'];
                        if (!empty($row['episode_subtitle'])) $subtitle = $row['episode_subtitle'];
                        if (!empty($row['episode_url'])) $url = $row['episode_url'];
                        if (!empty($row['episode_image']) && is_array($row['episode_image']) && !empty($row['episode_image']['url'])) {
                            $image = $row['episode_image'];
                        }
                    }
                    ?>
                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" class="oor-talk-show-episode-card">
                    <div class="oor-talk-show-episode-image">
                        <?php if ($image) : ?>
                        <picture>
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($title); ?>" class="oor-media-cover" loading="lazy">
                        </picture>
                        <?php else : ?>
                        <picture>
                            <source srcset="<?php echo $theme_uri; ?>/public/assets/talk-show-episode-<?php echo $fallback_img; ?>.avif 1x, <?php echo $theme_uri; ?>/public/assets/talk-show-episode-<?php echo $fallback_img; ?>@2x.avif 2x" type="image/avif">
                            <source srcset="<?php echo $theme_uri; ?>/public/assets/talk-show-episode-<?php echo $fallback_img; ?>.webp 1x, <?php echo $theme_uri; ?>/public/assets/talk-show-episode-<?php echo $fallback_img; ?>@2x.webp 2x" type="image/webp">
                            <img src="<?php echo $theme_uri; ?>/public/assets/talk-show-episode-<?php echo $fallback_img; ?>.png" srcset="<?php echo $theme_uri; ?>/public/assets/talk-show-episode-<?php echo $fallback_img; ?>.png 1x, <?php echo $theme_uri; ?>/public/assets/talk-show-episode-<?php echo $fallback_img; ?>@2x.png 2x" alt="<?php echo esc_attr($title); ?>" class="oor-media-cover" loading="lazy">
                        </picture>
                        <?php endif; ?>
                        <div class="oor-talk-show-episode-overlay"></div>
                        <div class="oor-talk-show-episode-play">
                            <img src="<?php echo $theme_uri; ?>/public/assets/youtube-icon.svg" alt="Play" width="50" height="50">
                        </div>
                    </div>
                    <h3 class="oor-talk-show-episode-title"><?php echo esc_html($title); ?></h3>
                    <?php if ($subtitle !== '') : ?>
                    <p class="oor-talk-show-episode-subtitle"><?php echo esc_html($subtitle); ?></p>
                    <?php endif; ?>
                </a>
                <?php } ?>
            </div>
            
            <div class="oor-talk-show-episodes-footer">
                <?php
                $more_url = get_field('talk_show_episodes_more_url');
                if (empty($more_url)) $more_url = 'https://www.youtube.com/@OutOfTalk';
                ?>
                <a href="<?php echo esc_url($more_url); ?>" target="_blank" rel="noopener noreferrer" class="oor-talk-show-episodes-more">Больше выпусков</a>
            </div>
        </div>
    </section>

    <!-- Rules Section - Parallax Scroll -->
    <section class="oor-talk-show-rules" id="talk-show-rules">
        <div class="oor-talk-show-rules-wrapper">
            <div class="oor-container">
                <div class="oor-talk-show-rules-header">
                    <div class="oor-talk-show-rules-index">[02]</div>
                </div>
                
                <div class="oor-talk-show-rules-content">
                    <div class="oor-talk-show-rules-left">
                        <h2 class="oor-talk-show-rules-title">ТОКШОУ БЕЗ ПРАВИЛ</h2>
                        <div class="oor-talk-show-rules-line">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-large.svg" alt="" width="18" height="1">
                        </div>
                        <p class="oor-talk-show-rules-description">Честные разговоры о разном — со звездами, которым есть что сказать. Взлеты и провалы, правда и вымыслы, романы и конфликты, истории успеха и просто истории</p>
                        <div class="oor-talk-show-rules-line-2">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-large.svg" alt="" width="18" height="1">
                        </div>
                        <div class="oor-talk-show-rules-participants">
                            <p class="oor-talk-show-rules-participants-title">Принимали участие:</p>
                            <p class="oor-talk-show-rules-participants-list">Александр Шепс, Сюзанна, LYRIQ, Чипинкос, CAPTOWN, BOOKER, Фогель, JANAGA, Жак Энтони, DASHI, Murovei, Отар Кушанашвили и другие</p>
                        </div>
                    </div>
                    
                    <div class="oor-talk-show-rules-right">
                        <!-- Column 1 - Faster scroll (participants 1-4) -->
                        <div class="oor-talk-show-rules-col oor-talk-show-rules-col-1" data-scroll-speed="1.5">
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-1.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-1@2x.avif 2x" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-1.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-1@2x.webp 2x" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-1.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-1.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-1@2x.png 2x" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-2.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-2@2x.avif 2x" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-2.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-2@2x.webp 2x" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-2.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-2.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-2@2x.png 2x" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-3.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-3@2x.avif 2x" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-3.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-3@2x.webp 2x" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-3.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-3.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-3@2x.png 2x" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-4.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-4@2x.avif 2x" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-4.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-4@2x.webp 2x" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-4.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-4.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-4@2x.png 2x" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                        </div>
                        
                        <!-- Column 2 - Slower scroll (participants 5-8) -->
                        <div class="oor-talk-show-rules-col oor-talk-show-rules-col-2" data-scroll-speed="1">
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-5.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-5@2x.avif 2x" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-5.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-5@2x.webp 2x" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-5.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-5.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-5@2x.png 2x" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-6.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-6@2x.avif 2x" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-6.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-6@2x.webp 2x" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-6.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-6.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-6@2x.png 2x" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-7.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-7@2x.avif 2x" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-7.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-7@2x.webp 2x" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-7.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-7.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-7@2x.png 2x" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-8.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-8@2x.avif 2x" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-8.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-8@2x.webp 2x" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-8.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-8.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-8@2x.png 2x" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
get_footer();
?>
