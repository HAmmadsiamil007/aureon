<?php
/**
 * Template Name: AETHER Team
 * Template Post Type: page
 *
 * AETHER Team page — pure section composition.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_component' ) ) :

	aether_render_component( 'hero/page-title', array(
		'label'    => __( 'The Collective', 'aureon' ),
		'title'    => __( 'Meet the Team', 'aureon' ),
		'subtitle' => __( 'The minds behind the machines', 'aureon' ),
		'behavior' => array( 'motion-text' => 'words' ),
	) );

endif;

if ( function_exists( 'aether_render_section' ) ) :

	if ( aureon_get_option( 'aether_section_team', true ) ) {
		aether_render_section( 'team' );
	}

	if ( aureon_get_option( 'aether_section_values', true ) ) {
		aether_render_section( 'values' );
	}

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}

endif;

get_footer();