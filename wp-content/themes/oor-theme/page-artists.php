<?php
/**
 * Template Name: Страница артистов
 * Шаблон для страницы со списком всех артистов
 * На основе artists.html
 */

get_header();
?>

<!-- HERO Section -->
<section class="oor-section-hero oor-artists-hero">
    <div class="oor-container">
        <div class="oor-artists-hero-header">
            <h1 class="oor-artists-hero-title">АРТИСТЫ</h1>
            <p class="oor-artists-hero-copyright"><?php echo date('Y'); ?>©</p>
        </div>
    </div>
</section>

<!-- Artists Grid Section -->
<section class="oor-artists-grid-section">
    <div class="oor-container">
        <div class="oor-artists-grid">
            <?php
            // Получаем всех артистов из CPT 'artist'
            $artists_query = new WP_Query([
                'post_type' => 'artist',
                'posts_per_page' => -1, // Все артисты
                'post_status' => 'publish',
                'orderby' => 'title',
                'order' => 'ASC'
            ]);
            
            if ($artists_query->have_posts()) :
                while ($artists_query->have_posts()) : $artists_query->the_post();
                    $artist_id = get_the_ID();
                    $artist_name = get_the_title();
                    $artist_slug = get_post_field('post_name', $artist_id);
                    $artist_url = get_permalink($artist_id);
                    
                    // Получаем изображение артиста из ACF
                    $artist_image = get_field('artist_image', $artist_id);
                    
                    // Если нет ACF изображения, используем featured image
                    if (!$artist_image && has_post_thumbnail($artist_id)) {
                        $thumbnail_id = get_post_thumbnail_id($artist_id);
                        $artist_image = [
                            'url' => wp_get_attachment_image_url($thumbnail_id, 'full'),
                            'sizes' => [
                                'medium' => wp_get_attachment_image_url($thumbnail_id, 'medium'),
                                'large' => wp_get_attachment_image_url($thumbnail_id, 'large'),
                            ]
                        ];
                    }
                    
                    // Если все еще нет изображения, используем fallback
                    if (!$artist_image) {
                        // Можно использовать дефолтное изображение или пропустить артиста
                        continue;
                    }
                    
                    $image_url = $artist_image['url'];
                    $image_medium = isset($artist_image['sizes']['medium']) ? $artist_image['sizes']['medium'] : $image_url;
                    $image_large = isset($artist_image['sizes']['large']) ? $artist_image['sizes']['large'] : $image_url;
                    $image_alt = isset($artist_image['alt']) ? $artist_image['alt'] : esc_attr($artist_name);
                    ?>
                    
                    <a href="<?php echo esc_url($artist_url); ?>" class="oor-artist-card">
                        <div class="oor-artist-image">
                            <picture>
                                <source srcset="<?php echo esc_url($image_medium); ?> 1x, <?php echo esc_url($image_large); ?> 2x" type="image/avif">
                                <source srcset="<?php echo esc_url($image_medium); ?> 1x, <?php echo esc_url($image_large); ?> 2x" type="image/webp">
                                <img src="<?php echo esc_url($image_url); ?>" 
                                     srcset="<?php echo esc_url($image_url); ?> 1x, <?php echo esc_url($image_large); ?> 2x" 
                                     alt="<?php echo $image_alt; ?>" 
                                     class="oor-media-cover no-parallax">
                            </picture>
                        </div>
                        <h3 class="oor-artist-name"><?php echo esc_html($artist_name); ?></h3>
                    </a>
                    
                <?php
                endwhile;
                wp_reset_postdata();
            else :
                // Если артистов нет, показываем сообщение
                ?>
                <div class="oor-artists-empty">
                    <p>Артисты не найдены. Добавьте артистов в разделе "Артисты" админ-панели.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
get_footer();
?>
