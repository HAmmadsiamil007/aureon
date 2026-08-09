<?php
/**
 * Single product adapter — maps the current WC product to product-page components.
 *
 * Gallery, colors, sizes, specs and reviews use real WC data when present
 * (attributes, gallery images, review comments) and fall back to demo tokens.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_product() {
    // WooCommerce must be present — this adapter only runs on product pages,
    // but guard anyway so a degraded stack (WC disabled/mid-upgrade) can
    // never fatal on is_product() or wc_get_product().
    if ( ! function_exists( 'is_product' ) || ! function_exists( 'wc_get_product' ) ) {
        return array();
    }

    $product_id = is_product() ? get_queried_object_id() : 0;
    $product    = $product_id ? wc_get_product( $product_id ) : false;

    if ( ! $product ) {
        return array();
    }

    $name        = $product->get_name();
    $price_plain = wp_strip_all_tags( wc_price( (float) $product->get_price() ) );

    // Badge — Sale > New (30 days) > Featured.
    $badge = '';
    if ( $product->is_on_sale() ) {
        $badge = 'Sale';
    } elseif ( $product->get_date_created() && $product->get_date_created()->getTimestamp() > strtotime( '-30 days' ) ) {
        $badge = 'New Arrival';
    } elseif ( $product->is_featured() ) {
        $badge = 'Featured';
    }

    $old_price_plain = '';
    if ( $product->is_on_sale() && $product->get_regular_price() ) {
        $old_price_plain = wp_strip_all_tags( wc_price( (float) $product->get_regular_price() ) );
    }

    // Rating — real reviews when present, demo fallback otherwise
    // (demo rating gated by aether_demo_content).
    $rating = (float) $product->get_average_rating();
    $count  = (int) $product->get_review_count();
    if ( ! $rating && ! $count && aureon_get_option( 'aether_demo_content', true ) ) {
        $rating = (float) aureon_get_option( 'aether_product_score', 4.8 );
        $count  = (int) aureon_get_option( 'aether_product_score_count', 128 );
    }

    // Gallery — WC gallery images, else featured image (4 source-style views).
    $gallery_ids = $product->get_gallery_image_ids();
    $gallery     = array();
    if ( ! empty( $gallery_ids ) ) {
        foreach ( array_slice( $gallery_ids, 0, 8 ) as $gid ) {
            $gallery[] = array(
                'src' => wp_get_attachment_image_url( $gid, 'large' ),
                'alt' => get_post_meta( $gid, '_wp_attachment_image_alt', true ),
            );
        }
    } else {
        $featured = get_the_post_thumbnail_url( $product_id, 'large' );
        if ( $featured ) {
            $views = array( 'Side View', 'Top View', 'Back View', 'Detail' );
            foreach ( $views as $view ) {
                $gallery[] = array(
                    'src' => $featured,
                    'alt' => $name . ' ' . $view,
                );
            }
        }
    }

    // Colors — pa_color attribute terms mapped to hex, else demo swatches.
    $colors = array();
    if ( taxonomy_exists( 'pa_color' ) ) {
        $terms = wp_get_post_terms( $product_id, 'pa_color', array( 'fields' => 'names' ) );
        foreach ( $terms as $term ) {
            $colors[] = array(
                'name' => $term,
                'hex'  => aether_product_color_hex( $term ),
            );
        }
    }
    if ( empty( $colors ) ) {
        $colors = (array) aureon_get_option( 'aether_product_colors', array() );
    }

    // Sizes — pa_size attribute terms, else demo list.
    $sizes = array();
    if ( taxonomy_exists( 'pa_size' ) ) {
        $terms = wp_get_post_terms( $product_id, 'pa_size', array( 'fields' => 'names' ) );
        $sizes = array_map( 'strval', $terms );
    }
    if ( empty( $sizes ) ) {
        $sizes = (array) aureon_get_option( 'aether_product_sizes', array() );
    }

    // Specs — product attributes + overview, else demo spec items.
    $specs = array();
    $attrs = $product->get_attributes();
    if ( ! empty( $attrs ) ) {
        foreach ( $attrs as $attr ) {
            if ( ! $attr->is_visible() || empty( $attr->get_options() ) ) {
                continue;
            }
            $values = $attr->is_taxonomy()
                ? implode( ', ', wp_list_pluck( $attr->get_terms(), 'name' ) )
                : implode( ', ', $attr->get_options() );
            $specs[] = array(
                'icon'  => 'fa-layer-group',
                'title' => wc_attribute_label( $attr->get_name() ),
                'body'  => $values,
            );
        }
    }
    if ( empty( $specs ) ) {
        $specs = (array) aureon_get_option( 'aether_spec_items', array() );
    }

    // Reviews — real WC review comments (top 3) or demo cards.
    $review_items = array();
    $comments     = get_comments( array(
        'post_id'    => $product_id,
        'status'     => 'approve',
        'type'       => 'review',
        'number'     => 3,
        'meta_key'   => 'rating',
    ) );
    if ( ! empty( $comments ) ) {
        foreach ( $comments as $comment ) {
            $star = (float) get_comment_meta( $comment->comment_ID, 'rating', true );
            $review_items[] = array(
                'initials' => aether_product_initials( $comment->comment_author ),
                'name'     => $comment->comment_author,
                'meta'     => sprintf( __( 'Verified — %s ago', 'aureon' ), human_time_diff( strtotime( $comment->comment_date_gmt ) ) ),
                'stars'    => $star,
                'title'    => __( 'Verified Purchase', 'aureon' ),
                'text'     => wp_trim_words( $comment->comment_content, 40 ),
            );
        }
    } elseif ( aureon_get_option( 'aether_demo_content', true ) ) {
        // Demo review cards only when the demo-content policy allows it.
        $review_items = (array) aureon_get_option( 'aether_product_reviews', array() );
    }

    // Score bars — real rating distribution or demo percentages.
    $bars = array();
    $counts = $product->get_rating_counts();
    if ( ! empty( $counts ) ) {
        $total = max( 1, array_sum( $counts ) );
        for ( $s = 5; $s >= 1; $s-- ) {
            $n = isset( $counts[ $s ] ) ? (int) $counts[ $s ] : 0;
            $bars[] = array(
                'star'    => $s,
                'percent' => (int) round( $n / $total * 100 ),
                'count'   => $n,
            );
        }
    } elseif ( aureon_get_option( 'aether_demo_content', true ) ) {
        // Demo score distribution only when the demo-content policy allows it.
        $bars = (array) aureon_get_option( 'aether_product_score_bars', array() );
    }

    // Description — short description, else excerpt of the content.
    $description = wp_strip_all_tags( $product->get_short_description() );
    if ( ! $description ) {
        $excerpt = get_the_excerpt( $product_id );
        $description = $excerpt ? $excerpt : '';
    }

    // Breadcrumb: Home / Collection / first category / product.
    $crumbs = array(
        array( 'label' => __( 'Home', 'aureon' ), 'url' => home_url( '/' ) ),
    );
    $shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
    $crumbs[] = array( 'label' => __( 'Collection', 'aureon' ), 'url' => $shop_url );
    $cats = get_the_terms( $product_id, 'product_cat' );
    if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
        $cat = $cats[0];
        $crumbs[] = array( 'label' => $cat->name, 'url' => get_term_link( $cat ) );
    }
    $crumbs[] = array( 'label' => $name, 'url' => '' );

    // Classic WC add-to-cart flow (redirects to cart on success).
    $add_to_cart_url = add_query_arg( 'add-to-cart', $product_id, function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '' );

    $data = array(
        'breadcrumb'        => $crumbs,
        'gallery'           => $gallery,
        'badge'             => $badge,
        'title'             => $name,
        'price'             => $product->get_price_html(),
        'price_plain'       => $price_plain,
        'old_price_plain'   => $old_price_plain,
        'rating'            => $rating,
        'rating_text'       => sprintf( '%s — %d Reviews', number_format_i18n( $rating, 1 ), $count ),
        'description'       => $description,
        'colors'            => $colors,
        'sizes'             => $sizes,
        'quantity'          => 1,
        'add_to_cart_url'   => $add_to_cart_url,
        'add_to_cart_label' => sprintf( __( 'Add to Cart — %s', 'aureon' ), $price_plain ),
        'trust'             => (array) aureon_get_option( 'aether_product_trust', array() ),
        'specs'             => $specs,
        'size_table'        => (array) aureon_get_option( 'aether_size_table', array() ),
        'reviews_score'     => $rating,
        'reviews_count'     => $count,
        'reviews_bars'      => $bars,
        'reviews_items'     => $review_items,
    );

    return $data;
}

/**
 * Map a color attribute term name to a hex swatch (unknown terms get a neutral).
 */
function aether_product_color_hex( $name ) {
    $map = array(
        'obsidian' => '#09090B',
        'black'    => '#09090B',
        'chrome'   => '#A8B5C0',
        'silver'   => '#A8B5C0',
        'gold'     => '#C8956C',
        'phantom'  => '#2D3436',
        'gray'     => '#2D3436',
        'white'    => '#F5F5F5',
    );
    $key = strtolower( trim( $name ) );
    return isset( $map[ $key ] ) ? $map[ $key ] : '#888888';
}

/**
 * Initials from a reviewer name (max 2 letters).
 */
function aether_product_initials( $name ) {
    $parts = preg_split( '/\s+/', trim( (string) $name ) );
    if ( empty( $parts ) ) {
        return '?';
    }
    $initials = '';
    foreach ( array_slice( $parts, 0, 2 ) as $part ) {
        $initials .= mb_substr( $part, 0, 1 );
    }
    return strtoupper( $initials );
}
