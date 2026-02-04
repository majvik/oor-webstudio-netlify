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
                <!-- Episode 1 -->
                <a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" class="oor-talk-show-episode-card">
                    <div class="oor-talk-show-episode-image">
                        <picture>
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-episode-1.avif" type="image/avif">
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-episode-1.webp" type="image/webp">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-episode-1.png" alt="Podcast title" class="oor-media-cover">
                        </picture>
                        <div class="oor-talk-show-episode-overlay"></div>
                        <div class="oor-talk-show-episode-play">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/youtube-icon.svg" alt="Play" width="50" height="50">
                        </div>
                    </div>
                    <h3 class="oor-talk-show-episode-title">Podcast title</h3>
                </a>
                
                <!-- Episode 2 -->
                <a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" class="oor-talk-show-episode-card">
                    <div class="oor-talk-show-episode-image">
                        <picture>
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-episode-2.avif" type="image/avif">
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-episode-2.webp" type="image/webp">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-episode-2.png" alt="Podcast title" class="oor-media-cover">
                        </picture>
                        <div class="oor-talk-show-episode-overlay"></div>
                        <div class="oor-talk-show-episode-play">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/youtube-icon.svg" alt="Play" width="50" height="50">
                        </div>
                    </div>
                    <h3 class="oor-talk-show-episode-title">Podcast title</h3>
                </a>
                
                <!-- Episode 3 -->
                <a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" class="oor-talk-show-episode-card">
                    <div class="oor-talk-show-episode-image">
                        <picture>
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-episode-3.avif" type="image/avif">
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-episode-3.webp" type="image/webp">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-episode-3.png" alt="Podcast title" class="oor-media-cover">
                        </picture>
                        <div class="oor-talk-show-episode-overlay"></div>
                        <div class="oor-talk-show-episode-play">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/youtube-icon.svg" alt="Play" width="50" height="50">
                        </div>
                    </div>
                    <h3 class="oor-talk-show-episode-title">Podcast title</h3>
                </a>
                
                <!-- Episode 4 -->
                <a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" class="oor-talk-show-episode-card">
                    <div class="oor-talk-show-episode-image">
                        <picture>
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-episode-4.avif" type="image/avif">
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-episode-4.webp" type="image/webp">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-episode-4.png" alt="Podcast title" class="oor-media-cover">
                        </picture>
                        <div class="oor-talk-show-episode-overlay"></div>
                        <div class="oor-talk-show-episode-play">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/youtube-icon.svg" alt="Play" width="50" height="50">
                        </div>
                    </div>
                    <h3 class="oor-talk-show-episode-title">Podcast title</h3>
                </a>
            </div>
            
            <div class="oor-talk-show-episodes-footer">
                <a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" class="oor-talk-show-episodes-more">Больше выпусков</a>
                <div class="oor-talk-show-episodes-counter">4/25</div>
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
                        <!-- Column 1 - Faster scroll -->
                        <div class="oor-talk-show-rules-col oor-talk-show-rules-col-1" data-scroll-speed="1.5">
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-6.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-6.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-6.png" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-3.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-3.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-3.png" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-4.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-4.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-4.png" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-5.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-5.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-5.png" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-1.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-1.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-1.png" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                        </div>
                        
                        <!-- Column 2 - Slower scroll -->
                        <div class="oor-talk-show-rules-col oor-talk-show-rules-col-2" data-scroll-speed="1">
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-1.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-1.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-1.png" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-7.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-7.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-7.png" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-2.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-2.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-2.png" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-4.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-4.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-4.png" alt="Participant" class="oor-media-cover">
                                </picture>
                            </div>
                            <div class="oor-talk-show-participant-card">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-3.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-3.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/talk-show-participant-3.png" alt="Participant" class="oor-media-cover">
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
