<?php
/**
 * Phase 3: WooCommerce native catalog and purchasing integration.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;

/** Render small responsive styling and default WooCommerce notices. */
function bazarnama_commerce_assets() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }
    wp_enqueue_style( 'bazarnama-commerce', get_template_directory_uri() . '/assets/css/commerce.css', array( 'bazarnama-design-v2' ), BAZARNAMA_VERSION );
    // Keep the AJAX add-to-cart header count accurate, including WooCommerce's
    // newer conditional loading of cart fragments.
    if ( wp_script_is( 'wc-cart-fragments', 'registered' ) ) {
        wp_enqueue_script( 'wc-cart-fragments' );
    }
}
add_action( 'wp_enqueue_scripts', 'bazarnama_commerce_assets', 20 );

/** Update the same markup selector used by the header. */
function bazarnama_cart_fragments( $fragments ) {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return $fragments;
    }
    $fragments['span.cart-count'] = '<span class="cart-count">' . esc_html( (string) WC()->cart->get_cart_contents_count() ) . '</span>';
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'bazarnama_cart_fragments' );

/**
 * Use WooCommerce's product data API rather than assuming products always live
 * in the WP posts database. IDs are handed to core's product card template.
 */
function bazarnama_featured_products( $limit = 8, $sale_only = false ) {
    if ( ! function_exists( 'wc_get_products' ) ) {
        return array();
    }
    $args = array( 'status' => 'publish', 'limit' => min( 16, max( 1, absint( $limit ) ) ), 'orderby' => 'date', 'order' => 'DESC' );
    if ( $sale_only ) {
        $ids = array_map( 'absint', (array) wc_get_product_ids_on_sale() );
        if ( ! $ids ) {
            return array();
        }
        $args['include'] = $ids;
    }
    return array_values( array_filter( wc_get_products( $args ), function( $product ) {
        return $product instanceof WC_Product && $product->is_visible();
    } ) );
}

/** Visual product information; fulfillment terms stay controlled by WooCommerce. */
function bazarnama_single_product_stock_information() {
    global $product;
    if ( ! $product instanceof WC_Product ) {
        return;
    }
    echo '<div class="bn-product-highlights">';
    echo '<span><span aria-hidden="true">✓</span> ' . esc_html__( 'اطلاعات موجودی', 'bazarnama' ) . ': <strong>' . esc_html( $product->is_in_stock() ? __( 'موجود', 'bazarnama' ) : __( 'ناموجود', 'bazarnama' ) ) . '</strong></span>';
    if ( $product->get_sku() ) {
        echo '<span>' . esc_html__( 'شناسه کالا:', 'bazarnama' ) . ' <bdi>' . esc_html( $product->get_sku() ) . '</bdi></span>';
    }
    echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'bazarnama_single_product_stock_information', 25 );

/** The native WooCommerce checkout, cart and account own order mutations. */
function bazarnama_checkout_caption() {
    if ( function_exists( 'is_checkout' ) && is_checkout() && ! is_order_received_page() ) {
        echo '<div class="bn-step-note">' . esc_html__( 'ثبت سفارش و پرداخت از طریق مراحل امن ووکامرس و روش‌های فعال فروشگاه انجام می‌شود.', 'bazarnama' ) . '</div>';
    }
}
add_action( 'woocommerce_before_checkout_form', 'bazarnama_checkout_caption', 5 );

/** Native WooCommerce add-to-cart is unavailable before WC activation. */
function bazarnama_single_product_related_title( $title ) {
    return __( 'کالاهای مرتبط', 'bazarnama' );
}
add_filter( 'woocommerce_product_related_products_heading', 'bazarnama_single_product_related_title' );
