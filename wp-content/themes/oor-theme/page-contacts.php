<?php
/**
 * Template Name: Контакты
 * Шаблон для страницы контактов по дизайну из Figma
 */

get_header();
?>

<!-- HERO Section -->
<section class="oor-section-hero oor-contacts-hero">
    <div class="oor-container">
        <div class="oor-contacts-hero-header">
            <h1 class="oor-contacts-hero-title">КОНТАКТЫ</h1>
        </div>
    </div>
</section>

<!-- Contacts Content Section -->
<section class="oor-contacts-content-section">
    <div class="oor-container">
        <div class="oor-contacts-content-wrapper">
            <!-- Main Heading Frame -->
            <div class="oor-contacts-main-frame">
                <?php
                $main_title = get_field('contacts_main_title');
                if ($main_title) {
                    echo '<h2 class="oor-contacts-main-title">' . esc_html($main_title) . '</h2>';
                } else {
                    echo '<h2 class="oor-contacts-main-title">OUT OF RECORDS – ОТ ИДЕИ ДО НОВОЙ РЕАЛЬНОСТИ!</h2>';
                }
                ?>
            </div>
            
            <!-- Email Contacts Frame -->
            <div class="oor-contacts-emails-frame">
                <?php
                $contacts_list = get_field('contacts_list');
                if ($contacts_list && is_array($contacts_list) && count($contacts_list) > 0) {
                    foreach ($contacts_list as $contact) {
                        $label = isset($contact['contact_label']) ? $contact['contact_label'] : '';
                        $display = isset($contact['contact_display']) ? $contact['contact_display'] : '';
                        $link = isset($contact['contact_link']) ? $contact['contact_link'] : '';
                        $show_tg_icon = !empty($contact['contact_show_tg_icon']);
                        
                        if ($label && $display && $link) {
                            $link_classes = 'oor-contacts-email-link rolling-button';
                            if ($show_tg_icon) {
                                $link_classes .= ' oor-contacts-email-link--with-tg';
                            }
                            ?>
                            <div class="oor-contacts-email-frame">
                                <p class="oor-contacts-email-label"><?php echo esc_html($label); ?></p>
                                <a href="<?php echo esc_url($link); ?>" class="<?php echo esc_attr($link_classes); ?>">
                                    <?php if ($show_tg_icon) : ?>
                                        <span class="oor-contacts-email-link__tg-icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" focusable="false">
                                                <path d="M12 2C17.523 2 22 6.47696 22 12C22 17.523 17.523 22 12 22C6.47696 22 2 17.523 2 12C2 6.47696 6.47696 2 12 2ZM15.4496 16.0761C15.6335 15.5117 16.4952 9.88739 16.6017 8.77913C16.6339 8.44348 16.5278 8.22043 16.32 8.12087C16.0687 8 15.6965 8.06043 15.2648 8.21609C14.6726 8.42957 7.10217 11.6439 6.66478 11.83C6.25 12.0061 5.85783 12.1983 5.85783 12.4765C5.85783 12.6722 5.97391 12.7822 6.29391 12.8965C6.62696 13.0152 7.46565 13.2696 7.96087 13.4061C8.43783 13.5378 8.98087 13.4235 9.28522 13.2343C9.60783 13.0339 13.3309 10.5426 13.5983 10.3243C13.8652 10.1061 14.0783 10.3857 13.86 10.6043C13.6417 10.8226 11.0861 13.303 10.7491 13.6465C10.34 14.0635 10.6304 14.4957 10.9048 14.6687C11.2183 14.8661 13.4726 16.3783 13.8122 16.6209C14.1517 16.8635 14.4961 16.9735 14.8113 16.9735C15.1265 16.9735 15.2926 16.5583 15.4496 16.0761Z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                    <?php endif; ?>
                                    <span class="tn-atom"><?php echo esc_html($display); ?></span>
                                </a>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
            </div>
            
            <!-- Social Links Frame -->
            <div class="oor-contacts-social-frame">
                <p class="oor-contacts-social-title">СОЦСЕТИ:</p>
                <div class="oor-contacts-social-links">
                    <?php
                    $social_networks = get_field('social_networks');
                    if ($social_networks && is_array($social_networks) && count($social_networks) > 0) {
                        foreach ($social_networks as $social) {
                            $name = isset($social['social_name']) ? $social['social_name'] : '';
                            $url = isset($social['social_link']) ? $social['social_link'] : '';
                            
                            if ($name && $url) {
                                ?>
                                <a href="<?php echo esc_url($url); ?>" 
                                   class="oor-contacts-social-link rolling-button" 
                                   target="_blank" 
                                   rel="noopener noreferrer">
                                    <span class="tn-atom"><?php echo esc_html($name); ?></span>
                                </a>
                                <?php
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
?>
