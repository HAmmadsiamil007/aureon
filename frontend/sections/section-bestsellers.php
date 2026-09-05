<?php
/**
 * Bestsellers section — WC top-sellers product grid.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'bestsellers', array(
	'template' => 'sections/section-bestsellers.php',
	'adapter'  => 'adapter-wc-products.php',
	'adapter_args' => array(
		'posts_per_page' => 4,
		'with_cta'       => true,
	),
	'behavior' => array( 'reveal-group' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$items    = isset( $sectionData['items'] ) ? (array) $sectionData['items'] : array();
$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();

if ( empty( $items ) ) {
	return;
}
?>
<section class="bestsellers" id="bestsellers" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="container">
		<?php
		aether_render_component( 'section/header', array(
			'label'    => isset( $sectionData['label'] ) ? $sectionData['label'] : __( 'Bestsellers', 'aureon' ),
			'title'    => isset( $sectionData['title'] ) ? $sectionData['title'] : __( 'Most Loved', 'aureon' ),
			'subtitle' => isset( $sectionData['subtitle'] ) ? $sectionData['subtitle'] : __( 'The shoes everyone is talking about. Tried, tested, and obsessed over.', 'aureon' ),
			'behavior' => $behavior,
		) );
		?>

		<div class="products-grid" data-reveal-group>
			<?php foreach ( $items as $product ) : ?>
				<?php aether_render_component( 'card/product', $product ); ?>
			<?php endforeach; ?>
		</div>

		<?php
		if ( ! empty( $sectionData['cta_label'] ) ) {
			aether_render_component( 'section/cta', array(
				'label' => $sectionData['cta_label'],
				'url'   => isset( $sectionData['cta_url'] ) ? $sectionData['cta_url'] : '',
			) );
		}
		?>
	</div>
</section>
