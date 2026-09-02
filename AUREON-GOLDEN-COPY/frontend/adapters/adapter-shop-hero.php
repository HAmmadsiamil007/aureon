<?php
/**
 * Shop hero adapter — page title from the active WC context.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_shop_hero() {
    $title = '';

    if ( is_product_category() ) {
        $title = single_cat_title( '', false );
    } elseif ( is_product_tag() ) {
        $title = single_tag_title( '', false );
    } elseif ( function_exists( 'woocommerce_page_title' ) ) {
        $title = woocommerce_page_title( false );
    }

    if ( ! $title && function_exists( 'wc_get_page_id' ) ) {
        $title = get_the_title( wc_get_page_id( 'shop' ) );
    }

    return array(
        'label'    => __( 'Collection', 'aureon' ),
        'title'    => $title ? $title : __( 'Shop', 'aureon' ),
        'subtitle' => __( 'Six colorways. One obsession.', 'aureon' ),
    );
}
