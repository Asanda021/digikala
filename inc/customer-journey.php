<?php
/**
 * Phase 5: secure native WooCommerce checkout, order and account presentation.
 * NO custom payment gateway or direct order/meta mutation exists here.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;

function bazarnama_customer_assets() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }
    wp_enqueue_style( 'bazarnama-customer', get_template_directory_uri() . '/assets/css/customer-journey.css', array( 'bazarnama-discovery' ), BAZARNAMA_VERSION );
}
add_action( 'wp_enqueue_scripts', 'bazarnama_customer_assets', 23 );

function bazarnama_order_tracking_url() {
    return add_query_arg( 'bn_view', 'track', home_url( '/' ) );
}
/**
 * WooCommerce registers [woocommerce_order_tracking], and checks order ID plus
 * billing email. Rendering it preserves those authorization checks.
 */
function bazarnama_tracking_template( $template ) {
    if ( is_admin() || ! class_exists( 'WooCommerce' ) || ! is_front_page() ) {
        return $template;
    }
    if ( function_exists( 'bazarnama_filter_value' ) && 'track' === bazarnama_filter_value( 'bn_view' ) ) {
        return get_template_directory() . '/templates/order-tracking.php';
    }
    return $template;
}
add_filter( 'template_include', 'bazarnama_tracking_template', 35 );

/** Only show checkout stages; WooCommerce owns shipping and payment values. */
function bazarnama_purchase_steps() {
    $step = '';
    if ( function_exists( 'is_cart' ) && is_cart() ) {
        $step = 'cart';
    } elseif ( function_exists( 'is_checkout' ) && is_checkout() && ! is_order_received_page() ) {
        $step = 'checkout';
    }
    if ( ! $step ) { return; }
    $items = array(
        array( 'cart', __( 'سبد خرید', 'bazarnama' ), bazarnama_cart_url() ),
        array( 'checkout', __( 'اطلاعات و پرداخت', 'bazarnama' ), function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/' ) ),
        array( 'done', __( 'ثبت سفارش', 'bazarnama' ), '' ),
    );
    echo '<nav class="bn-purchase-steps" aria-label="' . esc_attr__( 'مراحل خرید', 'bazarnama' ) . '">';
    foreach ( $items as $i => $item ) {
        $current = $step === $item[0];
        $completed = 'checkout' === $step && 'cart' === $item[0];
        $class = $current ? ' is-current' : ( $completed ? ' is-complete' : '' );
        echo '<span class="bn-step' . esc_attr( $class ) . '"' . ( $current ? ' aria-current="step"' : '' ) . '>';
        echo '<span class="bn-step-number">' . esc_html( (string) ( $i + 1 ) ) . '</span><span>' . esc_html( $item[1] ) . '</span></span>';
    }
    echo '</nav>';
}
add_action( 'woocommerce_before_cart', 'bazarnama_purchase_steps', 1 );
add_action( 'woocommerce_before_checkout_form', 'bazarnama_purchase_steps', 1 );

/** The account area links to genuine WooCommerce endpoints, not dummy pages. */
function bazarnama_account_welcome() {
    if ( ! is_user_logged_in() ) {
        return;
    }
    echo '<div class="bn-account-dashboard">';
    echo '<h2>' . esc_html__( 'مدیریت حساب و سفارش‌ها', 'bazarnama' ) . '</h2>';
    echo '<p>' . esc_html__( 'برای مشاهده تاریخچه خرید و به‌روزرسانی اطلاعات خود از بخش‌های حساب کاربری استفاده کنید.', 'bazarnama' ) . '</p>';
    echo '<div class="bn-account-links">';
    echo '<a href="' . esc_url( wc_get_account_endpoint_url( 'orders' ) ) . '">' . esc_html__( 'سفارش‌های من', 'bazarnama' ) . ' ←</a>';
    echo '<a href="' . esc_url( wc_get_account_endpoint_url( 'edit-account' ) ) . '">' . esc_html__( 'ویرایش حساب', 'bazarnama' ) . ' ←</a>';
    echo '<a href="' . esc_url( bazarnama_order_tracking_url() ) . '">' . esc_html__( 'پیگیری سفارش با شماره و ایمیل', 'bazarnama' ) . ' ←</a>';
    echo '</div></div>';
}
add_action( 'woocommerce_account_dashboard', 'bazarnama_account_welcome', 5 );

/** Display link to core tracking after checkout; no unverified order details. */
function bazarnama_thankyou_followup( $order_id ) {
    if ( ! $order_id ) { return; }
    echo '<p class="bn-followup-note">' . esc_html__( 'برای پیگیری سفارش می‌توانید از حساب کاربری یا فرم پیگیری استفاده کنید.', 'bazarnama' ) . ' ';
    echo '<a href="' . esc_url( bazarnama_order_tracking_url() ) . '">' . esc_html__( 'فرم پیگیری سفارش', 'bazarnama' ) . '</a></p>';
}
add_action( 'woocommerce_thankyou', 'bazarnama_thankyou_followup', 20 );

/** Account and order pages must not be cached publicly by full-page caches. */
function bazarnama_customer_no_cache() {
    if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_setcookie' ) ) { return; }
    if ( ( function_exists( 'is_account_page' ) && is_account_page() ) || ( function_exists( 'is_checkout' ) && is_checkout() ) || ( function_exists( 'is_cart' ) && is_cart() ) ) {
        if ( ! defined( 'DONOTCACHEPAGE' ) ) { define( 'DONOTCACHEPAGE', true ); }
        nocache_headers();
    }
}
add_action( 'template_redirect', 'bazarnama_customer_no_cache', 1 );
