<?php
/**
 * WC Categories adapter — maps product categories to card/category component data.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_wc_categories() {
    $terms = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'number'     => 6,
    ) );

    $items = array();

    if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
        $total = count( $terms );
        foreach ( $terms as $i => $term ) {
            if ( '' === trim( (string) $term->name ) ) {
                continue; // Skip unnamed/junk terms (e.g. unseeded "Uncategorized").
            }
            $modifier = '';
            if ( 0 === $i ) {
                $modifier = 'large';
            } elseif ( $i === $total - 1 ) {
                $modifier = 'accent';
            }
            $items[] = array(
                'name'     => $term->name,
                'count'    => sprintf( _n( '%d Product', '%d Products', $term->count, 'aureon' ), $term->count ),
                'image'    => get_term_meta( $term->term_id, 'thumbnail_id', true ) ? wp_get_attachment_image_url( get_term_meta( $term->term_id, 'thumbnail_id', true ), 'medium_large' ) : '',
                'alt'      => $term->name,
                'url'      => get_term_link( $term ),
                'modifier' => $modifier,
                'behavior' => array( 'reveal' => true ),
            );
        }
    }

    // Demo fallback — no categories in the store yet.
    if ( empty( $items ) ) {
        $demos  = (array) aureon_get_option( 'aether_category_items', array() );
        $total  = count( $demos );
        foreach ( $demos as $i => $demo ) {
            $modifier = isset( $demo['modifier'] ) ? $demo['modifier'] : '';
            if ( '' === $modifier ) {
                if ( 0 === $i ) {
                    $modifier = 'large';
                } elseif ( $i === $total - 1 ) {
                    $modifier = 'accent';
                }
            }
            $items[] = array(
                'name'     => isset( $demo['name'] ) ? $demo['name'] : '',
                'count'    => isset( $demo['count'] ) ? $demo['count'] : '',
                'image'    => isset( $demo['image'] ) ? aether_viewmodel_resolve_image( $demo['image'] ) : '',
                'alt'      => isset( $demo['alt'] ) ? $demo['alt'] : ( isset( $demo['name'] ) ? $demo['name'] : '' ),
                'url'      => isset( $demo['url'] ) ? esc_url_raw( $demo['url'] ) : '',
                'modifier' => $modifier,
                'behavior' => array( 'reveal' => true ),
            );
        }
    }

    return $items;
}
