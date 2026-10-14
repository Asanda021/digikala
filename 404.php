<?php
/**
 * Not found.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="primary" class="site-main"><div class="container content-shell" style="text-align:center">
    <h1><?php esc_html_e( 'صفحه موردنظر پیدا نشد', 'bazarnama' ); ?></h1>
    <p><?php esc_html_e( 'ممکن است نشانی صفحه تغییر کرده باشد.', 'bazarnama' ); ?></p>
    <a class="button-light" style="background:#ef4056;color:#fff" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'بازگشت به خانه', 'bazarnama' ); ?></a>
</div></main>
<?php get_footer(); ?>
