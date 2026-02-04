<?php
/**
 * Single product template.
 * Пока использует стандартный контент WooCommerce внутри OOR-обёртки.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main-content">
    <section class="oor-product-section">
        <div class="oor-container">
            <?php
            while (have_posts()) :
                the_post();
                /**
                 * По умолчанию выведем стандартный шаблон товара.
                 * При необходимости можно будет переопределить content-single-product.php
                 * и привести разметку к product.html.
                 */
                wc_get_template_part('content', 'single-product');
            endwhile;
            ?>
        </div>
    </section>
</main>

<?php
get_footer();

