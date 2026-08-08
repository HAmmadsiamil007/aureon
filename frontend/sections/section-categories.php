<?php
/**
 * Categories section — WC product category grid.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'categories', array(
	'template' => 'sections/section-categories.php',
	'adapter'  => 'adapter-wc-categories.php',
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
<section class="categories" id="categories" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="container">
		<?php
		aether_render_component( 'section/header', array(
			'label'    => isset( $sectionData['label'] ) ? $sectionData['label'] : __( 'Shop by Category', 'aureon' ),
			'title'    => isset( $sectionData['title'] ) ? $sectionData['title'] : __( 'Find Your Fit', 'aureon' ),
			'subtitle' => isset( $sectionData['subtitle'] ) ? $sectionData['subtitle'] : '',
			'behavior' => $behavior,
		) );
		?>

		<div class="category-grid" data-reveal-group>
			<?php foreach ( $items as $category ) : ?>
				<?php aether_render_component( 'card/category', $category ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
