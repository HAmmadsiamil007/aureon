<?php
/**
 * FAQ adapter — maps aether_faq CPT or Customizer repeater to accordion data.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_faq() {
    $items = array();

    // Try CPT first.
    $query = new WP_Query( array(
        'post_type'      => 'aether_faq',
        'posts_per_page' => 20,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ) );

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $items[] = array(
                'question' => get_the_title(),
                'answer'   => get_the_content(),
                'behavior' => array( 'reveal' => true ),
            );
        }
        wp_reset_postdata();
    }

    // Demo fallback — no FAQ posts yet.
    if ( empty( $items ) ) {
        foreach ( (array) aureon_get_option( 'aether_faq_items', array() ) as $demo ) {
            $items[] = array(
                'question' => isset( $demo['question'] ) ? $demo['question'] : '',
                'answer'   => isset( $demo['answer'] ) ? $demo['answer'] : '',
                'behavior' => array( 'reveal' => true ),
            );
        }
    }

    return $items;
}
