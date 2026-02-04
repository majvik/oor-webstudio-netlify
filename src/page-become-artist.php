<?php
/**
 * Template Name: Стать артистом
 * Шаблон для страницы подачи демо-материалов
 */

get_header();
?>

<!-- HERO Section -->
<section class="oor-section-hero oor-become-artist-hero">
    <div class="oor-container">
        <div class="oor-become-artist-hero-header">
            <h1 class="oor-become-artist-hero-title">СТАТЬ АРТИСТОМ</h1>
        </div>
    </div>
</section>

<!-- Become Artist Content Section -->
<section class="oor-become-artist-content-section">
    <div class="oor-container oor-become-artist-container">
        <div class="oor-become-artist-content-wrapper">
            <!-- Main Heading -->
            <div class="oor-become-artist-main-heading">
                <h2 class="oor-become-artist-subtitle">ЛЕЙБЛ OUT OF RECORDS ИЩЕТ ТАЛАНТЛИВЫХ ИСПОЛНИТЕЛЕЙ!</h2>
            </div>
            
            <!-- Instructions (Two Columns) -->
            <div class="oor-become-artist-instructions">
                <div class="oor-become-artist-instructions-col">
                    <p class="oor-become-artist-instructions-text">
                        Демо-материалы рассматриваются только в виде ссылок на потоковое прослушивание (YouTube, SoundCloud, Я. Диск и т.д.) Прослушивание демозаписей командой лейбла занимает до 3х недель
                    </p>
                </div>
                <div class="oor-become-artist-instructions-col">
                    <p class="oor-become-artist-instructions-text">
                        Если по истечению данного срока ответа не последовало – к сожалению, мы не готовы сотрудничать в данный момент, но будем рады вашим дальнейшим работам!
                    </p>
                </div>
            </div>
            
            <!-- Contact Form 7 Form -->
            <div class="oor-become-artist-form-wrapper">
                <?php
                // Получаем ID формы из ACF или используем дефолтный
                $form_id = get_field('become_artist_form_id');
                if (!$form_id) {
                    // Пытаемся найти форму по названию
                    $forms = get_posts([
                        'post_type' => 'wpcf7_contact_form',
                        'posts_per_page' => 1,
                        'title' => 'Стать артистом'
                    ]);
                    if (!empty($forms)) {
                        $form_id = $forms[0]->ID;
                    }
                }
                
                // Используем форму с ID 123 (создана через WP CLI)
                $default_form_id = 123;
                $form_id = $form_id ? $form_id : $default_form_id;
                
                if (function_exists('wpcf7_contact_form')) {
                    // Выводим форму - она уже содержит нужные классы в своем контенте
                    echo do_shortcode('[contact-form-7 id="' . esc_attr($form_id) . '"]');
                } else {
                    echo '<p>Форма недоступна. Убедитесь, что плагин Contact Form 7 активирован.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
?>
