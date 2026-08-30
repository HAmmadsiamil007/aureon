<?php
/**
 * Testimonials adapter — maps aether_testimonial CPT to card/review component data.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_testimonials() {
    $query = new WP_Query( array(
        'post_type'      => 'aether_testimonial',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
    ) );

    $items = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $items[] = array(
                'stars'    => (int) get_post_meta( get_the_ID(), '_testimonial_stars', true ),
                'name'     => get_the_title(),
                'role'     => get_post_meta( get_the_ID(), '_testimonial_role', true ),
                'quote'    => get_the_content(),
                'title'    => get_post_meta( get_the_ID(), '_testimonial_title', true ),
                'verified' => true,
                'date'     => get_post_meta( get_the_ID(), '_testimonial_date', true ),
                'image'    => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ),
                'behavior' => array( 'reveal' => true ),
            );
        }
        wp_reset_postdata();
    }

    // Demo fallback — no testimonial posts yet (gated by aether_demo_content).
    if ( empty( $items ) && aureon_get_option( 'aether_demo_content', true ) ) {
        foreach ( (array) aureon_get_option( 'aether_testimonial_items', array() ) as $demo ) {
            $items[] = array(
                'stars'    => isset( $demo['stars'] ) ? (float) $demo['stars'] : 5,
                'name'     => isset( $demo['name'] ) ? $demo['name'] : '',
                'role'     => isset( $demo['role'] ) ? $demo['role'] : '',
                'quote'    => isset( $demo['quote'] ) ? $demo['quote'] : '',
                'title'    => isset( $demo['title'] ) ? $demo['title'] : '',
                'verified' => ! empty( $demo['verified'] ),
                'date'     => isset( $demo['date'] ) ? $demo['date'] : '',
                'image'    => isset( $demo['image'] ) ? aether_viewmodel_resolve_image( $demo['image'] ) : '',
                'behavior' => array( 'reveal' => true ),
            );
        }
    }

    // Score/count: real WooCommerce aggregate wins once products have
    // reviews; the option values (demo numbers) only apply as fallback (F3-3).
    $score = (float) aureon_get_option( 'aether_reviews_score', 4.9 );
    $count = (int) aureon_get_option( 'aether_reviews_count', 0 );

    if ( class_exists( 'WooCommerce' ) ) {
        global $wpdb;
        $row = $wpdb->get_row(
            "SELECT COUNT(*) AS cnt, AVG( CAST( cm.meta_value AS DECIMAL(3,1) ) ) AS avg_rating
             FROM {$wpdb->comments} c
             INNER JOIN {$wpdb->commentmeta} cm ON c.comment_ID = cm.comment_id AND cm.meta_key = 'rating'
             WHERE c.comment_type = 'review' AND c.comment_approved = '1'"
        );
        if ( $row && (int) $row->cnt > 0 ) {
            $count = (int) $row->cnt;
            if ( null !== $row->avg_rating ) {
                $score = (float) round( (float) $row->avg_rating, 1 );
            }
        }
    }

    return array(
        'items' => $items,
        'score' => $score,
        'count' => $count,
    );
}
