<?php
/**
 * Ferm Living Product Page Template
 *
 * Overrides single-product.php. Renders product components directly
 * (bypasses core section-product.php) to control which sub-components appear.
 *
 * Flow: breadcrumb → hero (gallery + info) → sticky bar → accordion → recommendations → newsletter
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_component' ) && function_exists( 'aether_adapter_product' ) ) :

	/* Get product data from adapter */
	$product_data = aether_adapter_product();

	/* Apply Ferm enhancements (accordion items, recommendations) */
	if ( ! empty( $product_data ) ) {
		$product_data = ferm_enhance_product_data( $product_data );
	}

	if ( ! empty( $product_data ) ) :

		/* Breadcrumb */
		aether_render_component( 'product/breadcrumb', array(
			'crumbs' => isset( $product_data['breadcrumb'] ) ? $product_data['breadcrumb'] : array(),
		) );

		/* Hero: gallery + info grid */
		?>
		<section class="pd-hero" data-phantom-product data-phantom-bg="hero">
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
					<?php aether_render_component( 'product/gallery', array(
						'images' => isset( $product_data['gallery'] ) ? $product_data['gallery'] : array(),
					) ); ?>
					<?php aether_render_component( 'product/info', $product_data ); ?>
				</div>
			</div>
		</section>
		<?php

		/* Sticky add-to-cart bar */
		aether_render_component( 'product/sticky-bar', array(
			'image'            => isset( $product_data['gallery'][0]['src'] ) ? $product_data['gallery'][0]['src'] : '',
			'name'             => isset( $product_data['title'] ) ? $product_data['title'] : '',
			'price'            => isset( $product_data['price_plain'] ) ? $product_data['price_plain'] : '',
			'sizes'            => isset( $product_data['sizes'] ) ? $product_data['sizes'] : array(),
			'id'               => isset( $product_data['id'] ) ? (int) $product_data['id'] : 0,
			'product_type'     => isset( $product_data['product_type'] ) ? $product_data['product_type'] : 'simple',
			'add_to_cart_url'  => isset( $product_data['add_to_cart_url'] ) ? $product_data['add_to_cart_url'] : '#',
		) );

		/* Accordion: description, materials, care */
		aether_render_component( 'product/accordion', array(
			'title' => __( 'Product Details', 'aureon' ),
			'items' => isset( $product_data['accordion_items'] ) ? $product_data['accordion_items'] : array(),
		) );

		/* Recommendations */
		aether_render_component( 'product/recommendations', array(
			'title' => __( 'You May Also Like', 'aureon' ),
			'items' => isset( $product_data['recommendations'] ) ? $product_data['recommendations'] : array(),
		) );

		/* Newsletter */
		if ( function_exists( 'aether_render_section' ) && aureon_get_option( 'aether_section_newsletter', true ) ) {
			aether_render_section( 'newsletter' );
		}

	endif;

endif;

get_footer();
