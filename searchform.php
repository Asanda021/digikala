<?php
/**
 * Generic blog search.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label><span class="screen-reader-text"><?php esc_html_e( 'جست‌وجو', 'bazarnama' ); ?></span>
        <input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'جست‌وجو...', 'bazarnama' ); ?>" required>
    </label>
    <button type="submit"><?php esc_html_e( 'جست‌وجو', 'bazarnama' ); ?></button>
</form>
