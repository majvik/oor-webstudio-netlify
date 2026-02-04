    </main>
    
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
                    <a href="mailto:info@OOR.com" 
                       class="oor-footer-email rolling-button text-cuberto-cursor-1" 
                       data-text="Написать">
                        <span class="tn-atom">info@OOR.com</span>
                    </a>
                </div>
                
                <div class="oor-footer-links">
                    <div class="oor-footer-links-top">
                        <h3 class="oor-footer-nav-title">навигация</h3>
                        <div class="oor-footer-links-top-group">
                            <?php
                            $footer_menu_items = [
                                ['url' => home_url('/'), 'text' => 'Main', 'slug' => 'main'],
                                ['url' => home_url('/manifest'), 'text' => 'Манифест', 'slug' => 'manifest'],
                                ['url' => home_url('/artists'), 'text' => 'Артисты', 'slug' => 'artists'],
                                ['url' => home_url('/studio'), 'text' => 'Студия', 'slug' => 'studio'],
                                ['url' => home_url('/services'), 'text' => 'Услуги', 'slug' => 'services'],
                                ['url' => home_url('/dawgs'), 'text' => 'DAWGS', 'slug' => 'dawgs'],
                                ['url' => home_url('/talk-show'), 'text' => 'Talk-шоу', 'slug' => 'talk-show'],
                                ['url' => home_url('/events'), 'text' => 'События', 'slug' => 'events'],
                                ['url' => home_url('/merch'), 'text' => 'Мерч', 'slug' => 'merch'],
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
                            <a href="#" class="oor-footer-link rolling-button"><span class="tn-atom">ВК</span></a>
                            <a href="#" class="oor-footer-link rolling-button"><span class="tn-atom">Инста</span></a>
                            <a href="#" class="oor-footer-link rolling-button"><span class="tn-atom">Ютуб</span></a>
                            <a href="#" class="oor-footer-link rolling-button"><span class="tn-atom">Телеграм</span></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="oor-footer-privacy">
                <div class="oor-footer-privacy-left">
                    <a href="#" class="oor-footer-privacy-link rolling-button"><span class="tn-atom">политика конфиденциальности</span></a>
                    <a href="#" class="oor-footer-privacy-link rolling-button"><span class="tn-atom">все права защищены</span></a>
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
