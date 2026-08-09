<?php
/**
 * FAQ section — two-column accordion + contact CTA.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'faq', array(
	'template' => 'sections/section-faq.php',
	'adapter'  => 'adapter-faq.php',
	'behavior' => array( 'reveal-group' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$items    = isset( $sectionData['items'] ) ? (array) $sectionData['items'] : array();
$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();
$columns  = isset( $sectionData['columns'] ) ? absint( $sectionData['columns'] ) : 2;

if ( empty( $items ) ) {
	return;
}

// Split into N columns for the two-column accordion layout.
$chunks = array_chunk( $items, (int) ceil( count( $items ) / max( 1, $columns ) ) );
?>
<section class="faq-section" id="faq" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="container">
		<?php
		aether_render_component( 'section/header', array(
			'label'    => isset( $sectionData['label'] ) ? $sectionData['label'] : __( 'FAQ', 'aureon' ),
			'title'    => isset( $sectionData['title'] ) ? $sectionData['title'] : __( 'Got Questions?', 'aureon' ),
			'subtitle' => isset( $sectionData['subtitle'] ) ? $sectionData['subtitle'] : __( 'Everything you need to know about AETHER.', 'aureon' ),
			'behavior' => $behavior,
		) );
		?>

		<div class="faq-grid" data-reveal-group>
			<?php foreach ( $chunks as $col => $chunk ) : ?>
				<div class="faq-column">
					<?php foreach ( $chunk as $i => $item ) : ?>
						<?php aether_render_component( 'section/accordion', $item + array( 'open' => ( 0 === $col && 0 === $i ) ) ); ?>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="faq-cta">
			<p><?php echo esc_html( isset( $sectionData['cta_text'] ) ? $sectionData['cta_text'] : __( 'Still have questions?', 'aureon' ) ); ?></p>
			<a href="<?php echo esc_url( isset( $sectionData['cta_url'] ) && $sectionData['cta_url'] ? $sectionData['cta_url'] : home_url( '/contact/' ) ); ?>" class="btn btn-outline" data-magnetic="0.12"><?php echo esc_html( isset( $sectionData['cta_label'] ) ? $sectionData['cta_label'] : __( 'Contact Us', 'aureon' ) ); ?></a>
		</div>
	</div>
</section>
