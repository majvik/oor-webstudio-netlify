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
            <p class="oor-contacts-hero-copyright"><?php echo date('Y'); ?>©</p>
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
                        
                        if ($label && $display && $link) {
                            ?>
                            <div class="oor-contacts-email-frame">
                                <p class="oor-contacts-email-label"><?php echo esc_html($label); ?></p>
                                <a href="<?php echo esc_url($link); ?>" class="oor-contacts-email-link rolling-button">
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
