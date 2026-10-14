<?php
/** Editable Persian design settings; no external page builder. @package Bazarnama */
defined( 'ABSPATH' ) || exit;

function bazarnama_design_choice( $value, $setting ) {
    $choices = array(
        'bazarnama_container_width' => array( '1140', '1280', '1360' ),
        'bazarnama_card_radius' => array( '8', '12', '16', '22' ),
    );
    $key = $setting->id;
    return isset( $choices[ $key ] ) && in_array( (string) $value, $choices[ $key ], true )
        ? (string) $value : ( 'bazarnama_card_radius' === $key ? '16' : '1280' );
}
function bazarnama_design_media( $value ) {
    $id = absint( $value );
    return $id && wp_attachment_is_image( $id ) ? $id : 0;
}
function bazarnama_register_design_options( $customize ) {
    $customize->add_section( 'bazarnama_design', array(
        'title' => __( 'طراحی و چیدمان بازارنما', 'bazarnama' ), 'priority' => 36,
    ) );
    $customize->add_setting( 'bazarnama_accent_color', array(
        'default' => '#ef4056', 'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $customize->add_control( new WP_Customize_Color_Control( $customize, 'bazarnama_accent_color', array(
        'section' => 'bazarnama_design', 'label' => __( 'رنگ اصلی', 'bazarnama' ),
    ) ) );
    foreach ( array(
        'bazarnama_container_width' => array(
            'پهنای محتوای سایت', '1280', array( '1140' => 'فشرده', '1280' => 'معمولی', '1360' => 'عریض' ),
        ),
        'bazarnama_card_radius' => array(
            'گردی کارت‌ها', '16', array( '8' => 'کم', '12' => 'متوسط', '16' => 'معمولی', '22' => 'زیاد' ),
        ),
    ) as $key => $data ) {
        $customize->add_setting( $key, array(
            'default' => $data[1], 'sanitize_callback' => 'bazarnama_design_choice',
        ) );
        $customize->add_control( $key, array(
            'label' => $data[0], 'section' => 'bazarnama_design',
            'type' => 'select', 'choices' => $data[2],
        ) );
    }
    foreach ( array(
        'bazarnama_sticky_header' => array( 'سربرگ ثابت', false ),
        'bazarnama_show_intro' => array( 'نمایش متن بالای بنر', true ),
        'bazarnama_show_categories' => array( 'نمایش دسته‌بندی‌های اصلی', true ),
        'bazarnama_show_sale' => array( 'نمایش بخش تخفیف‌دار', true ),
    ) as $key => $data ) {
        $customize->add_setting( $key, array(
            'default' => $data[1], 'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        $customize->add_control( $key, array(
            'label' => $data[0], 'section' => 'bazarnama_design', 'type' => 'checkbox',
        ) );
    }
    $customize->add_setting( 'bazarnama_hero_image', array(
        'default' => 0, 'sanitize_callback' => 'bazarnama_design_media',
    ) );
    $customize->add_control( new WP_Customize_Media_Control( $customize, 'bazarnama_hero_image', array(
        'section' => 'bazarnama_design',
        'label' => __( 'تصویر بنر اصلی', 'bazarnama' ),
        'mime_type' => 'image',
    ) ) );
    $customize->add_setting( 'bazarnama_footer_description', array(
        'default' => 'فروشگاه اینترنتی شما، با تجربه خرید ساده و دلپذیر.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $customize->add_control( 'bazarnama_footer_description', array(
        'label' => __( 'متن معرفی در پابرگ', 'bazarnama' ),
        'section' => 'bazarnama_design', 'type' => 'textarea',
    ) );
}
add_action( 'customize_register', 'bazarnama_register_design_options', 20 );

function bazarnama_dynamic_design_styles() {
    $color = sanitize_hex_color( get_theme_mod( 'bazarnama_accent_color', '#ef4056' ) );
    if ( ! $color ) { $color = '#ef4056'; }
    $width = (string) get_theme_mod( 'bazarnama_container_width', '1280' );
    if ( ! in_array( $width, array( '1140', '1280', '1360' ), true ) ) { $width = '1280'; }
    $radius = (string) get_theme_mod( 'bazarnama_card_radius', '16' );
    if ( ! in_array( $radius, array( '8', '12', '16', '22' ), true ) ) { $radius = '16'; }
    $css = ':root{--brand:' . $color . ';--radius:' . $radius . 'px}'
        . '.container{max-width:' . $width . 'px}'
        . '.brand-mark,.cart-count,.bn-catalog-filters button{background:' . $color . '!important}'
        . '.brand,.section-heading a,.footer-brand-mini{color:' . $color . '}'
        . '.category-panel,.content-shell,.products-shell{border-radius:var(--radius)}';
    if ( get_theme_mod( 'bazarnama_sticky_header', false ) ) {
        $css .= '.site-header{position:sticky;top:0;z-index:99}body.admin-bar .site-header{top:32px}'
            . '@media(max-width:782px){body.admin-bar .site-header{top:46px}}';
    }
    wp_add_inline_style( 'bazarnama-settings', $css );
}
add_action( 'wp_enqueue_scripts', 'bazarnama_dynamic_design_styles', 30 );
