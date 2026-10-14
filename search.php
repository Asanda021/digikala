<?php
/**
 * Fallback search results (WooCommerce product searches use the product archive).
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="primary" class="site-main">
    <div class="container content-shell">
        <h1><?php printf( esc_html__( 'نتایج جست‌وجو برای: %s', 'bazarnama' ), esc_html( get_search_query() ) ); ?></h1>
        <?php if ( have_posts() ) : ?>
            <div class="site-search-results">
            <?php while ( have_posts() ) : the_post(); ?>
                <article <?php post_class( 'entry-card' ); ?>>
                    <?php if ( has_post_thumbnail() ) : ?><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a><?php endif; ?>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php the_excerpt(); ?>
                </article>
            <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p><?php esc_html_e( 'نتیجه‌ای پیدا نشد. عبارت دیگری را جست‌وجو کنید.', 'bazarnama' ); ?></p>
            <?php get_search_form(); ?>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
