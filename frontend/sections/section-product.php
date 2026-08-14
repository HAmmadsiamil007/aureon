<?php
/**
 * Single product section — breadcrumb, hero (gallery + info), sticky bar,
 * tech specs, reviews and size guide modal.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'product', array(
	'template' => 'sections/section-product.php',
	'adapter'  => 'adapter-product.php',
	'behavior' => array( 'parallax-section' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();
$title    = isset( $sectionData['title'] ) ? $sectionData['title'] : '';

if ( ! $title ) {
	return;
}
?>
<?php aether_render_component( 'product/breadcrumb', array( 'crumbs' => isset( $sectionData['breadcrumb'] ) ? $sectionData['breadcrumb'] : array() ) ); ?>
<section class="pd-hero" data-phantom-product data-phantom-bg="hero" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="hero-fog" aria-hidden="true">
		<div id="hl_01" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
		<div id="hl_02" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
		<div id="hl_03" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
	</div>
	<div class="container">
		<div class="pd-grid">
			<?php aether_render_component( 'product/gallery', array( 'images' => isset( $sectionData['gallery'] ) ? $sectionData['gallery'] : array() ) ); ?>
			<?php aether_render_component( 'product/info', $sectionData ); ?>
		</div>
	</div>
</section>
<?php
aether_render_component( 'product/sticky-bar', array(
	'image'            => isset( $sectionData['gallery'][0]['src'] ) ? $sectionData['gallery'][0]['src'] : '',
	'name'             => $title,
	'price'            => isset( $sectionData['price_plain'] ) ? $sectionData['price_plain'] : '',
	'sizes'            => isset( $sectionData['sizes'] ) ? $sectionData['sizes'] : array(),
	'id'               => isset( $sectionData['id'] ) ? (int) $sectionData['id'] : 0,
	'product_type'     => isset( $sectionData['product_type'] ) ? $sectionData['product_type'] : 'simple',
	'add_to_cart_url'  => isset( $sectionData['add_to_cart_url'] ) ? $sectionData['add_to_cart_url'] : '#',
) );

aether_render_component( 'product/specs', array(
	'title' => __( 'Tech Specs', 'aureon' ),
	'items' => isset( $sectionData['specs'] ) ? $sectionData['specs'] : array(),
) );

aether_render_component( 'product/reviews', array(
	'title' => __( 'Customer Reviews', 'aureon' ),
	'score' => isset( $sectionData['reviews_score'] ) ? $sectionData['reviews_score'] : 0,
	'count' => isset( $sectionData['reviews_count'] ) ? $sectionData['reviews_count'] : 0,
	'bars'  => isset( $sectionData['reviews_bars'] ) ? $sectionData['reviews_bars'] : array(),
	'items' => isset( $sectionData['reviews_items'] ) ? $sectionData['reviews_items'] : array(),
) );

aether_render_component( 'product/size-guide', array(
	'title'    => __( 'Size Guide', 'aureon' ),
	'subtitle' => $title . ' — ' . __( 'Unisex Sizing', 'aureon' ),
	'rows'     => isset( $sectionData['size_table'] ) ? $sectionData['size_table'] : array(),
) );
