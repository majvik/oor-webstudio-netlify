<?php
/**
 * Template Name: Манифест
 * Манифест
 */

get_header();
?>

<!-- HERO Section -->
    <section class="oor-manifest-hero">
        <div class="oor-container">
            <div class="oor-manifest-hero-top">
                <div class="oor-manifest-hero-plus-left">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="18" height="18">
                </div>
                <h1 class="oor-manifest-hero-title">БРОСИТЬ ВЫЗОВ ПРИВЫЧНОМУ</h1>
            </div>
        </div>
        <div class="oor-container">
            <div class="oor-manifest-hero-video-wrapper">
                <!-- Video background -->
                <video class="oor-manifest-hero-video" autoplay muted loop playsinline preload="metadata" poster="<?php echo get_template_directory_uri(); ?>/public/assets/video-cover.avif">
                    <source src="<?php echo get_template_directory_uri(); ?>/public/assets/OUTOFREC_reel_v4_nologo.webm" type="video/webm">
                    <source src="<?php echo get_template_directory_uri(); ?>/public/assets/OUTOFREC_reel_v4_nologo.mp4" type="video/mp4">
                </video>
                <div class="oor-manifest-hero-overlay">
                    <div class="oor-manifest-hero-col-left">
                        <div class="oor-manifest-hero-card">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/manifest-out-of-rec.svg" alt="OUT OF REC">
                        </div>
                    </div>
                    <div class="oor-manifest-hero-col-right">
                        <p class="oor-manifest-hero-text">Чтобы быть музыкантом, нужно сердце и смелость. Сердце — чтобы чувствовать и исследовать звук. Смелость — чтобы бросать вызов скучным правилам, чужим мнениям и мейнстриму</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Manifest Section -->
    <section class="oor-manifest-section">
        <div class="oor-container">
            <div class="oor-manifest-section-inner">
                <div class="oor-manifest-section-left">
                    <div class="oor-manifest-section-label">[МАНИФЕСТ]</div>
                    <div class="oor-manifest-section-line">
                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/line-large.svg" alt="" width="18" height="1">
                    </div>
                </div>
                <div class="oor-manifest-section-content">
                    <h2 class="oor-manifest-section-title">СЕРДЦЕ И СМЕЛОСТЬ</h2>
                    <div class="oor-manifest-section-text">
                        <p>Мы считаем несправедливым, что артистам так часто приходится гнаться за цифрами, выживать в иерархии индустрии, подгонять свое творчество под чужие лекала.</p>
                        <p>Мы здесь, чтобы поддержать тех для кого музыка многое значит. Тех, кто многое делает ради музыки. Тех, чьи треки и голоса еще не были услышаны — но мы это исправим</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CEO Section -->
    <section class="oor-manifest-ceo-section">
        <div class="oor-container">
            <div class="oor-manifest-ceo-header">
                <div class="oor-manifest-ceo-label">[CEO OUT OF RECORDS]</div>
                <div class="oor-manifest-ceo-plus">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="18" height="18">
                </div>
            </div>
            <div class="oor-manifest-ceo-quote">
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean suscipit felis in tellus volutpat sodales in sed erat"
            </div>
            <div class="oor-manifest-ceo-content">
                <div class="oor-manifest-ceo-plus-top-right">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="18" height="18">
                </div>
                <div class="oor-manifest-ceo-text">
                    <div class="oor-manifest-ceo-info">
                        <div class="oor-manifest-ceo-name">Анастасия Кудряшкина</div>
                        <div class="oor-manifest-ceo-role">CEO OOR</div>
                    </div>
                    <div class="oor-manifest-ceo-description">
                        <p>Мы считаем несправедливым, что артистам так часто приходится гнаться за цифрами, выживать в иерархии индустрии, подгонять свое творчество под чужие лекала.</p>
                        <p>Мы здесь, чтобы поддержать тех для кого музыка многое значит. Тех, кто многое делает ради музыки. Тех, чьи треки и голоса еще не были услышаны — но мы это исправим</p>
                    </div>
                    <a href="https://music.apple.com/ru/artist/%D0%BD%D0%B5%D1%82-%D0%B2%D1%80%D0%B5%D0%BC%D0%B5%D0%BD%D0%B8-%D0%BE%D0%B1%D1%8A%D1%8F%D1%81%D0%BD%D1%8F%D1%82%D1%8C/1587750356" class="oor-manifest-ceo-link" target="_blank" rel="noopener noreferrer">MUSIC</a>
                </div>
                <div class="oor-manifest-ceo-image">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/manifest-ceo-photo.avif" type="image/avif">
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/manifest-ceo-photo.webp" type="image/webp">
                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/manifest-ceo-photo.png" alt="Анастасия Кудряшкина - CEO OOR" class="oor-media-cover">
                    </picture>
                </div>
                <div class="oor-manifest-ceo-plus-bottom">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="18" height="18">
                </div>
            </div>
        </div>
    </section>

<?php
get_footer();
?>
