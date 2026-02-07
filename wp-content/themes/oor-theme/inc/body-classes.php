<?php
/**
 * Управление body-классами
 */

function oor_body_classes($classes) {
    // Гарантируем класс admin-bar при показе топ-бара (для CSS: хедер/меню под баром)
    if (is_admin_bar_showing() && !in_array('admin-bar', $classes, true)) {
        $classes[] = 'admin-bar';
    }
    // Статические страницы
    if (is_page('studio')) {
        $classes[] = 'oor-studio-page';
    } elseif (is_page('artists') || is_post_type_archive('artist')) {
        $classes[] = 'oor-artists-page';
    } elseif (is_page('manifest')) {
        $classes[] = 'oor-manifest-page';
    } elseif (is_page('services')) {
        $classes[] = 'oor-services-page';
    } elseif (is_page('dawgs')) {
        $classes[] = 'oor-dawgs-page';
    } elseif (is_page('talk-show')) {
        $classes[] = 'oor-talk-show-page';
    } elseif (is_page('contacts')) {
        $classes[] = 'oor-contacts-page';
    } elseif (is_page('become-artist')) {
        $classes[] = 'oor-become-artist-page';
    }
    
    // Custom Post Types
    if (is_singular('artist')) {
        $classes[] = 'oor-artist-page';
    } elseif (is_singular('event')) {
        $classes[] = 'oor-event-page';
    } elseif (is_post_type_archive('event')) {
        $classes[] = 'oor-events-page';
    }
    
    // WooCommerce страницы (мерч в подпапке /merch/)
    if (function_exists('is_woocommerce')) {
        if (is_woocommerce()) {
            $classes[] = 'oor-merch-page';
        }
        if (is_product() || !empty($GLOBALS['oor_is_single_product_template'])) {
            $classes[] = 'oor-product-page';
        }
        if (is_cart()) {
            $classes[] = 'oor-cart-page';
        }
        if (is_checkout()) {
            $classes[] = 'oor-checkout-page';
        }
        // Единый класс для чёрного хедера на всех страницах магазина (каталог, товар, корзина, оформление)
        if (is_woocommerce() || is_product() || is_cart() || is_checkout() || !empty($GLOBALS['oor_is_single_product_template'])) {
            $classes[] = 'oor-shop-header';
        }
    }
    
    return $classes;
}
add_filter('body_class', 'oor_body_classes');
