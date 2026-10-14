<?php
/**
 * Global footer.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;
?>
<footer class="site-footer" id="colophon">
    <div class="container">
        <div class="footer-toolbar">
            <span class="footer-brand-mini"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
            <button class="back-to-top" type="button"><?php esc_html_e( 'بازگشت به بالا ↑', 'bazarnama' ); ?></button>
        </div>
        <div class="footer-layout">
            <div class="footer-about">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-brand"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
                <p><?php echo esc_html( get_bloginfo( 'description' ) ?: 'فروشگاه اینترنتی شما، با تجربه خرید ساده و دلپذیر.' ); ?></p>
            </div>
            <div class="footer-menu">
                <h3><?php esc_html_e( 'دسترسی سریع', 'bazarnama' ); ?></h3>
                <?php if ( has_nav_menu( 'footer' ) ) : ?>
                    <?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 1 ) ); ?>
                <?php else : ?>
                    <ul><li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحه اصلی', 'bazarnama' ); ?></a></li><li><a href="<?php echo esc_url( bazarnama_shop_url() ); ?>"><?php esc_html_e( 'فروشگاه', 'bazarnama' ); ?></a></li></ul>
                <?php endif; ?>
            </div>
            <div class="footer-menu">
                <h3><?php esc_html_e( 'حساب و سفارش‌ها', 'bazarnama' ); ?></h3>
                <ul>
                    <li><a href="<?php echo esc_url( bazarnama_account_url() ); ?>"><?php esc_html_e( 'حساب من', 'bazarnama' ); ?></a></li>
                    <li><a href="<?php echo esc_url( bazarnama_cart_url() ); ?>"><?php esc_html_e( 'سبد خرید', 'bazarnama' ); ?></a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom"><?php echo esc_html( '© ' . wp_date( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'تمام حقوق محفوظ است.', 'bazarnama' ); ?></div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
