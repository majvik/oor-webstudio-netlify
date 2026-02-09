    </main>
    
    <!-- deploy: {{DEPLOY_TIMESTAMP}} {{DEPLOY_VERSION}} -->
    <!-- Footer Section -->
    <footer class="oor-footer">
        <div class="oor-container">
            <div class="oor-footer-content">
                <div class="oor-footer-content-left">
                    <div class="oor-footer-logo">
                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/logo-footer.svg" 
                             alt="<?php bloginfo('name'); ?>" 
                             class="oor-footer-logo-image">
                    </div>
                    <?php
                    $footer_email = function_exists('get_field') ? get_field('footer_email', 'option') : '';
                    $footer_email = is_string($footer_email) ? trim($footer_email) : '';
                    if (!$footer_email) {
                        $footer_email = 'info@OOR.com';
                    }
                    $footer_email_safe = esc_attr($footer_email);
                    $footer_email_mailto = 'mailto:' . $footer_email_safe;
                    ?>
                    <a href="<?php echo esc_url($footer_email_mailto); ?>" 
                       class="oor-footer-email rolling-button text-cuberto-cursor-1" 
                       data-text="Написать">
                        <span class="tn-atom"><?php echo esc_html($footer_email); ?></span>
                    </a>
                </div>
                
                <div class="oor-footer-links">
                    <div class="oor-footer-links-top">
                        <h3 class="oor-footer-nav-title">навигация</h3>
                        <div class="oor-footer-links-top-group">
                            <?php
                            $footer_merch_url = (function_exists('wc_get_page_id') && wc_get_page_id('shop') > 0)
                                ? get_permalink(wc_get_page_id('shop'))
                                : home_url('/merch');
                            $footer_menu_items = [
                                ['url' => home_url('/'), 'text' => 'Main', 'slug' => 'main'],
                                ['url' => home_url('/manifest'), 'text' => 'Манифест', 'slug' => 'manifest'],
                                ['url' => home_url('/artists'), 'text' => 'Артисты', 'slug' => 'artists'],
                                ['url' => home_url('/studio'), 'text' => 'Студия', 'slug' => 'studio'],
                                ['url' => home_url('/services'), 'text' => 'Услуги', 'slug' => 'services'],
                                ['url' => home_url('/dawgs'), 'text' => 'DAWGS', 'slug' => 'dawgs'],
                                ['url' => home_url('/talk-show'), 'text' => 'Talk-шоу', 'slug' => 'talk-show'],
                                ['url' => home_url('/events'), 'text' => 'События', 'slug' => 'events'],
                                ['url' => $footer_merch_url, 'text' => 'Мерч', 'slug' => 'merch'],
                                ['url' => home_url('/contacts'), 'text' => 'Контакты', 'slug' => 'contacts'],
                            ];
                            
                            foreach ($footer_menu_items as $item) {
                                echo sprintf(
                                    '<a href="%s" class="oor-footer-link rolling-button" data-menu-item="%s"><span class="tn-atom">%s</span></a>',
                                    esc_url($item['url']),
                                    esc_attr($item['slug']),
                                    esc_html($item['text'])
                                );
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="oor-footer-links-bottom">
                        <h3 class="oor-footer-social-title">соцсети</h3>
                        <div class="oor-footer-links-bottom-group">
                            <?php
                            $footer_socials = function_exists('get_field') ? get_field('footer_social_links', 'option') : [];
                            if (!empty($footer_socials) && is_array($footer_socials)) {
                                foreach ($footer_socials as $item) {
                                    $label = isset($item['social_label']) ? trim((string) $item['social_label']) : '';
                                    $url   = isset($item['social_url']) ? trim((string) $item['social_url']) : '';
                                    if ($label !== '' && $url !== '') {
                                        echo sprintf(
                                            '<a href="%s" class="oor-footer-link rolling-button" target="_blank" rel="noopener noreferrer"><span class="tn-atom">%s</span></a>',
                                            esc_url($url),
                                            esc_html($label)
                                        );
                                    }
                                }
                            } else {
                                // Fallback, если в настройках пусто
                                ?>
                                <a href="#" class="oor-footer-link rolling-button"><span class="tn-atom">ВК</span></a>
                                <a href="#" class="oor-footer-link rolling-button"><span class="tn-atom">Инста</span></a>
                                <a href="#" class="oor-footer-link rolling-button"><span class="tn-atom">Ютуб</span></a>
                                <a href="#" class="oor-footer-link rolling-button"><span class="tn-atom">Телеграм</span></a>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="oor-footer-privacy">
                <div class="oor-footer-privacy-left">
                    <?php
                    $footer_privacy_url = (function_exists('get_field') ? get_field('footer_privacy_policy_url', 'option') : '') ?: '#';
                    $footer_all_rights_url = (function_exists('get_field') ? get_field('footer_all_rights_url', 'option') : '') ?: '#';
                    $footer_privacy_url = is_string($footer_privacy_url) ? trim($footer_privacy_url) : '#';
                    $footer_all_rights_url = is_string($footer_all_rights_url) ? trim($footer_all_rights_url) : '#';
                    if ($footer_privacy_url === '') {
                        $footer_privacy_url = '#';
                    }
                    if ($footer_all_rights_url === '') {
                        $footer_all_rights_url = '#';
                    }
                    ?>
                    <a href="<?php echo esc_url($footer_privacy_url); ?>" class="oor-footer-privacy-link rolling-button"><span class="tn-atom">политика конфиденциальности</span></a>
                    <a href="<?php echo esc_url($footer_all_rights_url); ?>" class="oor-footer-privacy-link rolling-button"><span class="tn-atom">все права защищены</span></a>
                </div>
                <div class="oor-footer-copyright">© <?php echo date('Y'); ?></div>
            </div>
        </div>
    </footer>
    
    <!-- Custom scrollbar -->
    <div class="custom-scrollbar" id="customScrollbar">
        <div class="custom-scrollbar-track">
            <div class="custom-scrollbar-thumb" id="scrollbarThumb"></div>
        </div>
    </div>
    
    <!-- Fullscreen Video Modal -->
    <?php
    // Получаем видео для модалки из ACF (только на главной странице)
    $modal_video_url = '';
    if (is_front_page() || is_page('home')) {
        $hero_modal_video = get_field('hero_modal_video');
        if ($hero_modal_video) {
            if (is_array($hero_modal_video)) {
                $modal_video_url = isset($hero_modal_video['url']) ? $hero_modal_video['url'] : (isset($hero_modal_video['ID']) ? wp_get_attachment_url($hero_modal_video['ID']) : '');
            } elseif (is_numeric($hero_modal_video)) {
                $modal_video_url = wp_get_attachment_url($hero_modal_video);
            }
        }
    }
    
    // Fallback на статичное видео, если ACF поле не заполнено
    if (!$modal_video_url) {
        $modal_video_url = get_template_directory_uri() . '/public/assets/OUTOFREC_reel_v4_nologo.mp4';
    }
    
    // Определяем WebM версию (если есть)
    $modal_video_webm = str_replace('.mp4', '.webm', $modal_video_url);
    $modal_video_webm = file_exists(str_replace(get_template_directory_uri(), get_template_directory(), $modal_video_webm)) 
        ? $modal_video_webm 
        : '';
    ?>
    <div class="oor-fullscreen-video" id="fullscreen-video">
        <video class="oor-fullscreen-video-element" controls 
               poster="<?php echo get_template_directory_uri(); ?>/public/assets/video-cover.avif">
            <?php if ($modal_video_webm) : ?>
                <source src="<?php echo esc_url($modal_video_webm); ?>" type="video/webm">
            <?php endif; ?>
            <source src="<?php echo esc_url($modal_video_url); ?>" type="video/mp4">
        </video>
        <button class="oor-fullscreen-close" id="fullscreen-close">
            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" 
                 alt="Закрыть" 
                 width="17" 
                 height="17">
        </button>
    </div>
    
    <?php wp_footer(); ?>
</body>
</html>
