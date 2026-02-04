<?php
/**
 * Базовый шаблон (fallback)
 * Используется если нет специфичного шаблона
 */

get_header();
?>

<main id="main-content">
    <div class="oor-container">
        <?php
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>
                    
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
                <?php
            }
        } else {
            ?>
            <p>Контент не найден.</p>
            <?php
        }
        ?>
    </div>
</main>

<?php
get_footer();
?>
