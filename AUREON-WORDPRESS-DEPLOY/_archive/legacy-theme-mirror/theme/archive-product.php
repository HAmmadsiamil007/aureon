<?php
/**
 * WooCommerce Shop / Archive Template (AETHER).
 *
 * Pure section composition — data flows from WC through adapters only.
 * Serves the shop page, product category/tag archives.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_section' ) ) :

	aether_render_section( 'shop-hero' );
	aether_render_section( 'shop-filter' );

	$shop_args = array(
		'posts_per_page' => max( 1, (int) aureon_get_option( 'aether_shop_per_page', 9 ) ),
		'paged'          => max( 1, (int) get_query_var( 'paged' ) ),
		'orderby_shop'   => 1,
	);

	if ( is_product_category() ) {
		$shop_args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => (int) get_queried_object_id(),
			),
		);
	} elseif ( is_product_tag() ) {
		$shop_args['tax_query'] = array(
			array(
				'taxonomy' => 'product_tag',
				'field'    => 'term_id',
				'terms'    => (int) get_queried_object_id(),
			),
		);
	}

	if ( isset( $_GET['on_sale'] ) ) {
		$shop_args['on_sale'] = 1;
	}

	aether_render_section( 'shop-grid', $shop_args );

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}

endif;

get_footer();
