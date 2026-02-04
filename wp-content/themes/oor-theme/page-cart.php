<?php
/**
 * Template Name: Корзина (WooCommerce)
 * Страница корзины, основана на cart.html.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main-content">
    <section class="oor-cart-section">
        <div class="oor-container">
            <div class="oor-cart-header">
                <h1 class="oor-cart-title"><?php esc_html_e('КОРЗИНА', 'oor-theme'); ?></h1>
                <div class="oor-cart-copyright">2025©</div>
            </div>

            <div class="oor-cart-divider"></div>

            <div class="oor-cart-items">
                <?php echo do_shortcode('[woocommerce_cart]'); ?>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();

