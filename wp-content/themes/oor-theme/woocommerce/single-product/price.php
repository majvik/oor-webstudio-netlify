<?php
/**
 * Single Product Price — переопределение темы OOR.
 * Краткая запись: перечёркнутая старая цена и актуальная, без текстового мусора.
 *
 * @package WooCommerce\Templates
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;

$regular = $product->get_regular_price();
$current = $product->get_price();
$on_sale = $product->is_on_sale() && $regular !== '' && $current !== '' && (float) $current < (float) $regular;

$price_class = esc_attr(apply_filters('woocommerce_product_price_class', 'price'));

if ($on_sale) {
    $price_html = '<del aria-hidden="true">' . wc_price($regular) . '</del> <ins>' . wc_price($current) . '</ins>';
} else {
    $price_html = wc_price($current);
}
?>
<p class="<?php echo $price_class; ?>"><?php echo $price_html; ?></p>
