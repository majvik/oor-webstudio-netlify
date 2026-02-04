<?php
/**
 * Template Name: Страница по умолчанию
 * Базовый шаблон для всех страниц
 */

get_header();
?>

<main id="main-content" class="oor-main-content">
    <div class="oor-container">
        <?php
        while (have_posts()) :
            the_post();
        ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('oor-page-content'); ?>>
                <header class="oor-page-header">
                    <h1 class="oor-page-title"><?php the_title(); ?></h1>
                </header>

                <div class="oor-page-body">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();
?>
