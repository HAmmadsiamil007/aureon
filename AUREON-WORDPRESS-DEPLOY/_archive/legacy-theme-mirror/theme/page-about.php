<?php
/**
 * Template Name: AETHER About
 * Template Post Type: page
 *
 * AETHER About page — pure section composition.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_section' ) ) :

	if ( aureon_get_option( 'aether_section_mission', true ) ) {
		aether_render_section( 'mission' );
	}
	if ( aureon_get_option( 'aether_section_features', true ) ) {
		aether_render_section( 'features' );
	}
	if ( aureon_get_option( 'aether_section_story', true ) ) {
		aether_render_section( 'story' );
	}
	if ( aureon_get_option( 'aether_section_stats', true ) ) {
		aether_render_section( 'stats' );
	}
	if ( aureon_get_option( 'aether_section_team', true ) ) {
		aether_render_section( 'team' );
	}

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}

endif;

get_footer();