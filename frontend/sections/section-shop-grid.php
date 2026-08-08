<?php
/**
 * Shop grid — product cards + pagination.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'shop-grid', array(
	'template' => 'sections/section-shop-grid.php',
	'adapter'  => 'adapter-wc-products.php',
	'behavior' => array( 'reveal-group' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$items      = isset( $sectionData['items'] ) ? (array) $sectionData['items'] : array();
$behavior   = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();
$pagination = isset( $sectionData['pagination'] ) ? (array) $sectionData['pagination'] : array();
$current    = isset( $pagination['current'] ) ? max( 1, (int) $pagination['current'] ) : 1;
$total      = isset( $pagination['total'] ) ? (int) $pagination['total'] : 0;

if ( empty( $items ) ) {
	return;
}

// Pagination base keeps the current query (on_sale, tax) minus paged.
$base = remove_query_arg( 'paged', home_url( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '/' ) );
?>
<section class="shop-grid-section">
	<div class="container">
		<div class="shop-grid" data-reveal-group>
			<?php foreach ( $items as $product ) : ?>
				<?php aether_render_component( 'card/product', $product + array( 'layout' => 'shop' ) ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php
if ( $total > 1 ) {
	aether_render_component( 'section/pagination', array(
		'current' => $current,
		'total'   => $total,
		'base'    => $base,
	) );
}
