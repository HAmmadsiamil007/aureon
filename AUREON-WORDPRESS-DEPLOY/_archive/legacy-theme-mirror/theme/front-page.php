<?php
/**
 * AETHER Front Page Template.
 *
 * The homepage is a pure composition of registered sections. WordPress and
 * WooCommerce supply data only via adapters — presentation lives in
 * frontend/components/*. Toggle each section via the Customizer
 * (aether_section_* options).
 *
 * The section list is filterable via 'aether_frontpage_sections' so that
 * active design packs can contribute additional sections (editorial bands,
 * product rows, room grids, etc.) without modifying this template.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_section' ) ) :

	/**
	 * Filterable front-page section list.
	 *
	 * Design packs hook into this filter to append, prepend, or replace
	 * sections in the homepage composition. Each entry is either a string
	 * section ID (rendered with defaults) or an array with 'id' and
	 * optional 'data' key for explicit section data.
	 *
	 * @param array $sections Default section list.
	 */
	$sections = apply_filters( 'aether_frontpage_sections', array(
		'hero',
		'categories',
		'bestsellers',
		'reviews',
		'faq',
		'newsletter',
	) );

	foreach ( $sections as $entry ) {
		$section_id = is_array( $entry ) ? $entry['id'] : $entry;
		$section_data = is_array( $entry ) && isset( $entry['data'] ) ? $entry['data'] : array();

		// Standard toggle gates for built-in sections.
		$option_key = 'aether_section_' . $section_id;
		if ( in_array( $section_id, array( 'hero', 'categories', 'bestsellers', 'reviews', 'faq', 'newsletter' ), true ) ) {
			if ( ! aureon_get_option( $option_key, true ) ) {
				continue;
			}
		}

		if ( ! empty( $section_data ) ) {
			aether_render_section( $section_id, $section_data );
		} else {
			aether_render_section( $section_id );
		}
	}

endif;

get_footer();
