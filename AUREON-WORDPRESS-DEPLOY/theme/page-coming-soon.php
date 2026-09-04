<?php
/**
 * Template Name: AETHER Coming Soon
 * Template Post Type: page
 *
 * AETHER Coming Soon landing — countdown hero only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_section' ) ) :
	if ( aureon_get_option( 'aether_section_coming_soon', true ) ) {
		aether_render_section( 'coming-soon' );
	}
endif;

get_footer();