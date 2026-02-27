<?php
/**
 * WooCommerce Shop (Merch) archive template.
 * Основан на статической странице merch.html.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main-content">
    <!-- Merch Hero Section -->
    <section class="oor-section-hero oor-merch-hero">
        <div class="oor-container">
            <div class="oor-merch-hero-header">
                <h1 class="oor-merch-hero-title"><?php esc_html_e('МЕРЧ OOR', 'oor-theme'); ?></h1>
            </div>
        </div>
    </section>

    <!-- Merch Filters Section (категории из WooCommerce) -->
    <?php
    $product_cats = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);
    ?>
    <section class="oor-merch-filters-section">
        <div class="oor-container">
            <div class="oor-merch-filters">
                <button type="button" class="oor-merch-filter-btn oor-merch-filter-btn--active" data-filter="all"><?php esc_html_e('Всё', 'oor-theme'); ?></button>
                <?php
                if (!is_wp_error($product_cats) && !empty($product_cats)) {
                    foreach ($product_cats as $term) {
                        printf(
                            '<button type="button" class="oor-merch-filter-btn" data-filter="%s">%s</button>',
                            esc_attr($term->slug),
                            esc_html($term->name)
                        );
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Merch Products Grid Section -->
    <section class="oor-merch-products-section">
        <div class="oor-container">
            <div class="oor-merch-products-grid products">
                <?php
                // Главный цикл — только если в запросе реально запрошены товары (не страница мерч).
                global $wp_query;
                $use_main_loop = $wp_query->get('post_type') === 'product' && have_posts();
                if ($use_main_loop) :
                    while (have_posts()) :
                        the_post();
                        global $product;
                        if (!$product instanceof WC_Product) {
                            $product = wc_get_product(get_the_ID());
                        }
                        $terms = get_the_terms(get_the_ID(), 'product_cat');
                        $category_slugs = [];
                        if ($terms && !is_wp_error($terms)) {
                            foreach ($terms as $t) {
                                $category_slugs[] = $t->slug;
                            }
                        }
                        $data_category = !empty($category_slugs) ? implode(' ', $category_slugs) : 'uncategorized';
                        ?>
                        <div <?php wc_product_class('oor-merch-product product', $product); ?> data-category="<?php echo esc_attr($data_category); ?>">
                            <a href="<?php the_permalink(); ?>" class="oor-merch-product-link">
                                <div class="oor-merch-product-image-wrapper">
                                    <div class="oor-merch-product-bg"></div>
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('large', [
                                            'class' => 'oor-merch-product-image wp-post-image',
                                            'alt'   => esc_attr(get_the_title()),
                                        ]);
                                    }
                                    ?>
                                </div>
                            </a>
                            <div class="oor-merch-product-price price">
                                <?php if ($product) : ?>
                                    <?php echo wp_kses_post($product->get_price_html()); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile;
                else :
                    $products = function_exists('wc_get_products') ? wc_get_products([
                        'status'  => 'publish',
                        'limit'   => 12,
                        'orderby' => 'menu_order',
                        'order'   => 'ASC',
                        'return'  => 'ids',
                    ]) : [];
                    if (!empty($products)) :
                        foreach ($products as $product_id) :
                            $product = wc_get_product($product_id);
                            if (!$product || !$product->is_visible()) {
                                continue;
                            }
                            $terms = get_the_terms($product_id, 'product_cat');
                            $category_slugs = [];
                            if ($terms && !is_wp_error($terms)) {
                                foreach ($terms as $t) {
                                    $category_slugs[] = $t->slug;
                                }
                            }
                            $data_category = !empty($category_slugs) ? implode(' ', $category_slugs) : 'uncategorized';
                            ?>
                            <div class="oor-merch-product product type-product" data-category="<?php echo esc_attr($data_category); ?>">
                                <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="oor-merch-product-link">
                                    <div class="oor-merch-product-image-wrapper">
                                        <div class="oor-merch-product-bg"></div>
                                        <?php if (get_post_thumbnail_id($product_id)) : ?>
                                            <?php echo get_the_post_thumbnail($product_id, 'large', [
                                                'class' => 'oor-merch-product-image wp-post-image',
                                                'alt'   => esc_attr($product->get_name()),
                                            ]); ?>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                <div class="oor-merch-product-price price">
                                    <?php echo wp_kses_post($product->get_price_html()); ?>
                                </div>
                            </div>
                        <?php endforeach;
                    else : ?>
                        <p class="oor-merch-empty">
                            <?php esc_html_e('Товары пока не добавлены.', 'oor-theme'); ?>
                        </p>
                    <?php endif;
                endif; ?>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();

