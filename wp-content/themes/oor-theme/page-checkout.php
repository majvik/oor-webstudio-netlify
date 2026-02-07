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
                <style id="oor-checkout-fields-fix">
                    /* Отступ снизу у всех заголовков h3 в оплате */
                    .oor-checkout-section h3 {
                        margin-bottom: 16px !important;
                    }
                    /* Отступ у инпута внутри обёртки (для новодобавленных полей) */
                    .oor-checkout-section .woocommerce-input-wrapper > input {
                        margin-top: 4px !important;
                    }
                    /* Критично: обязательные поля чекаута всегда видимы (приоритет над WC/темами) */
                    .oor-checkout-section .woocommerce-billing-fields,
                    .oor-checkout-section .woocommerce-billing-fields__field-wrapper,
                    .oor-checkout-section .woocommerce-billing-fields .form-row,
                    .oor-checkout-section .woocommerce-billing-fields .form-row.validate-required,
                    .oor-checkout-section .woocommerce-billing-fields .woocommerce-input-wrapper,
                    .oor-checkout-section .woocommerce-billing-fields input,
                    .oor-checkout-section .woocommerce-billing-fields select,
                    .oor-checkout-section .woocommerce-billing-fields textarea {
                        display: block !important;
                        visibility: visible !important;
                        opacity: 1 !important;
                        height: auto !important;
                        min-height: 52px !important;
                        overflow: visible !important;
                        position: relative !important;
                        clip: auto !important;
                    }
                </style>
                <?php echo do_shortcode('[woocommerce_checkout]'); ?>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();

