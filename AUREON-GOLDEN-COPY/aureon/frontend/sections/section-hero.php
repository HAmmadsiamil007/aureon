<?php
/**
 * Hero section — AETHER slider.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'hero', array(
	'template' => 'sections/section-hero.php',
	'adapter'  => 'adapter-hero.php',
	'behavior' => array( 'parallax-section' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();
?>
<?php aether_render_component( 'hero/slider', array(
	'slides'   => isset( $sectionData['slides'] ) ? (array) $sectionData['slides'] : array(),
	'behavior' => $behavior,
) ); ?>
