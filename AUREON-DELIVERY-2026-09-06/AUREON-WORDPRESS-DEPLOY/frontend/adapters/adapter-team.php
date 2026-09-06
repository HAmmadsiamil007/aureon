<?php
/**
 * Team adapter — maps aether_team CPT to card/team component data.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_team() {
    $query = new WP_Query( array(
        'post_type'      => 'aether_team',
        'posts_per_page' => 8,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ) );

    $items = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $items[] = array(
                'name'     => get_the_title(),
                'role'     => get_post_meta( get_the_ID(), '_team_role', true ),
                'bio'      => get_the_content(),
                'image'    => get_the_post_thumbnail_url( get_the_ID(), 'medium_large' ),
                'behavior' => array( 'reveal' => true ),
            );
        }
        wp_reset_postdata();
    }

    // Demo fallback — no team posts yet (gated by aether_demo_content).
    if ( empty( $items ) && aureon_get_option( 'aether_demo_content', true ) ) {
        foreach ( (array) aureon_get_option( 'aether_team_items', array() ) as $demo ) {
            $items[] = array(
                'name'     => isset( $demo['name'] ) ? $demo['name'] : '',
                'role'     => isset( $demo['role'] ) ? $demo['role'] : '',
                'bio'      => isset( $demo['bio'] ) ? $demo['bio'] : '',
                'image'    => isset( $demo['image'] ) ? aether_viewmodel_resolve_image( $demo['image'] ) : '',
                'behavior' => array( 'reveal' => true ),
            );
        }
    }

    return $items;
}
