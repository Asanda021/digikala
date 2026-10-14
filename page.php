<?php
/**
 * Regular pages.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="primary" class="site-main">
    <div class="container content-shell">
        <?php while ( have_posts() ) : the_post(); ?>
            <article <?php post_class(); ?>>
                <h1><?php the_title(); ?></h1>
                <?php the_content(); ?>
                <?php wp_link_pages(); ?>
                <?php if ( comments_open() || get_comments_number() ) : comments_template(); endif; ?>
            </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>
