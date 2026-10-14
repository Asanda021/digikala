<?php
/**
 * Bazarnama theme functions.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'BAZARNAMA_VERSION' ) ) {
    define( 'BAZARNAMA_VERSION', '1.1.0' );
}

function bazarnama_setup() {
    load_theme_textdomain( 'bazarnama', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'custom-logo', array( 'height' => 72, 'width' => 230, 'flex-height' => true, 'flex-width' => true ) );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'woocommerce', array(
        'thumbnail_image_width' => 340,
        'single_image_width'    => 620,
        'product_grid'          => array( 'default_rows' => 4, 'min_rows' => 2, 'max_rows' => 10, 'default_columns' => 4, 'min_columns' => 2, 'max_columns' => 5 ),
    ) );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    register_nav_menus( array(
        'primary' => __( 'فهرست اصلی', 'bazarnama' ),
        'footer'  => __( 'فهرست پایین صفحه', 'bazarnama' ),
    ) );
}
add_action( 'after_setup_theme', 'bazarnama_setup' );

function bazarnama_assets() {
    wp_enqueue_style( 'bazarnama-style', get_stylesheet_uri(), array(), BAZARNAMA_VERSION );
    wp_enqueue_style( 'bazarnama-design-v2', get_template_directory_uri() . '/assets/css/design-v2.css', array( 'bazarnama-style' ), BAZARNAMA_VERSION );
    wp_enqueue_script( 'bazarnama-main', get_template_directory_uri() . '/assets/js/main.js', array(), BAZARNAMA_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'bazarnama_assets' );

function bazarnama_shop_url() {
    if ( class_exists( 'WooCommerce' ) ) {
        $url = wc_get_page_permalink( 'shop' );
        if ( $url ) {
            return $url;
        }
    }
    return home_url( '/' );
}

function bazarnama_cart_count() {
    return ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
}

function bazarnama_cart_url() {
    return function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
}

function bazarnama_account_url() {
    return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
}

/**
 * Output the theme-owned product grid, using actual published WC products.
 */
function bazarnama_product_grid( $args = array() ) {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return false;
    }
    $query = new WP_Query( array_merge( array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 5,
        'no_found_rows'  => true,
    ), $args ) );
    if ( ! $query->have_posts() ) {
        wp_reset_postdata();
        return false;
    }
    $previous_loop = function_exists( 'wc_get_loop_prop' ) ? wc_get_loop_prop( 'columns' ) : 4;
    if ( function_exists( 'wc_set_loop_prop' ) ) {
        wc_set_loop_prop( 'columns', 5 );
    }
    echo '<ul class="products columns-5">';
    while ( $query->have_posts() ) {
        $query->the_post();
        wc_get_template_part( 'content', 'product' );
    }
    echo '</ul>';
    wp_reset_postdata();
    if ( function_exists( 'wc_set_loop_prop' ) ) {
        wc_set_loop_prop( 'columns', $previous_loop );
    }
    return true;
}

function bazarnama_customize( $wp_customize ) {
    $wp_customize->add_section( 'bazarnama_home', array(
        'title'    => __( 'تنظیمات صفحه اصلی فروشگاه', 'bazarnama' ),
        'priority' => 35,
    ) );
    $fields = array(
        'notice'       => array( 'اطلاعیه بالای سایت', 'ارسال سریع و خرید آسان از فروشگاه ما' ),
        'hero_title'   => array( 'عنوان بنر اصلی', 'خریدی جذاب برای هر سلیقه' ),
        'hero_body'    => array( 'متن بنر اصلی', 'جدیدترین محصولات را با خیال راحت انتخاب کنید.' ),
        'hero_button'  => array( 'متن دکمه بنر', 'مشاهده محصولات' ),
    );
    foreach ( $fields as $key => $value ) {
        $id = 'bazarnama_' . $key;
        $wp_customize->add_setting( $id, array(
            'default'           => $value[1],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( $id, array(
            'label'   => $value[0],
            'section' => 'bazarnama_home',
            'type'    => 'text',
        ) );
    }
}
add_action( 'customize_register', 'bazarnama_customize' );

function bazarnama_nav_fallback() {
    echo '<ul class="primary-menu">';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'خانه', 'bazarnama' ) . '</a></li>';
    echo '<li><a href="' . esc_url( bazarnama_shop_url() ) . '">' . esc_html__( 'محصولات', 'bazarnama' ) . '</a></li>';
    echo '</ul>';
}

/**
 * WooCommerce shop columns (other than the custom home section).
 */
function bazarnama_loop_columns() {
    return 4;
}
add_filter( 'loop_shop_columns', 'bazarnama_loop_columns', 20 );

/** Editable visual elements for phase two. */
require_once get_template_directory() . '/inc/design-customizer.php';
