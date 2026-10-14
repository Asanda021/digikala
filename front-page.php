<?php
/**
 * Storefront homepage.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;
get_header();
$shop_url = bazarnama_shop_url();
?>
<main id="primary" class="site-main home-main">
    <div class="container">
        <section class="home-hero" aria-label="<?php esc_attr_e( 'پیشنهادهای فروشگاه', 'bazarnama' ); ?>">
            <div class="hero-primary">
                <div class="hero-copy">
                    <span class="hero-eyebrow"><?php esc_html_e( 'خوش آمدید به فروشگاه ما', 'bazarnama' ); ?></span>
                    <h1><?php echo esc_html( get_theme_mod( 'bazarnama_hero_title', 'خریدی جذاب برای هر سلیقه' ) ); ?></h1>
                    <p><?php echo esc_html( get_theme_mod( 'bazarnama_hero_body', 'جدیدترین محصولات را با خیال راحت انتخاب کنید.' ) ); ?></p>
                    <a class="button-light" href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html( get_theme_mod( 'bazarnama_hero_button', 'مشاهده محصولات' ) ); ?> ←</a>
                </div>
                <div class="hero-art" aria-hidden="true"><div class="hero-box"></div></div>
            </div>
            <div class="hero-side">
                <a class="promo-tile promo-violet" href="<?php echo esc_url( $shop_url ); ?>">
                    <strong><?php esc_html_e( 'انتخاب‌های تازه برای شما', 'bazarnama' ); ?></strong>
                    <span><?php esc_html_e( 'کشف محصولات فروشگاه ←', 'bazarnama' ); ?></span>
                </a>
                <a class="promo-tile promo-mint" href="<?php echo esc_url( $shop_url ); ?>">
                    <strong><?php esc_html_e( 'خرید ساده، انتخاب بهتر', 'bazarnama' ); ?></strong>
                    <span><?php esc_html_e( 'دیدن همه کالاها ←', 'bazarnama' ); ?></span>
                </a>
            </div>
        </section>

        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
            <?php
            $categories = get_terms( array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
                'number'     => 8,
                'orderby'    => 'count',
                'order'      => 'DESC',
                'exclude'    => array( (int) get_option( 'default_product_cat', 0 ) ),
            ) );
            ?>
            <?php if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) : ?>
            <section class="section-block" aria-labelledby="cat-title">
                <div class="section-heading"><h2 id="cat-title"><?php esc_html_e( 'دسته‌بندی‌های محبوب', 'bazarnama' ); ?></h2><a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'تمام محصولات ←', 'bazarnama' ); ?></a></div>
                <div class="category-grid">
                <?php foreach ( $categories as $category ) : ?>
                    <?php
                    $link = get_term_link( $category );
                    if ( is_wp_error( $link ) ) {
                        continue;
                    }
                    $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
                    ?>
                    <a class="category-item" href="<?php echo esc_url( $link ); ?>">
                        <span class="category-circle">
                            <?php if ( $thumbnail_id ) : ?>
                                <?php echo wp_get_attachment_image( $thumbnail_id, 'thumbnail', false, array( 'loading' => 'lazy', 'alt' => $category->name ) ); ?>
                            <?php else : ?>
                                <span class="category-glyph" aria-hidden="true">◈</span>
                            <?php endif; ?>
                        </span>
                        <span><?php echo esc_html( $category->name ); ?></span>
                    </a>
                <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php
            $sale_ids = wc_get_product_ids_on_sale();
            $sale_ids = array_values( array_filter( array_map( 'absint', (array) $sale_ids ) ) );
            $sale_ids = array_slice( $sale_ids, 0, 30 );
            ?>
            <?php if ( $sale_ids ) : ?>
            <section class="sale-band" aria-labelledby="sale-title">
                <div class="sale-intro"><span class="sale-symbol" aria-hidden="true">✦</span><strong id="sale-title"><?php esc_html_e( 'پیشنهاد شگفت‌انگیز', 'bazarnama' ); ?></strong><a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'دیدن همه ←', 'bazarnama' ); ?></a></div>
                <div><?php bazarnama_product_grid( array( 'post__in' => $sale_ids, 'orderby' => 'post__in', 'posts_per_page' => 4 ) ); ?></div>
            </section>
            <?php endif; ?>

            <section class="section-block" aria-labelledby="new-title">
                <div class="section-heading"><h2 id="new-title"><?php esc_html_e( 'جدیدترین کالاها', 'bazarnama' ); ?></h2><a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'مشاهده همه ←', 'bazarnama' ); ?></a></div>
                <div class="products-shell">
                    <?php
                    if ( ! bazarnama_product_grid( array( 'posts_per_page' => 10 ) ) ) :
                        echo '<div class="no-products">' . esc_html__( 'هنوز محصولی منتشر نشده است. پس از افزودن محصولات در ووکامرس، این بخش به‌صورت خودکار نمایش داده می‌شود.', 'bazarnama' ) . '</div>';
                    endif;
                    ?>
                </div>
            </section>
        <?php else : ?>
            <div class="site-notice"><?php esc_html_e( 'قالب آماده است. برای فعال شدن فروشگاه، افزونه رایگان ووکامرس را نصب و فعال کنید.', 'bazarnama' ); ?></div>
        <?php endif; ?>

        <section class="trust-strip" aria-label="<?php esc_attr_e( 'ویژگی‌های فروشگاه', 'bazarnama' ); ?>">
            <div class="trust-item"><span class="trust-icon" aria-hidden="true">📦</span><span><?php esc_html_e( 'پیگیری سفارش‌ها', 'bazarnama' ); ?></span></div>
            <div class="trust-item"><span class="trust-icon" aria-hidden="true">💳</span><span><?php esc_html_e( 'پشتیبانی از پرداخت ووکامرس', 'bazarnama' ); ?></span></div>
            <div class="trust-item"><span class="trust-icon" aria-hidden="true">🔎</span><span><?php esc_html_e( 'جست‌وجوی محصولات', 'bazarnama' ); ?></span></div>
            <div class="trust-item"><span class="trust-icon" aria-hidden="true">🛒</span><span><?php esc_html_e( 'سبد خرید و ثبت سفارش', 'bazarnama' ); ?></span></div>
        </section>
    </div>
</main>
<?php get_footer(); ?>
