<?php
/**
 * Single post.
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
                <p class="entry-meta"><?php echo esc_html( get_the_date() ); ?></p>
                <?php if ( has_post_thumbnail() ) : ?><div class="post-thumbnail"><?php the_post_thumbnail( 'large' ); ?></div><?php endif; ?>
                <?php the_content(); ?>
                <?php wp_link_pages(); ?>
                <?php if ( comments_open() || get_comments_number() ) : comments_template(); endif; ?>
            </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>
