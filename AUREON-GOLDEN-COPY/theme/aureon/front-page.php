<?php
/**
 * AETHER Front Page Template.
 *
 * The homepage is a pure composition of registered sections. WordPress and
 * WooCommerce supply data only via adapters — presentation lives in
 * frontend/components/*. Toggle each section via the Customizer
 * (aether_section_* options).
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_section' ) ) :
	if ( aureon_get_option( 'aether_section_hero', true ) ) {
		aether_render_section( 'hero' );
	}
	if ( aureon_get_option( 'aether_section_categories', true ) ) {
		aether_render_section( 'categories' );
	}
	if ( aureon_get_option( 'aether_section_bestsellers', true ) ) {
		aether_render_section( 'bestsellers' );
	}
	if ( aureon_get_option( 'aether_section_reviews', true ) ) {
		aether_render_section( 'reviews' );
	}
	if ( aureon_get_option( 'aether_section_faq', true ) ) {
		aether_render_section( 'faq' );
	}
	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}
endif;

get_footer();
