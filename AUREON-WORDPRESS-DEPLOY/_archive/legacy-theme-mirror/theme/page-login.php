<?php
/**
 * Template Name: AETHER Login
 * Template Post Type: page
 *
 * AETHER Login page — single-column auth card.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_section' ) ) :
	if ( aureon_get_option( 'aether_section_auth', true ) ) {
		aether_render_section( 'auth', array( 'mode' => 'login' ) );
	}
endif;

get_footer();