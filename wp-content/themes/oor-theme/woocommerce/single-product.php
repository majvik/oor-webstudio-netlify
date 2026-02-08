<?php
/**
 * Single product template.
 * Пока использует стандартный контент WooCommerce внутри OOR-обёртки.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Гарантируем класс body.oor-product-page при загрузке этого шаблона (на сервере is_product() может быть не готов к get_header).
$GLOBALS['oor_is_single_product_template'] = true;

get_header();
?>

<main id="main-content">
    <section class="oor-product-section oor-product-page-layout">
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
// Если на body не попал класс oor-product-page (кэш/сервер), добавляем его для хедера и общих стилей
?>
<script>(function(){ if (!document.body.classList.contains('oor-product-page') && document.querySelector('.oor-product-page-layout')) { document.body.classList.add('oor-product-page'); } })();</script>
<?php
get_footer();

