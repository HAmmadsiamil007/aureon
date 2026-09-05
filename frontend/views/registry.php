<?php
/**
 * Section registry — sections register here and render via aether_render_section().
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get the section registry.
 *
 * @return array
 */
function aether_section_registry() {
	global $aether_section_registry;

	if ( ! is_array( $aether_section_registry ) ) {
		$aether_section_registry = array();
	}

	return $aether_section_registry;
}

/**
 * Register a section.
 *
 * @param string $id   Section ID (e.g. 'hero').
 * @param array  $args Section args: template, adapter, adapter_args, behavior.
 */
function aether_register_section( $id, $args = array() ) {
	global $aether_section_registry;

	if ( ! is_array( $aether_section_registry ) ) {
		$aether_section_registry = array();
	}

	if ( empty( $id ) || empty( $args['template'] ) ) {
		return;
	}

	$aether_section_registry[ $id ] = wp_parse_args( $args, array(
		'template'     => '',
		'adapter'      => '',
		'adapter_args' => array(),
		'behavior'     => array(),
	) );
}
