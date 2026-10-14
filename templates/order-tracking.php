<?php
/**
 * Native WooCommerce guest order lookup.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="primary" class="site-main">
    <div class="container content-shell bn-tracking">
        <span class="bn-tracking-kicker"><?php esc_html_e( 'پیگیری خرید', 'bazarnama' ); ?></span>
        <h1><?php esc_html_e( 'پیگیری سفارش', 'bazarnama' ); ?></h1>
        <p><?php esc_html_e( 'برای اطلاع از وضعیت سفارش، شماره سفارش و ایمیل ثبت‌شده هنگام خرید را وارد کنید.', 'bazarnama' ); ?></p>
        <?php
        if ( class_exists( 'WooCommerce' ) && shortcode_exists( 'woocommerce_order_tracking' ) ) {
            echo do_shortcode( '[woocommerce_order_tracking]' );
        } else {
            echo '<p>' . esc_html__( 'فرم پیگیری ووکامرس در دسترس نیست. لطفاً از بخش سفارش‌های حساب کاربری استفاده کنید.', 'bazarnama' ) . '</p>';
        }
        ?>
        <p><a href="<?php echo esc_url( bazarnama_account_url() ); ?>"><?php esc_html_e( 'مشاهده سفارش‌ها در حساب کاربری ←', 'bazarnama' ); ?></a></p>
    </div>
</main>
<?php get_footer(); ?>
