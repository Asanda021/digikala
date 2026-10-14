<?php
/**
 * Accessible WooCommerce loop card. Core cart actions remain authoritative.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;
global $product;
if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
    return;
}
$product_id = $product->get_id();
$in_stock   = $product->is_in_stock();
?>
<li <?php wc_product_class( 'bazarnama-product-card', $product ); ?> data-product-id="<?php echo esc_attr( (string) $product_id ); ?>">
    <a class="product-card-link" href="<?php echo esc_url( get_permalink( $product_id ) ); ?>">
        <div class="product-media">
            <?php if ( $product->is_on_sale() ) : ?><span class="product-sale-pill"><?php esc_html_e( 'تخفیف', 'bazarnama' ); ?></span><?php endif; ?>
            <?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ) ); ?>
        </div>
        <h2 class="woocommerce-loop-product__title product-title"><?php echo esc_html( $product->get_name() ); ?></h2>
        <?php if ( wc_review_ratings_enabled() && $product->get_rating_count() > 0 ) : ?>
            <div class="product-rating" aria-label="<?php echo esc_attr( sprintf( 'امتیاز %s از ۵', wc_format_decimal( $product->get_average_rating(), 1 ) ) ); ?>">★ <?php echo esc_html( wc_format_decimal( $product->get_average_rating(), 1 ) ); ?></div>
        <?php endif; ?>
        <div class="product-card-price price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
        <span class="bn-stock-state <?php echo $in_stock ? 'available' : 'unavailable'; ?>"><?php echo esc_html( $in_stock ? __( 'موجود', 'bazarnama' ) : __( 'ناموجود', 'bazarnama' ) ); ?></span>
    </a>
    <div class="product-card-actions"><?php woocommerce_template_loop_add_to_cart(); ?></div>
</li>
