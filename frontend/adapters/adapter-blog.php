<?php
/**
 * Blog adapter — maps WP_Query posts to card/blog component data (+pagination).
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_blog( $query_args = array() ) {
	$defaults = array(
		'post_type'      => 'post',
		'posts_per_page' => 6,
		'post_status'    => 'publish',
		'paged'          => max( 1, (int) get_query_var( 'paged' ) ),
	);

	$query_args = wp_parse_args( $query_args, $defaults );
	$query      = new WP_Query( $query_args );
	$items      = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$categories = get_the_category( get_the_ID() );
			$items[] = array(
				'title'    => get_the_title(),
				'excerpt'  => wp_trim_words( get_the_excerpt(), 20 ),
				'date'     => get_the_date(),
				'category' => ! empty( $categories ) ? $categories[0]->name : '',
				'author'   => get_the_author(),
				'image'    => get_the_post_thumbnail_url( get_the_ID(), 'medium_large' ),
				'alt'      => get_the_title(),
				'url'      => get_permalink(),
				'behavior' => array( 'reveal' => true ),
			);
		}
		wp_reset_postdata();
	}

	// Pagination window data for the section/pagination component.
	return array(
		'items'   => $items,
		'paged'   => array(
			'current' => absint( $query_args['paged'] ),
			'total'   => (int) $query->max_num_pages,
			'base'    => remove_query_arg( 'paged', home_url( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '/' ) ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		),
	);
}