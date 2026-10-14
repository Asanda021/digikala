<?php
/** Phase 4: search, catalog filters, wishlist and compare. @package Bazarnama */
defined( 'ABSPATH' ) || exit;

function bazarnama_discovery_assets() {
    if ( ! class_exists( 'WooCommerce' ) ) { return; }
    wp_enqueue_style( 'bazarnama-discovery', get_template_directory_uri() . '/assets/css/discovery.css', array( 'bazarnama-commerce' ), BAZARNAMA_VERSION );
    wp_enqueue_script( 'bazarnama-discovery', get_template_directory_uri() . '/assets/js/discovery.js', array(), BAZARNAMA_VERSION, true );
    wp_localize_script( 'bazarnama-discovery', 'bazarnamaDiscovery', array(
        'ajax' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'bazarnama_discovery' ),
        'shop' => bazarnama_shop_url(),
        'labels' => array(
            'wishlist' => __( 'علاقه‌مندی', 'bazarnama' ),
            'compare' => __( 'مقایسه', 'bazarnama' ),
            'emptyWishlist' => __( 'هنوز محصولی به علاقه‌مندی‌ها اضافه نشده است.', 'bazarnama' ),
            'emptyCompare' => __( 'برای مقایسه، حداکثر چهار کالا انتخاب کنید.', 'bazarnama' ),
            'limitCompare' => __( 'حداکثر چهار کالا قابل مقایسه است.', 'bazarnama' ),
            'limitWishlist' => __( 'حداکثر چهل کالا قابل ذخیره است.', 'bazarnama' ),
            'error' => __( 'دریافت اطلاعات ممکن نشد. دوباره امتحان کنید.', 'bazarnama' ),
            'noResults' => __( 'محصولی پیدا نشد.', 'bazarnama' ),
        ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'bazarnama_discovery_assets', 22 );

function bazarnama_discovery_nonce() {
    if ( ! check_ajax_referer( 'bazarnama_discovery', 'nonce', false ) || ! class_exists( 'WooCommerce' ) ) {
        wp_send_json_error( array( 'message' => 'درخواست نامعتبر است.' ), 403 );
    }
}
function bazarnama_discovery_product( $product, $attributes = false ) {
    $image_id = $product->get_image_id();
    $properties = array();
    if ( $attributes ) {
        foreach ( array_slice( $product->get_attributes(), 0, 12 ) as $attribute ) {
            if ( ! $attribute->get_visible() ) { continue; }
            $value = wp_strip_all_tags( $product->get_attribute( $attribute->get_name() ) );
            if ( '' !== $value ) {
                $properties[] = array( 'name' => wp_strip_all_tags( wc_attribute_label( $attribute->get_name() ) ), 'value' => $value );
            }
        }
    }
    return array(
        'id' => $product->get_id(),
        'title' => $product->get_name(),
        'url' => get_permalink( $product->get_id() ),
        'image' => esc_url_raw( $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src() ),
        'price' => html_entity_decode( wp_strip_all_tags( $product->get_price_html() ), ENT_QUOTES, 'UTF-8' ),
        'stock' => $product->is_in_stock() ? __( 'موجود', 'bazarnama' ) : __( 'ناموجود', 'bazarnama' ),
        'sku' => $product->get_sku(),
        'attributes' => $properties,
    );
}
function bazarnama_ajax_search() {
    bazarnama_discovery_nonce();
    $raw = isset( $_POST['term'] ) && is_string( $_POST['term'] ) ? wp_unslash( $_POST['term'] ) : '';
    $term = sanitize_text_field( $raw );
    $len = function_exists( 'mb_strlen' ) ? mb_strlen( $term, 'UTF-8' ) : strlen( $term );
    if ( $len < 2 ) { wp_send_json_success( array( 'products' => array() ) ); }
    $term = function_exists( 'mb_substr' ) ? mb_substr( $term, 0, 80, 'UTF-8' ) : substr( $term, 0, 240 );
    $posts = get_posts( array(
        'post_type' => 'product', 'post_status' => 'publish', 's' => $term,
        'posts_per_page' => 6, 'suppress_filters' => false,
    ) );
    $items = array();
    foreach ( $posts as $post ) {
        $product = wc_get_product( $post->ID );
        if ( $product instanceof WC_Product && $product->is_visible() ) {
            $items[] = bazarnama_discovery_product( $product );
        }
    }
    wp_send_json_success( array( 'products' => $items ) );
}
add_action( 'wp_ajax_bazarnama_search', 'bazarnama_ajax_search' );
add_action( 'wp_ajax_nopriv_bazarnama_search', 'bazarnama_ajax_search' );
function bazarnama_ajax_products() {
    bazarnama_discovery_nonce();
    $raw_ids = isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ? wp_unslash( $_POST['ids'] ) : array();
    $ids = array_slice( array_unique( array_filter( array_map( 'absint', $raw_ids ) ) ), 0, 40 );
    $items = array();
    foreach ( $ids as $id ) {
        $product = wc_get_product( $id );
        if ( $product instanceof WC_Product && 'publish' === $product->get_status() && $product->is_visible() ) {
            $items[] = bazarnama_discovery_product( $product, true );
        }
    }
    wp_send_json_success( array( 'products' => $items ) );
}
add_action( 'wp_ajax_bazarnama_products', 'bazarnama_ajax_products' );
add_action( 'wp_ajax_nopriv_bazarnama_products', 'bazarnama_ajax_products' );

function bazarnama_filter_value( $key ) {
    return isset( $_GET[ $key ] ) && is_scalar( $_GET[ $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) : '';
}
function bazarnama_filter_catalog( $query ) {
    if ( is_admin() || ! ( is_shop() || is_product_category() || is_product_tag() ) ) { return; }
    $min = bazarnama_filter_value( 'bn_min_price' );
    $max = bazarnama_filter_value( 'bn_max_price' );
    $meta = (array) $query->get( 'meta_query' );
    if ( '' !== $min && is_numeric( $min ) && (float) $min >= 0 ) {
        $meta[] = array( 'key' => '_price', 'value' => (float) wc_format_decimal( $min ), 'compare' => '>=', 'type' => 'DECIMAL(18,6)' );
    }
    if ( '' !== $max && is_numeric( $max ) && (float) $max >= 0 ) {
        $meta[] = array( 'key' => '_price', 'value' => (float) wc_format_decimal( $max ), 'compare' => '<=', 'type' => 'DECIMAL(18,6)' );
    }
    if ( '1' === bazarnama_filter_value( 'bn_stock' ) ) {
        $meta[] = array( 'key' => '_stock_status', 'value' => 'instock' );
    }
    if ( $meta ) { $query->set( 'meta_query', $meta ); }
    if ( '1' === bazarnama_filter_value( 'bn_sale' ) ) {
        $sale = array_map( 'absint', (array) wc_get_product_ids_on_sale() );
        $current = array_filter( array_map( 'absint', (array) $query->get( 'post__in' ) ) );
        $allowed = $current ? array_intersect( $current, $sale ) : $sale;
        $query->set( 'post__in', $allowed ? array_values( $allowed ) : array( 0 ) );
    }
}
add_action( 'woocommerce_product_query', 'bazarnama_filter_catalog', 30 );
function bazarnama_catalog_filters() {
    if ( ! ( is_shop() || is_product_category() || is_product_tag() ) ) { return; }
    $action = bazarnama_shop_url();
    if ( is_product_category() || is_product_tag() ) {
        $url = get_term_link( get_queried_object() );
        if ( ! is_wp_error( $url ) ) { $action = $url; }
    }
    ?>
    <form class="bn-catalog-filters" method="get" action="<?php echo esc_url( $action ); ?>" aria-label="<?php esc_attr_e( 'فیلتر محصولات', 'bazarnama' ); ?>">
        <label><?php esc_html_e( 'از قیمت', 'bazarnama' ); ?><input type="number" min="0" step="any" inputmode="decimal" name="bn_min_price" value="<?php echo esc_attr( bazarnama_filter_value( 'bn_min_price' ) ); ?>"></label>
        <label><?php esc_html_e( 'تا قیمت', 'bazarnama' ); ?><input type="number" min="0" step="any" inputmode="decimal" name="bn_max_price" value="<?php echo esc_attr( bazarnama_filter_value( 'bn_max_price' ) ); ?>"></label>
        <label class="bn-filter-check"><input type="checkbox" name="bn_stock" value="1" <?php checked( '1', bazarnama_filter_value( 'bn_stock' ) ); ?>><?php esc_html_e( 'فقط موجودها', 'bazarnama' ); ?></label>
        <label class="bn-filter-check"><input type="checkbox" name="bn_sale" value="1" <?php checked( '1', bazarnama_filter_value( 'bn_sale' ) ); ?>><?php esc_html_e( 'تخفیف‌دار', 'bazarnama' ); ?></label>
        <button type="submit"><?php esc_html_e( 'اعمال فیلتر', 'bazarnama' ); ?></button>
        <a href="<?php echo esc_url( $action ); ?>"><?php esc_html_e( 'پاک کردن', 'bazarnama' ); ?></a>
    </form>
    <?php
}
add_action( 'woocommerce_before_shop_loop', 'bazarnama_catalog_filters', 5 );
add_action( 'woocommerce_no_products_found', 'bazarnama_catalog_filters', 5 );

function bazarnama_single_collections() {
    global $product;
    if ( ! $product instanceof WC_Product ) { return; }
    ?>
    <div class="bn-single-collections">
        <button type="button" class="bn-collection-btn" data-bn-collection="wishlist" data-product-id="<?php echo esc_attr( (string) $product->get_id() ); ?>" aria-pressed="false"><?php esc_html_e( '♡ علاقه‌مندی', 'bazarnama' ); ?></button>
        <button type="button" class="bn-collection-btn" data-bn-collection="compare" data-product-id="<?php echo esc_attr( (string) $product->get_id() ); ?>" aria-pressed="false"><?php esc_html_e( '⊞ مقایسه', 'bazarnama' ); ?></button>
    </div>
    <?php
}
add_action( 'woocommerce_single_product_summary', 'bazarnama_single_collections', 35 );

function bazarnama_collection_template( $template ) {
    if ( is_admin() || ! class_exists( 'WooCommerce' ) || ! is_front_page() ) { return $template; }
    if ( in_array( bazarnama_filter_value( 'bn_view' ), array( 'wishlist', 'compare', 'track' ), true ) ) {
        return get_template_directory() . '/templates/collection.php';
    }
    return $template;
}
add_filter( 'template_include', 'bazarnama_collection_template', 25 );
