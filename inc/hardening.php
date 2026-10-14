<?php
/**
 * Performance, SEO metadata and conservative query hardening.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;

/** Preserve WordPress and other installed SEO plugins as the source of truth. */
function bazarnama_seo_meta() {
    if ( is_admin() || is_feed() || is_404() || is_search() ) {
        return;
    }
    // Popular SEO plugins own Open Graph. Avoid duplicate OG titles/URLs.
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' )
        || defined( 'AIOSEO_VERSION' ) || function_exists( 'aioseo' )
    ) {
        return;
    }
    $title = wp_get_document_title();
    $description = get_bloginfo( 'description' );
    $type = 'website';
    $image = '';
    if ( is_singular() ) {
        $type = 'article';
        $post = get_queried_object();
        if ( $post instanceof WP_Post ) {
            $description = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 28, '…' );
            $thumbnail = get_post_thumbnail_id( $post->ID );
            if ( $thumbnail ) {
                $image = wp_get_attachment_image_url( $thumbnail, 'large' );
            }
        }
        if ( function_exists( 'is_product' ) && is_product() ) {
            $type = 'product';
        }
    }
    $url = home_url( '/' );
    if ( is_singular() ) {
        $url = get_permalink();
    } elseif ( function_exists( 'is_shop' ) && is_shop() && function_exists( 'wc_get_page_permalink' ) ) {
        $shop_url = wc_get_page_permalink( 'shop' );
        if ( $shop_url ) {
            $url = $shop_url;
        }
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( $term instanceof WP_Term ) {
            $term_url = get_term_link( $term );
            if ( ! is_wp_error( $term_url ) ) {
                $url = $term_url;
            }
            $term_description = term_description( $term );
            if ( $term_description ) {
                $description = wp_strip_all_tags( $term_description );
            }
        }
    } elseif ( is_post_type_archive() ) {
        $archive = get_queried_object();
        if ( $archive instanceof WP_Post_Type ) {
            $archive_url = get_post_type_archive_link( $archive->name );
            if ( $archive_url ) {
                $url = $archive_url;
            }
        }
    } elseif ( is_home() ) {
        $posts_page = (int) get_option( 'page_for_posts' );
        if ( $posts_page ) {
            $url = get_permalink( $posts_page );
        }
    }
    echo "\n" . '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( wp_strip_all_tags( $description ) ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    if ( $image ) {
        echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
    }
    echo '<meta name="twitter:card" content="' . esc_attr( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
}
add_action( 'wp_head', 'bazarnama_seo_meta', 6 );

/**
 * Respect script dependencies and defer only theme-owned enhancement scripts.
 * The classic WooCommerce cart fragments remain core-controlled.
 */
function bazarnama_defer_enhancement_scripts() {
    foreach ( array( 'bazarnama-main', 'bazarnama-discovery' ) as $handle ) {
        if ( wp_script_is( $handle, 'enqueued' ) ) {
            wp_script_add_data( $handle, 'strategy', 'defer' );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'bazarnama_defer_enhancement_scripts', 40 );

/**
 * Do not leak product variant stock information through arbitrary IDs:
 * the existing public endpoint already checks published status and visibility.
 * Prevent accidental indexing of browser-only collection and tracking views.
 */
function bazarnama_virtual_pages_noindex( $robots ) {
    if ( ! is_front_page() || ! function_exists( 'bazarnama_filter_value' ) ) {
        return $robots;
    }
    if ( in_array( bazarnama_filter_value( 'bn_view' ), array( 'wishlist', 'compare', 'track' ), true ) ) {
        $robots['noindex'] = true;
        unset( $robots['index'] );
    }
    return $robots;
}
add_filter( 'wp_robots', 'bazarnama_virtual_pages_noindex', 15 );

/**
 * Avoid caching customer-specific pages at the full-page level. Hosting/CDN
 * cache exclusions are still required and documented separately.
 */
function bazarnama_visitor_personal_pages() {
    if ( ! class_exists( 'WooCommerce' ) || is_admin() ) {
        return;
    }
    if ( ( function_exists( 'is_cart' ) && is_cart() )
        || ( function_exists( 'is_checkout' ) && is_checkout() )
        || ( function_exists( 'is_account_page' ) && is_account_page() )
    ) {
        if ( ! defined( 'DONOTCACHEPAGE' ) ) {
            define( 'DONOTCACHEPAGE', true );
        }
    }
}
add_action( 'template_redirect', 'bazarnama_visitor_personal_pages', 0 );
