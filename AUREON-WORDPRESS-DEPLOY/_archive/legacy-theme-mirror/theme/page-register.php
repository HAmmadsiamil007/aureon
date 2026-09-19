<?php
/**
 * Template Name: AETHER Register
 * Template Post Type: page
 *
 * AETHER Register page — single-column auth card.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_section' ) ) :
	if ( aureon_get_option( 'aether_section_auth', true ) ) {
		aether_render_section( 'auth', array( 'mode' => 'register' ) );
	}
endif;

get_footer();