<?php
/**
 * WooCommerce content wrapper.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="primary" class="site-main wc-main">
    <div class="container">
        <?php woocommerce_content(); ?>
    </div>
</main>
<?php get_footer(); ?>
