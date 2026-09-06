<?php
/**
 * Shop page hero — label + title + subtitle over fog.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'shop-hero', array(
	'template' => 'sections/section-shop-hero.php',
	'adapter'  => 'adapter-shop-hero.php',
	'behavior' => array( 'parallax-section' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();

aether_render_component( 'hero/page-title', array(
	'label'    => isset( $sectionData['label'] ) ? $sectionData['label'] : __( 'Collection', 'aureon' ),
	'title'    => isset( $sectionData['title'] ) ? $sectionData['title'] : __( 'Shop', 'aureon' ),
	'subtitle' => isset( $sectionData['subtitle'] ) ? $sectionData['subtitle'] : '',
	'behavior' => $behavior,
) );
