<?php
/**
 * The template for displaying a single page (AETHER).
 *
 * Composed: page hero + content/page + newsletter.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_component' ) ) :

	$aether_page_title = is_front_page() ? get_bloginfo( 'name' ) : get_the_title();

	aether_render_component( 'hero/page-title', array(
		'label'    => __( 'About', 'aureon' ),
		'title'    => $aether_page_title,
		'subtitle' => '',
		'behavior' => array( 'motion-text' => 'words' ),
	) );

	aether_render_component( 'content/page', array(
		'content' => get_the_content(),
	) );

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		if ( function_exists( 'aether_render_section' ) ) {
			aether_render_section( 'newsletter' );
		}
	}

endif;

get_footer();