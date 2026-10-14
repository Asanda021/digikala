<?php
/**
 * Shop loop product card.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;
global $product;
if ( ! $product || ! $product->is_visible() ) {
    return;
}
?>
<li <?php wc_product_class( 'bazarnama-product-card', $product ); ?>>
    <a class="product-card-link" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
        <div class="product-media">
            <?php if ( $product->is_on_sale() ) : ?>
                <span class="product-sale-pill"><?php esc_html_e( 'ویژه', 'bazarnama' ); ?></span>
            <?php endif; ?>
            <?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ) ); ?>
        </div>
        <h2 class="woocommerce-loop-product__title product-title"><?php echo esc_html( $product->get_name() ); ?></h2>
        <?php if ( wc_review_ratings_enabled() && $product->get_rating_count() > 0 ) : ?>
            <div class="product-rating">★ <?php echo esc_html( wc_format_decimal( $product->get_average_rating(), 1 ) ); ?></div>
        <?php endif; ?>
        <div class="product-card-price price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
    </a>
    <div class="product-card-actions"><?php woocommerce_template_loop_add_to_cart(); ?></div>
</li>
