<?php
/**
 * Template Name: Услуги
 * Услуги
 */

get_header();
?>

<!-- HERO Section -->
    <section class="oor-services-hero">
        <div class="oor-container">
            <h1 class="oor-services-hero-title">УСЛУГИ</h1>
        </div>
    </section>

    <!-- Recording Section -->
    <section class="oor-services-recording">
        <div class="oor-container">
            <div class="oor-services-recording-divider"></div>
            
            <div class="oor-services-recording-content">
                <div class="oor-services-recording-list">
                    <!-- Услуга 1 -->
                    <div class="oor-services-recording-item">
                        <h3 class="oor-services-recording-item-title">Песня под ключ</h3>
                        <p class="oor-services-recording-item-text">Создание трека с нуля: музыка, аранжировка, текст, запись, сведение и мастеринг</p>
                    </div>
                    <div class="oor-services-recording-item-divider"></div>
                    
                    <!-- Услуга 2 -->
                    <div class="oor-services-recording-item">
                        <h3 class="oor-services-recording-item-title">Запись вокала и инструментов</h3>
                        <p class="oor-services-recording-item-text">Работа с топовым оборудованием и микрофонами; подбор под голос/инструмент; комфортная атмосфера записи</p>
                    </div>
                    <div class="oor-services-recording-item-divider"></div>
                    
                    <!-- Услуга 3 -->
                    <div class="oor-services-recording-item">
                        <h3 class="oor-services-recording-item-title">Сведение и мастеринг</h3>
                        <p class="oor-services-recording-item-text">Авторское сведение, аналоговый и цифровой мастеринг для всех платформ (Spotify, Apple Music, радио, клубные форматы)</p>
                    </div>
                    <div class="oor-services-recording-item-divider"></div>
                    
                    <!-- Услуга 4 -->
                    <div class="oor-services-recording-item">
                        <h3 class="oor-services-recording-item-title">Аренда студии под проекты</h3>
                        <p class="oor-services-recording-item-text">Для артистов, продюсеров, саунд-дизайнеров: доступ к оборудованию, инструментам и рабочему пространству</p>
                    </div>
                    <div class="oor-services-recording-item-divider"></div>
                    
                    <!-- Услуга 5 -->
                    <div class="oor-services-recording-item">
                        <h3 class="oor-services-recording-item-title">Саунд-дизайн и музыка для брендов</h3>
                        <p class="oor-services-recording-item-text">Джинглы, рекламные ролики, кино, сериалы, подкасты, игры</p>
                    </div>
                    <div class="oor-services-recording-item-divider"></div>
                    
                    <!-- Услуга 6 -->
                    <div class="oor-services-recording-item">
                        <h3 class="oor-services-recording-item-title">Сессии с продюсером, консультации</h3>
                        <p class="oor-services-recording-item-text">Индивидуальное продюсирование, помощь с подбором материала, обучение</p>
                    </div>
                    
                    <!-- Recording Image (mobile: between list and contact) -->
                    <div class="oor-services-recording-image">
                        <picture>
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/services-recording.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/services-recording@2x.avif 2x" type="image/avif">
                            <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/services-recording.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/services-recording@2x.webp 2x" type="image/webp">
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/services-recording.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/services-recording.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/services-recording@2x.png 2x" alt="Recording Studio" class="oor-media-cover">
                        </picture>
                    </div>
                    
                    <!-- Contact Block -->
                    <div class="oor-services-contact">
                        <p class="oor-services-contact-text">Хотите арендовать студию? Есть вопросы по техническому оснащению? Нужен профессиональный совет по треку?</p>
                        <a href="<?php echo esc_url( home_url( '/contacts' ) ); ?>" class="oor-services-contact-button rolling-button">
                            <span class="tn-atom">Напишите нам</span>
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/send-icon.svg" alt="" width="32" height="32">
                        </a>
                    </div>
                </div>
                
                <!-- Desktop Image -->
                <div class="oor-services-recording-image-desktop">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/services-recording.avif 1x, <?php echo get_template_directory_uri(); ?>/public/assets/services-recording@2x.avif 2x" type="image/avif">
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/services-recording.webp 1x, <?php echo get_template_directory_uri(); ?>/public/assets/services-recording@2x.webp 2x" type="image/webp">
                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/services-recording.png" srcset="<?php echo get_template_directory_uri(); ?>/public/assets/services-recording.png 1x, <?php echo get_template_directory_uri(); ?>/public/assets/services-recording@2x.png 2x" alt="Recording Studio" class="oor-media-cover" data-parallax-scale="1.15" data-parallax-max="48">
                    </picture>
                </div>
            </div>
            
            <div class="oor-services-recording-divider"></div>
        </div>
    </section>

<?php
get_footer();
?>
