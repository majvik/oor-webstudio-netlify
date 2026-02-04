<?php
/**
 * Template Name: Оплата (WooCommerce)
 * Страница оформления заказа, основана на checkout.html.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main-content">
    <section class="oor-checkout-section">
        <div class="oor-container">
            <div class="oor-checkout-header">
                <h1 class="oor-checkout-title"><?php esc_html_e('ОПЛАТА', 'oor-theme'); ?></h1>
                <div class="oor-checkout-copyright">2025©</div>
            </div>

            <div class="oor-checkout-divider"></div>

            <div class="oor-checkout-content">
                <?php echo do_shortcode('[woocommerce_checkout]'); ?>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();

