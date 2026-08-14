<?php
/**
 * Related products section — swiper via adapter-wc-products (related_to).
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'related', array(
	'template' => 'sections/section-related.php',
	'adapter'  => 'adapter-wc-products.php',
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$items = isset( $sectionData['items'] ) ? (array) $sectionData['items'] : array();

if ( empty( $items ) ) {
	// Graceful empty state instead of a silent disappear (F8-3).
	aether_render_component( 'utility/empty-state', array(
		'title'       => __( 'No related products yet', 'aureon' ),
		'description' => __( 'We have not curated any companions for this piece yet. Check back soon.', 'aureon' ),
		'icon'        => 'fa-tags',
	) );
	return;
}

aether_render_component( 'product/related', array(
	'title' => __( 'You May Also Like', 'aureon' ),
	'items' => $items,
) );
