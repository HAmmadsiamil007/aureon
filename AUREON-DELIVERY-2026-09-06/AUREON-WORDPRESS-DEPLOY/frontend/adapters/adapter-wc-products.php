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
    // WooCommerce guards: this adapter is loaded on every request. Without WC
    // there are no products and wc_get_product()/wc_price() would fatal.
    if ( ! function_exists( 'wc_get_product' ) || ! function_exists( 'wc_price' ) ) {
        return array(
            'items'      => array(),
            'pagination' => array( 'current' => 1, 'total' => 1 ),
        );
    }

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

    // Whitelist query keys — the renderer merges section data (label, title,
    // subtitle...) into $query_args; WP_Query would interpret 'title' as a
    // post-title search and 'layout'/other UI keys are invalid. Only known
    // keys (plus the custom ones handled below) reach WP_Query.
    $allowed = array(
        'post_type', 'posts_per_page', 'post_status', 'paged',
        'orderby', 'order', 'meta_key', 'meta_value', 'meta_query',
        'tax_query', 'post__in', 'post__not_in', 's', 'on_sale', 'related_to', 'orderby_shop',
    );
    $query_args = array_intersect_key( $query_args, array_flip( $allowed ) );

    // On-sale filter: only products with an active sale price.
    if ( ! empty( $query_args['on_sale'] ) ) {
        $sale_ids = function_exists( 'wc_get_product_ids_on_sale' ) ? wc_get_product_ids_on_sale() : array();
        $query_args['post__in'] = ! empty( $sale_ids ) ? $sale_ids : array( 0 );
        unset( $query_args['on_sale'] );
    }

    // Related products: WC's related engine (shared category/tag), excludes self.
    if ( ! empty( $query_args['related_to'] ) ) {
        $related_ids = function_exists( 'wc_get_related_products' )
            ? wc_get_related_products( (int) $query_args['related_to'], (int) $query_args['posts_per_page'] )
            : array();
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
                'add_to_cart_url'=> function_exists( 'wc_get_cart_url' ) ? add_query_arg( 'add-to-cart', $product->get_id(), wc_get_cart_url() ) : '',
                'product_type'   => $product->get_type(),
                'behavior'       => array( 'tilt' => true ),
            );
        }
        wp_reset_postdata();
    }

    // Demo fallback — no products in the store yet (gated by aether_demo_content).
    if ( empty( $items ) && aureon_get_option( 'aether_demo_content', true ) ) {
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
                'add_to_cart_url'=> '',
                'product_type'   => '',
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
