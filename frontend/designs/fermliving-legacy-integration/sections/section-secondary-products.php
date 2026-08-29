<?php
/**
 * Ferm Living secondary products row — heading + product card grid.
 *
 * Used on collection pages, category archives, and as a secondary
 * product section on the homepage. Same adapter contract as bestsellers:
 * adapter-wc-products.php -> items[] (product cards) + cta_label/cta_url.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

aether_register_section( 'ferm-secondary-products', array(
	'template' => 'sections/section-secondary-products.php',
	'adapter'  => 'adapter-wc-products.php',
	'adapter_args' => array(
		'posts_per_page' => 8,
		'with_cta'       => true,
	),
	'behavior' => array(),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$items = isset( $sectionData['items'] ) ? (array) $sectionData['items'] : array();

if ( empty( $items ) ) {
	return;
}

$title = isset( $sectionData['title'] ) ? $sectionData['title'] : '';
$url   = isset( $sectionData['cta_url'] ) ? $sectionData['cta_url'] : '';
?>
<section class="ferm-products-row ferm-products-row--secondary" id="secondary-products">
	<div class="container">
		<?php if ( $title ) : ?>
			<h2 class="ferm-section-title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>

		<div class="ferm-products-grid">
			<?php foreach ( $items as $product ) : ?>
				<?php aether_render_component( 'card/product', $product ); ?>
			<?php endforeach; ?>
		</div>

		<?php if ( $url ) : ?>
			<div class="ferm-row-footer">
				<a href="<?php echo esc_url( $url ); ?>" class="btn ferm-btn-outline">
					<?php echo esc_html( isset( $sectionData['cta_label'] ) && $sectionData['cta_label'] ? $sectionData['cta_label'] : __( 'View All', 'aureon' ) ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
