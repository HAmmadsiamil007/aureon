<?php
/**
 * WC Products adapter — maps WC product queries to card/product component data.
 *
 * Defaults to top sellers (meta_key total_sales) so the "bestsellers" home
 * section works without explicit args; the shop grid passes explicit args
 * (paged, orderby, tax_query, on_sale) and receives pagination data back.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_wc_products( $query_args = array() ) {
    $defaults = array(
        'post_type'      => 'product',
        'posts_per_page' => 8,
        'post_status'    => 'publish',
        'paged'          => 1,
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'total_sales',
        'order'          => 'DESC',
    );

    $query_args = wp_parse_args( $query_args, $defaults );
    $with_cta   = ! empty( $query_args['with_cta'] );
    unset( $query_args['with_cta'] );

    // On-sale filter: only products with an active sale price.
    if ( ! empty( $query_args['on_sale'] ) ) {
        $sale_ids = wc_get_product_ids_on_sale();
        $query_args['post__in'] = ! empty( $sale_ids ) ? $sale_ids : array( 0 );
        unset( $query_args['on_sale'] );
    }

    // Related products: WC's related engine (shared category/tag), excludes self.
    if ( ! empty( $query_args['related_to'] ) ) {
        $related_ids = wc_get_related_products( (int) $query_args['related_to'], (int) $query_args['posts_per_page'] );
        $query_args['post__in'] = ! empty( $related_ids ) ? $related_ids : array( 0 );
        unset( $query_args['related_to'] );
    }

    // Shop/taxonomy context: explicit taxonomy + date ordering (menu_order).
    if ( ! empty( $query_args['orderby_shop'] ) ) {
        $query_args['orderby'] = 'menu_order title';
        $query_args['order']   = 'ASC';
        unset( $query_args['meta_key'] );
        unset( $query_args['orderby_shop'] );
    }

    $query = new WP_Query( $query_args );
    $items = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $product = wc_get_product( get_the_ID() );
            if ( ! $product ) {
                continue;
            }
            $tagline = wp_strip_all_tags( $product->get_short_description() );
            if ( mb_strlen( $tagline ) > 48 ) {
                $tagline = mb_substr( $tagline, 0, 48 ) . '…';
            }

            $badge = '';
            if ( $product->is_on_sale() ) {
                $badge = 'Sale';
            } elseif ( $product->get_date_created() && $product->get_date_created()->getTimestamp() > strtotime( '-30 days' ) ) {
                $badge = 'New';
            } elseif ( $product->is_featured() ) {
                $badge = 'Featured';
            }

            $old_price = '';
            if ( $product->is_on_sale() && $product->get_regular_price() ) {
                $old_price = wp_strip_all_tags( wc_price( (float) $product->get_regular_price() ) );
            }

            $items[] = array(
                'id'             => $product->get_id(),
                'name'           => $product->get_name(),
                'price'          => $product->get_price_html(),
                'price_plain'    => wp_strip_all_tags( wc_price( (float) $product->get_price() ) ),
                'old_price_plain'=> $old_price,
                'tagline'        => $tagline,
                'rating'         => (float) $product->get_average_rating(),
                'reviews'        => $product->get_review_count(),
                'image'          => get_the_post_thumbnail_url( get_the_ID(), 'medium_large' ),
                'alt'            => get_post_meta( get_the_ID(), '_wp_attachment_image_alt', true ),
                'url'            => get_permalink(),
                'badge'          => $badge,
                'behavior'       => array( 'tilt' => true ),
            );
        }
        wp_reset_postdata();
    }

    // Demo fallback — no products in the store yet.
    if ( empty( $items ) ) {
        foreach ( (array) aureon_get_option( 'aether_product_items', array() ) as $demo ) {
            $items[] = array(
                'id'             => isset( $demo['id'] ) ? (int) $demo['id'] : 0,
                'name'           => isset( $demo['name'] ) ? $demo['name'] : '',
                'price'          => isset( $demo['price'] ) ? $demo['price'] : '',
                'price_plain'    => isset( $demo['price'] ) ? $demo['price'] : '',
                'old_price_plain'=> isset( $demo['old_price'] ) ? $demo['old_price'] : '',
                'tagline'        => isset( $demo['tagline'] ) ? $demo['tagline'] : '',
                'rating'         => isset( $demo['rating'] ) ? (float) $demo['rating'] : 0,
                'reviews'        => isset( $demo['reviews'] ) ? $demo['reviews'] : '',
                'image'          => isset( $demo['image'] ) ? aether_viewmodel_resolve_image( $demo['image'] ) : '',
                'alt'            => isset( $demo['alt'] ) ? $demo['alt'] : ( isset( $demo['name'] ) ? $demo['name'] : '' ),
                'url'            => isset( $demo['url'] ) ? esc_url_raw( $demo['url'] ) : '',
                'badge'          => isset( $demo['badge'] ) ? $demo['badge'] : '',
                'behavior'       => array( 'tilt' => true ),
            );
        }
    }

    $data = array(
        'items'      => $items,
        'pagination' => array(
            'current' => (int) $query_args['paged'],
            'total'   => (int) $query->max_num_pages,
        ),
    );

    if ( $with_cta ) {
        $data['cta_label'] = __( 'View All Products', 'aureon' );
        $data['cta_url']   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
    }

    return $data;
}
