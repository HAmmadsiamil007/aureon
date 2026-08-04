<?php
/**
 * ProductAdapter — WooCommerce product data shaping.
 *
 * Phase 9 (WooCommerce Bridge): normalizes a WooCommerce product into a
 * Lumina-safe array (id, name, price, images, gallery, rating, stock, meta)
 * through the public WC API only. Every call is capability-guarded and
 * returns inert defaults WP-free / when the product cannot be loaded.
 *
 * @package Lumina\Core\Woo\Data
 * @since 0.9.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Woo\Data;

/**
 * Product data adapter.
 */
class ProductAdapter {

	/**
	 * Normalized product snapshot by product id.
	 *
	 * @param int $id Product id.
	 * @return array<string, mixed>
	 */
	public function from_id( int $id ): array {
		if ( $id <= 0 || ! class_exists( '\WC_Product' ) || ! function_exists( 'wc_get_product' ) ) {
			return $this->empty();
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WC core function.
		$product = wc_get_product( $id );

		if ( ! $product instanceof \WC_Product ) {
			return $this->empty();
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WC core function.
		$currency = function_exists( 'get_woocommerce_currency' )
			? (string) get_woocommerce_currency()
			: '';

		return array(
			'id'                => $product->get_id(),
			'name'              => (string) $product->get_name(),
			'slug'              => (string) $product->get_slug(),
			'type'              => (string) $product->get_type(),
			'price'             => (string) $product->get_price(),
			'regular'           => (string) $product->get_regular_price(),
			'sale'              => (string) $product->get_sale_price(),
			'currency'          => $currency,
			'image'             => $this->image( $product ),
			'gallery'           => $this->gallery( $product ),
			'rating'            => (float) $product->get_average_rating(),
			'rating_count'      => (int) $product->get_rating_count(),
			'stock'             => $this->stock( $product ),
			'status'            => (string) $product->get_status(),
			'url'               => (string) $product->get_permalink(),
			'description'       => (string) $product->get_description(),
			'short_description' => (string) $product->get_short_description(),
		);
	}

	/**
	 * Product main image (src, srcset, sizes) or empty array.
	 *
	 * @param \WC_Product $product Product object.
	 * @return array<string, string>
	 */
	private function image( \WC_Product $product ): array {
		$id = $product->get_image_id();

		if ( $id <= 0 ) {
			return array();
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );

		return array(
			'id'     => (string) $id,
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			'src'    => (string) wp_get_attachment_image_url( $id, 'full' ),
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			'srcset' => (string) wp_get_attachment_image_srcset( $id, 'full' ),
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			'sizes'  => (string) wp_get_attachment_image_sizes( $id, 'full' ),
			'alt'    => is_string( $alt ) ? $alt : '',
		);
	}

	/**
	 * Gallery image URLs.
	 *
	 * @param \WC_Product $product Product object.
	 * @return list<string>
	 */
	private function gallery( \WC_Product $product ): array {
		$urls = array();

		foreach ( $product->get_gallery_image_ids() as $image_id ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			$url = wp_get_attachment_image_url( $image_id, 'full' );

			if ( is_string( $url ) && '' !== $url ) {
				$urls[] = $url;
			}
		}

		return $urls;
	}

	/**
	 * Stock state (manage, status, qty, backorders, in_stock).
	 *
	 * @param \WC_Product $product Product object.
	 * @return array<string, mixed>
	 */
	private function stock( \WC_Product $product ): array {
		return array(
			'manage'     => $product->managing_stock(),
			'status'     => (string) $product->get_stock_status(),
			'quantity'   => $product->managing_stock() ? (int) $product->get_stock_quantity() : null,
			'in_stock'   => $product->is_in_stock(),
			'backorders' => (string) $product->get_backorders(),
		);
	}

	/**
	 * Empty snapshot (WP-free / missing product).
	 *
	 * @return array<string, mixed>
	 */
	public function empty(): array {
		return array(
			'id'                => 0,
			'name'              => '',
			'slug'              => '',
			'type'              => '',
			'price'             => '',
			'regular'           => '',
			'sale'              => '',
			'currency'          => '',
			'image'             => array(),
			'gallery'           => array(),
			'rating'            => 0.0,
			'rating_count'      => 0,
			'stock'             => array(
				'manage'     => false,
				'status'     => '',
				'quantity'   => null,
				'in_stock'   => false,
				'backorders' => '',
			),
			'status'            => '',
			'url'               => '',
			'description'       => '',
			'short_description' => '',
		);
	}
}
