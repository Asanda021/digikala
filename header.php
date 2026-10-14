<?php
/**
 * Global header.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'رفتن به محتوای اصلی', 'bazarnama' ); ?></a>
<?php $notice = get_theme_mod( 'bazarnama_notice', 'ارسال سریع و خرید آسان از فروشگاه ما' ); ?>
<?php if ( $notice ) : ?>
<div class="announcement"><div class="container"><?php echo esc_html( $notice ); ?></div></div>
<?php endif; ?>
<header class="site-header" id="masthead">
    <div class="container header-main">
        <button class="nav-toggle" type="button" aria-label="<?php esc_attr_e( 'نمایش فهرست', 'bazarnama' ); ?>" aria-controls="primary-navigation" aria-expanded="false">☰</button>
        <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
            <?php if ( has_custom_logo() ) : ?>
                <?php $logo_id = get_theme_mod( 'custom_logo' ); echo wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'custom-logo' ) ); ?>
            <?php else : ?>
                <span class="brand-mark" aria-hidden="true"><span>ب</span></span>
                <span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
            <?php endif; ?>
        </a>
        <div class="header-search">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <button type="submit" aria-label="<?php esc_attr_e( 'جست‌وجو', 'bazarnama' ); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg>
                </button>
                <label class="screen-reader-text" for="header-search-input"><?php esc_html_e( 'جست‌وجوی محصولات', 'bazarnama' ); ?></label>
                <input id="header-search-input" type="search" name="s" placeholder="<?php esc_attr_e( 'جست‌وجو در محصولات...', 'bazarnama' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
                <?php if ( class_exists( 'WooCommerce' ) ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
            </form>
            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <div class="bn-search-suggestions" id="bn-search-suggestions" role="status" aria-live="polite" hidden></div>
            <?php endif; ?>
        </div>
        <div class="header-actions">
            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <a class="action-link bn-head-collection" href="<?php echo esc_url( add_query_arg( 'bn_view', 'wishlist', home_url( '/' ) ) ); ?>" aria-label="<?php esc_attr_e( 'علاقه‌مندی‌ها', 'bazarnama' ); ?>" title="<?php esc_attr_e( 'علاقه‌مندی‌ها', 'bazarnama' ); ?>">♡</a>
                <a class="action-link bn-head-collection" href="<?php echo esc_url( add_query_arg( 'bn_view', 'compare', home_url( '/' ) ) ); ?>" aria-label="<?php esc_attr_e( 'مقایسه محصولات', 'bazarnama' ); ?>" title="<?php esc_attr_e( 'مقایسه محصولات', 'bazarnama' ); ?>">⊞</a>
            <?php endif; ?>
            <a class="action-link account-link" href="<?php echo esc_url( bazarnama_account_url() ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M8 9h8M8 14h5"/></svg>
                <?php echo is_user_logged_in() ? esc_html__( 'حساب کاربری', 'bazarnama' ) : esc_html__( 'ورود | ثبت‌نام', 'bazarnama' ); ?>
            </a>
            <a class="action-link cart-link" href="<?php echo esc_url( bazarnama_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'سبد خرید', 'bazarnama' ); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3 4h2l2.3 11.5h11.2L21 8H6"/><circle cx="9" cy="20" r="1.3"/><circle cx="18" cy="20" r="1.3"/></svg>
                <span><?php esc_html_e( 'سبد خرید', 'bazarnama' ); ?></span>
                <?php if ( class_exists( 'WooCommerce' ) ) : ?><span class="cart-count"><?php echo esc_html( (string) bazarnama_cart_count() ); ?></span><?php endif; ?>
            </a>
        </div>
    </div>
    <nav class="header-nav" id="primary-navigation" aria-label="<?php esc_attr_e( 'فهرست اصلی', 'bazarnama' ); ?>">
        <div class="container nav-row">
            <div class="category-nav-wrap">
                <button class="nav-highlight category-toggle" type="button" aria-controls="shop-category-panel" aria-expanded="false">
                    <span class="menu-bars" aria-hidden="true">☷</span><?php esc_html_e( 'دسته‌بندی کالاها', 'bazarnama' ); ?><span aria-hidden="true">⌄</span>
                </button>
                <div class="category-panel" id="shop-category-panel" hidden>
                    <strong class="category-panel-title"><?php esc_html_e( 'دسته‌بندی محصولات', 'bazarnama' ); ?></strong>
                    <div class="category-panel-links bn-mega-grid">
                        <?php
                        if ( taxonomy_exists( 'product_cat' ) ) :
                            $parent_categories = get_terms( array(
                                'taxonomy'   => 'product_cat',
                                'hide_empty' => true,
                                'parent'     => 0,
                                'number'     => 9,
                                'orderby'    => 'count',
                                'order'      => 'DESC',
                                'exclude'    => array_filter( array( (int) get_option( 'default_product_cat', 0 ) ) ),
                            ) );
                            if ( ! is_wp_error( $parent_categories ) ) :
                                foreach ( $parent_categories as $parent_category ) :
                                    $parent_url = get_term_link( $parent_category );
                                    if ( is_wp_error( $parent_url ) ) { continue; }
                                    $children = get_terms( array(
                                        'taxonomy' => 'product_cat',
                                        'hide_empty' => true,
                                        'parent' => (int) $parent_category->term_id,
                                        'number' => 6,
                                        'orderby' => 'name',
                                        'order' => 'ASC',
                                    ) );
                                    ?>
                                    <section class="bn-mega-group">
                                        <a class="bn-mega-parent" href="<?php echo esc_url( $parent_url ); ?>"><span aria-hidden="true">◈</span><?php echo esc_html( $parent_category->name ); ?><span aria-hidden="true">←</span></a>
                                        <?php if ( ! is_wp_error( $children ) ) : ?>
                                            <?php foreach ( $children as $child ) : ?>
                                                <?php $child_url = get_term_link( $child ); if ( is_wp_error( $child_url ) ) { continue; } ?>
                                                <a class="bn-mega-child" href="<?php echo esc_url( $child_url ); ?>"><?php echo esc_html( $child->name ); ?></a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </section>
                                    <?php
                                endforeach;
                            endif;
                        endif;
                        ?>
                        <a class="category-all bn-mega-all" href="<?php echo esc_url( bazarnama_shop_url() ); ?>"><?php esc_html_e( 'مشاهده همه محصولات', 'bazarnama' ); ?><span aria-hidden="true">←</span></a>
                    </div>
                </div>
            </div>
            <?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'primary-menu', 'fallback_cb' => 'bazarnama_nav_fallback', 'depth' => 2 ) ); ?>
            <span class="nav-location"><?php esc_html_e( 'خرید آنلاین، در هر کجا', 'bazarnama' ); ?></span>
        </div>
    </nav>
</header>
<nav class="mobile-bottom-nav" aria-label="<?php esc_attr_e( 'دسترسی سریع موبایل', 'bazarnama' ); ?>">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>><span aria-hidden="true">⌂</span><?php esc_html_e( 'خانه', 'bazarnama' ); ?></a>
    <a href="<?php echo esc_url( bazarnama_shop_url() ); ?>"><span aria-hidden="true">▦</span><?php esc_html_e( 'فروشگاه', 'bazarnama' ); ?></a>
    <a href="<?php echo esc_url( bazarnama_cart_url() ); ?>"><span aria-hidden="true">🛒</span><?php esc_html_e( 'سبد خرید', 'bazarnama' ); ?></a>
    <a href="<?php echo esc_url( bazarnama_account_url() ); ?>"><span aria-hidden="true">♙</span><?php esc_html_e( 'حساب من', 'bazarnama' ); ?></a>
</nav>
