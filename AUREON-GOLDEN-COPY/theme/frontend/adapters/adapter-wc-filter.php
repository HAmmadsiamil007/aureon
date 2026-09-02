<?php
/**
 * Shop filter adapter — "All" + product categories + Sale buttons.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_wc_filter() {
    $shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

    $buttons = array(
        array(
            'label'  => __( 'All', 'aureon' ),
            'url'    => $shop_url,
            'active' => ( is_shop() && ! isset( $_GET['on_sale'] ) ),
        ),
    );

    $terms = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'number'     => 20,
    ) );

    if ( ! is_wp_error( $terms ) ) {
        foreach ( $terms as $term ) {
            if ( '' === trim( (string) $term->name ) || 'uncategorized' === $term->slug ) {
                continue;
            }
            $buttons[] = array(
                'label'  => $term->name,
                'url'    => get_term_link( $term ),
                'active' => is_product_category() && (int) get_queried_object_id() === (int) $term->term_id,
            );
        }
    }

    $sale_ids = function_exists( 'wc_get_product_ids_on_sale' ) ? wc_get_product_ids_on_sale() : array();

    if ( ! empty( $sale_ids ) ) {
        $buttons[] = array(
            'label'  => __( 'Sale', 'aureon' ),
            'url'    => add_query_arg( 'on_sale', '1', $shop_url ),
            'active' => isset( $_GET['on_sale'] ),
        );
    }

    return array( 'buttons' => $buttons );
}
