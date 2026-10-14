<?php
/**
 * Main fallback template.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="primary" class="site-main">
    <div class="container content-shell">
        <h1 class="archive-title"><?php echo is_home() ? esc_html__( 'مطالب فروشگاه', 'bazarnama' ) : esc_html( get_bloginfo( 'name' ) ); ?></h1>
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <article <?php post_class( 'entry-card' ); ?>>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="entry-meta"><?php echo esc_html( get_the_date() ); ?></div>
                    <p><?php echo esc_html( get_the_excerpt() ); ?></p>
                </article>
            <?php endwhile; ?>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p><?php esc_html_e( 'هنوز مطلبی منتشر نشده است.', 'bazarnama' ); ?></p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
