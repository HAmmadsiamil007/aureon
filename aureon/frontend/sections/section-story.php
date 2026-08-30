<?php
/**
 * Story section — about page full-width parallax quote band.
 *
 * Source: about.html .story-section
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'story', array(
	'template' => 'sections/section-story.php',
	'adapter'  => 'adapter-about.php',
	'behavior' => array( 'parallax-section' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$story    = isset( $sectionData['story'] ) ? (array) $sectionData['story'] : array();
$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();

if ( empty( $story['quote'] ) ) {
	return;
}

aether_render_component( 'content/story', array(
	'quote'    => $story['quote'],
	'behavior' => $behavior,
) );