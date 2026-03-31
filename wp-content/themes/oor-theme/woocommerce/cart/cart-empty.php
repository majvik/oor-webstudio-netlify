<?php
/**
 * Пустая корзина (переопределение темы).
 *
 * В ядре WooCommerce этот блок обёрнут в `if ( wc_get_page_id( 'shop' ) > 0 )`.
 * Здесь тот же HTML, что в ядре, но без этого условия (URL всё равно через
 * `woocommerce_return_to_shop_redirect` в functions.php, в т.ч. fallback /merch).
 *
 * Важно: этот файл используется только шорткодом [woocommerce_cart]. Блоковая
 * корзина (Gutenberg) его не подключает — для неё см. template_include в functions.php.
 *
 * @see woocommerce/templates/cart/cart-empty.php
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

/*
 * @hooked wc_empty_cart_message - 10
 */
do_action('woocommerce_cart_is_empty');

$wp_button_class = '';
if (function_exists('wc_wp_theme_get_element_class_name')) {
    $theme_btn = wc_wp_theme_get_element_class_name('button');
    if ($theme_btn) {
        $wp_button_class = ' ' . $theme_btn;
    }
}
?>
<div class="return-to-shop">
    <a class="button wc-backward oor-cart-return-to-shop<?php echo esc_attr($wp_button_class); ?>" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">
        <?php
        echo esc_html(apply_filters('woocommerce_return_to_shop_text', __('Return to shop', 'woocommerce')));
        ?>
    </a>
</div>
