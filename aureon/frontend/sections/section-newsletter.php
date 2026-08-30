<?php
/**
 * Newsletter section — email capture.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'newsletter', array(
	'template' => 'sections/section-newsletter.php',
	'adapter'  => 'adapter-options.php',
	'adapter_args' => array(
		'aether_newsletter_text'     => 'Stay Connected',
		'aether_newsletter_subtitle' => 'Get 10% off your first order. No spam, ever.',
	),
	'behavior' => array( 'reveal-group' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();
?>
<section class="newsletter-section" id="newsletter" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<?php aether_render_component( 'section/newsletter', array(
		'label'    => isset( $sectionData['label'] ) ? $sectionData['label'] : '',
		'title'    => isset( $sectionData['aether_newsletter_text'] ) ? $sectionData['aether_newsletter_text'] : '',
		'subtitle' => isset( $sectionData['aether_newsletter_subtitle'] ) ? $sectionData['aether_newsletter_subtitle'] : '',
		'behavior' => $behavior,
	) ); ?>
</section>
