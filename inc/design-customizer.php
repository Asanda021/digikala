<?php
/**
 * Phase 2 editable marketing content.
 *
 * @package Bazarnama
 */
defined( 'ABSPATH' ) || exit;

function bazarnama_phase2_customizer( $wp_customize ) {
    $fields = array(
        'hero_link'       => array( 'نشانی پیوند بنر اصلی', '', 'url' ),
        'promo_one_title' => array( 'عنوان تبلیغ اول', 'انتخاب‌های تازه برای شما', 'text' ),
        'promo_one_link'  => array( 'نشانی پیوند تبلیغ اول', '', 'url' ),
        'promo_two_title' => array( 'عنوان تبلیغ دوم', 'خرید ساده، انتخاب بهتر', 'text' ),
        'promo_two_link'  => array( 'نشانی پیوند تبلیغ دوم', '', 'url' ),
    );
    foreach ( $fields as $name => $info ) {
        $setting = 'bazarnama_' . $name;
        $wp_customize->add_setting( $setting, array(
            'default'           => $info[1],
            'sanitize_callback' => 'url' === $info[2] ? 'esc_url_raw' : 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( $setting, array(
            'label'   => $info[0],
            'section' => 'bazarnama_home',
            'type'    => $info[2],
        ) );
    }
}
add_action( 'customize_register', 'bazarnama_phase2_customizer', 15 );
