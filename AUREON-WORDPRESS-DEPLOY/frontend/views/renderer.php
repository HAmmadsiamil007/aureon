<?php
/**
 * Component renderer — the single escape boundary.
 *
 * Components receive normalized $componentData and render escaped HTML.
 * They NEVER call WordPress/WooCommerce functions.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get the component manifest.
 *
 * @return array
 */
function aether_component_manifest() {
	static $manifest = null;

	if ( null === $manifest && defined( 'AETHER_FRONTEND_DIR' ) ) {
		$manifest = include AETHER_FRONTEND_DIR . 'manifest/components.php';
	}

	// Design packs may register extra component ids (their templates live in
	// the pack; resolution happens pack-first in aether_render_component()).
	return (array) apply_filters( 'aether_component_manifest', (array) $manifest );
}

/**
 * Render a component from $componentData.
 *
 * @param string $id Component ID (e.g. 'card/product').
 * @param array  $data Normalized component data.
 */
function aether_render_component( $id, $data = array() ) {
	$manifest = aether_component_manifest();

	if ( empty( $manifest[ $id ] ) || empty( $manifest[ $id ]['template'] ) ) {
		return;
	}

	$template = aether_resolve_design_path( $manifest[ $id ]['template'] );

	if ( ! file_exists( $template ) ) {
		return;
	}

	$componentData = apply_filters( 'aether_component_data', $data, $id );

	include $template;
}

/**
 * Render a section from the section registry.
 *
 * Resolves the section's adapter (if any), merges its normalized data with
 * the passed $data, then renders the section template. Adapters are the only
 * layer allowed to touch WP/WC APIs.
 *
 * @param string $id Section ID (e.g. 'bestsellers').
 * @param array  $data Section data (overrides/merges with adapter output).
 */
function aether_render_section( $id, $data = array() ) {
	$registry = aether_section_registry();

	if ( empty( $registry[ $id ] ) || empty( $registry[ $id ]['template'] ) ) {
		return;
	}

	// Resolve adapter data first (normalized component data for this section).
	if ( ! empty( $registry[ $id ]['adapter'] ) ) {
		$adapter_slug = basename( $registry[ $id ]['adapter'], '.php' );
		// 'adapter-wc-products' -> 'wc_products' -> aether_adapter_wc_products().
		$adapter_fn   = 'aether_adapter_' . str_replace( '-', '_', (string) preg_replace( '/^adapter[-_]/', '', $adapter_slug ) );

		if ( function_exists( $adapter_fn ) ) {
			// Sections may pass explicit args (e.g. option keys, query args);
			// per-call $data wins over registered adapter_args defaults.
			$registered_args = isset( $registry[ $id ]['adapter_args'] ) ? (array) $registry[ $id ]['adapter_args'] : array();
			$adapter_args    = wp_parse_args( $data, $registered_args );
			$adapter_data    = (array) call_user_func( $adapter_fn, $adapter_args );

			// Flat item lists (cards, posts, terms...) are normalized to 'items'.
			if ( array_keys( $adapter_data ) === range( 0, count( $adapter_data ) - 1 ) ) {
				$adapter_data = array( 'items' => $adapter_data );
			}

			$data = wp_parse_args( $data, $adapter_data );
		}
	}

	// Normalize ViewModel key aliases (canonical + legacy spellings).
	$data = aether_normalize_viewmodel( $data );

	$template = aether_resolve_design_path( $registry[ $id ]['template'] );

	if ( ! file_exists( $template ) ) {
		return;
	}

	$sectionData = apply_filters( 'aether_section_data', $data, $id );

	include $template;
}

/**
 * Normalize ViewModel key aliases to canonical spellings.
 *
 * Canonical: 'pagination', 'breadcrumb', stats as a list of {number,label}.
 * Legacy aliases ('paged', 'crumbs', stats wrapped in 'items') are filled in
 * so both existing templates and new design packs consume the same shape.
 *
 * @param array $data Section/adapter data.
 * @return array Normalized data.
 */
function aether_normalize_viewmodel( $data ) {
	$data = (array) $data;

	// Pagination: canonical 'pagination', legacy 'paged' (blog).
	if ( ! isset( $data['pagination'] ) && isset( $data['paged'] ) && is_array( $data['paged'] ) ) {
		$data['pagination'] = $data['paged'];
	}
	if ( ! isset( $data['paged'] ) && isset( $data['pagination'] ) && is_array( $data['pagination'] ) ) {
		$data['paged'] = $data['pagination'];
	}

	// Breadcrumb: canonical 'breadcrumb', legacy 'crumbs' (cart).
	if ( ! isset( $data['breadcrumb'] ) && isset( $data['crumbs'] ) && is_array( $data['crumbs'] ) ) {
		$data['breadcrumb'] = $data['crumbs'];
	}
	if ( ! isset( $data['crumbs'] ) && isset( $data['breadcrumb'] ) && is_array( $data['breadcrumb'] ) ) {
		$data['crumbs'] = $data['breadcrumb'];
	}

	// Stats: canonical list of {number,label}; legacy wrapper {items:[...]} (about).
	if ( isset( $data['stats'] ) && is_array( $data['stats'] ) && isset( $data['stats']['items'] ) && is_array( $data['stats']['items'] ) ) {
		$data['stats'] = $data['stats']['items'];
	}

	return $data;
}

/**
 * Build a behavior attribute string from a behavior flags array.
 * Whitelists attribute names — output is intentionally attribute-safe.
 *
 * @param array $behavior Behavior flags.
 * @return string
 */
function aether_behavior_attrs( $behavior ) {
	$behavior = (array) $behavior;
	$attrs    = '';

	$map = array(
		'reveal'          => 'data-reveal-item',
		'reveal-group'    => 'data-reveal-group',
		'tilt'            => 'data-tilt',
		'parallax'        => 'data-parallax',
		'parallax-section'=> 'data-parallax-section',
		'zoom'            => 'data-image-zoom',
	);

	foreach ( $map as $key => $attr ) {
		if ( ! empty( $behavior[ $key ] ) ) {
			$attrs .= ' ' . $attr;
		}
	}

	// Motion text type (words|lines).
	if ( ! empty( $behavior['motion-text'] ) ) {
		$attrs .= ' data-motion-text="' . esc_attr( sanitize_key( $behavior['motion-text'] ) ) . '"';
	}

	return $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- whitelisted attribute names.
}
