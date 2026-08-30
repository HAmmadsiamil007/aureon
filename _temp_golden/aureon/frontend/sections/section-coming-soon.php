<?php
/**
 * Coming soon section — countdown hero + notify form + socials.
 *
 * Source: coming-soon.html .coming-soon-section
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'coming-soon', array(
	'template' => 'sections/section-coming-soon.php',
	'adapter'  => 'adapter-coming-soon.php',
	'behavior' => array( 'reveal' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();

aether_render_component( 'soon/countdown', array(
	'brand'    => isset( $sectionData['brand'] ) ? $sectionData['brand'] : '',
	'title'    => isset( $sectionData['title'] ) ? $sectionData['title'] : '',
	'subtitle' => isset( $sectionData['subtitle'] ) ? $sectionData['subtitle'] : '',
	'target'   => isset( $sectionData['target'] ) ? $sectionData['target'] : '',
	'socials'  => isset( $sectionData['socials'] ) ? (array) $sectionData['socials'] : array(),
	'behavior' => $behavior,
) );