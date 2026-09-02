<?php
/**
 * WC Categories adapter — maps product categories to card/category component data.
 *
 * Production-ready with:
 * - Proper error handling for get_term_link()
 * - Optimized image sizes
 * - Real product images as category thumbnails
 * - Graceful fallbacks
 * - Customizer-driven title/subtitle
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_wc_categories( $args = array() ) {
    // Merge with defaults from adapter_args, then allow settings to win.
    // (Token defaults in tokens.php equal the current premium copy, so the
    // default render is pixel-identical while values become editable.)
    $defaults = array(
        'aether_categories_label'    => 'Shop by Category',
        'aether_categories_title'    => 'Find Your Fit',
        'aether_categories_subtitle' => '',
    );
    $args = wp_parse_args( $args, $defaults );

    $args['aether_categories_label']    = (string) aureon_get_option( 'aether_categories_label', $args['aether_categories_label'] );
    $args['aether_categories_title']    = (string) aureon_get_option( 'aether_categories_title', $args['aether_categories_title'] );
    $args['aether_categories_subtitle'] = (string) aureon_get_option( 'aether_categories_subtitle', $args['aether_categories_subtitle'] );

    // Fetch real WooCommerce categories, excluding "Uncategorized".
    $exclude = array( get_option( 'default_product_cat', 0 ) );

    $terms = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'number'     => 5,
        'orderby'    => 'count',
        'order'      => 'DESC',
        'exclude'    => $exclude,
    ) );

    // Count total categories for "View All" button.
    $total_categories = wp_count_terms( 'product_cat', array(
        'hide_empty' => true,
        'exclude'    => $exclude,
    ) );
    $has_more = is_numeric( $total_categories ) && $total_categories > 5;

    // If no real categories, use curated fallback (gated by aether_demo_content).
    if ( ( empty( $terms ) || is_wp_error( $terms ) ) && aureon_get_option( 'aether_demo_content', true ) ) {
        $items = aether_get_fallback_categories();
    } else {
        $items = array();
        // Layout: first card is large (left column, 2 rows), next 4 fill the 2x2 right side.
        $modifiers = array( 'large', '', '', '', '' );

        // Curated order for the right-side 2x2 so the hero categories sit in
        // the top row (Women's Bags, Men's Shoes above Men's Sneakers, Men's Boots).
        $priority = array(
            "Accessories"  => 0,
            "Women's Bags" => 1,
            "Men's Shoes"  => 2,
            "Men's Sneakers" => 3,
            "Men's Boots"  => 4,
        );
        usort( $terms, function ( $a, $b ) use ( $priority ) {
            $pa = isset( $priority[ $a->name ] ) ? $priority[ $a->name ] : 99;
            $pb = isset( $priority[ $b->name ] ) ? $priority[ $b->name ] : 99;
            if ( $pa === $pb ) {
                return $b->count - $a->count;
            }
            return $pa - $pb;
        } );

        foreach ( $terms as $i => $term ) {
            // Get category image — prefer thumbnail, fallback to first product image.
            $image = aether_get_category_image( $term->term_id );

            // Build category link — handle WP_Error gracefully.
            $link = get_term_link( $term );
            if ( is_wp_error( $link ) ) {
                $link = function_exists( 'wc_get_page_permalink' )
                    ? wc_get_page_permalink( 'shop' )
                    : home_url( '/shop/' );
            }

            // Product count label.
            $count_label = sprintf(
                _n( '%d Product', '%d Products', $term->count, 'aureon' ),
                $term->count
            );

            $items[] = array(
                'name'     => $term->name,
                'count'    => $count_label,
                'image'    => $image,
                'alt'      => sprintf( __( 'Shop %s', 'aureon' ), $term->name ),
                'url'      => $link,
                'modifier' => isset( $modifiers[ $i ] ) ? $modifiers[ $i ] : '',
                'behavior' => array( 'reveal' => true ),
            );
        }
    }

    // Return items + Customizer options for the section header.
    return array(
        'items'                      => $items,
        'has_more'                   => $has_more,
        'total_categories'           => $total_categories,
        'all_categories_url'         => function_exists( 'wc_get_page_permalink' )
            ? wc_get_page_permalink( 'shop' )
            : home_url( '/shop/' ),
        'aether_categories_label'    => $args['aether_categories_label'],
        'aether_categories_title'    => $args['aether_categories_title'],
        'aether_categories_subtitle' => $args['aether_categories_subtitle'],
    );
}

/**
 * Get a category image — thumbnail first, then first product image, then placeholder.
 *
 * @param int $term_id Category term ID.
 * @return string Image URL.
 */
function aether_get_category_image( $term_id ) {
    // 1. Try category thumbnail (set via WooCommerce → Products → Categories).
    $thumb_id = get_term_meta( $term_id, 'thumbnail_id', true );
    if ( $thumb_id ) {
        $url = wp_get_attachment_image_url( $thumb_id, 'medium_large' );
        if ( $url ) {
            return $url;
        }
    }

    // 2. Try first published product in this category with a featured image.
    $products = get_posts( array(
        'post_type'      => 'product',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $term_id,
            ),
        ),
    ) );

    if ( ! empty( $products ) ) {
        $img = get_the_post_thumbnail_url( $products[0], 'medium_large' );
        if ( $img ) {
            return $img;
        }
    }

    // 3. Fallback to WooCommerce placeholder or theme placeholder.
    if ( function_exists( 'wc_placeholder_img_src' ) ) {
        return wc_placeholder_img_src( 'medium_large' );
    }

    return aether_viewmodel_resolve_image( 'frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg' );
}

/**
 * Curated fallback categories for when no WC categories exist.
 * Uses real product images from the store.
 *
 * @return array
 */
function aether_get_fallback_categories() {
    $shop_url = function_exists( 'wc_get_page_permalink' )
        ? wc_get_page_permalink( 'shop' )
        : home_url( '/shop/' );

    $fallbacks = array(
        array(
            'name'     => "Men's Boots",
            'count'    => 12,
            'sku'      => 'mens-captain-lace-up-boot-black-coffee-leather',
            'modifier' => 'large',
        ),
        array(
            'name'     => "Women's Boots",
            'count'    => 7,
            'sku'      => 'womens-regent-knee-high-riding-boot-black-gold',
            'modifier' => '',
        ),
        array(
            'name'     => "Men's Sneakers",
            'count'    => 23,
            'sku'      => 'mens-court-leather-sneaker-coffee-leather',
            'modifier' => '',
        ),
        array(
            'name'     => "Accessories",
            'count'    => 96,
            'sku'      => 'mens-chambray-button-up-workshirt-vintage-indigo',
            'modifier' => 'accent',
        ),
    );

    $items = array();
    foreach ( $fallbacks as $fb ) {
        // Try to get image from product SKU.
        $image = aether_viewmodel_resolve_image( 'frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg' );
        $pid   = function_exists( 'wc_get_product_id_by_sku' ) ? wc_get_product_id_by_sku( $fb['sku'] ) : 0;
        if ( $pid ) {
            $img = get_the_post_thumbnail_url( $pid, 'medium_large' );
            if ( $img ) {
                $image = $img;
            }
        }

        $items[] = array(
            'name'     => $fb['name'],
            'count'    => sprintf( _n( '%d Product', '%d Products', $fb['count'], 'aureon' ), $fb['count'] ),
            'image'    => $image,
            'alt'      => sprintf( __( 'Shop %s', 'aureon' ), $fb['name'] ),
            'url'      => $shop_url,
            'modifier' => $fb['modifier'],
            'behavior' => array( 'reveal' => true ),
        );
    }

    return $items;
}
