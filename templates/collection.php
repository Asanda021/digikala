<?php
/** Store collections backed by this browser only. @package Bazarnama */
defined( 'ABSPATH' ) || exit;
$view = bazarnama_filter_value( 'bn_view' );
if ( ! in_array( $view, array( 'wishlist', 'compare' ), true ) ) {
    status_header( 404 );
    get_template_part( '404' );
    return;
}
$title = 'wishlist' === $view ? __( 'علاقه‌مندی‌های من', 'bazarnama' ) : __( 'مقایسه محصولات', 'bazarnama' );
get_header();
?>
<main id="primary" class="site-main">
    <div class="container content-shell bn-collection-page" data-bn-page="<?php echo esc_attr( $view ); ?>">
        <div class="bn-collection-head"><h1><?php echo esc_html( $title ); ?></h1><a href="<?php echo esc_url( bazarnama_shop_url() ); ?>"><?php esc_html_e( 'بازگشت به فروشگاه ←', 'bazarnama' ); ?></a></div>
        <p class="bn-browser-note"><?php esc_html_e( 'این فهرست فقط در همین مرورگر نگهداری می‌شود و بین دستگاه‌ها و حساب‌های کاربری همگام‌سازی نمی‌شود.', 'bazarnama' ); ?></p>
        <div id="bn-collection-notice" role="status" aria-live="polite"></div>
        <div id="bn-collection-results" class="bn-collection-results"><p><?php esc_html_e( 'در حال دریافت کالاها...', 'bazarnama' ); ?></p></div>
    </div>
</main>
<?php get_footer(); ?>
