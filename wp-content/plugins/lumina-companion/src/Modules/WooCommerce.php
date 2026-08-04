<?php
/**
 * WooCommerce — WC styling enhancements for the Lumina theme.
 *
 * Original implementation of the premium WooCommerce feature category. Only
 * active when WooCommerce is present (guarded by class_exists); touches only
 * public WooCommerce APIs and the Lumina Woo bridge contract. Never removes
 * or replaces WooCommerce hooks.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * WooCommerce module.
 */
final class WooCommerce implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'woocommerce';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'WooCommerce';
	}

	/**
	 * Default settings.
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'product_grid_columns' => '3',
			'image_zoom'           => true,
			'qty_inputs'           => true,
			'checkout_style'       => true,
		);
	}

	/**
	 * Register WC hooks (guarded — only when WooCommerce is present).
	 *
	 * @return void
	 */
	public function register(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'add_action' ) || ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		add_filter( 'loop_shop_columns', array( $this, 'product_columns' ), 20 );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		add_action( 'wp_head', array( $this, 'print_css' ), 30 );
	}

	/**
	 * Shop loop columns.
	 *
	 * @param int $columns Default columns.
	 * @return int
	 */
	public function product_columns( int $columns ): int {
		$set = (int) $this->setting( 'product_grid_columns', '3' );

		return $set > 0 ? $set : $columns;
	}

	/**
	 * WooCommerce styling CSS (original; token-driven).
	 *
	 * @return string
	 */
	public function css(): string {
		$s = $this->settings();

		$css = '.woocommerce ul.products{display:grid;grid-template-columns:repeat(var(--lumina-woo-columns,3),1fr);gap:var(--lumina-spacing-gap,1rem)}.woocommerce ul.products li.product{width:100%!important;margin:0}.woocommerce .woocommerce-result-count,.woocommerce .woocommerce-ordering{margin-bottom:var(--lumina-spacing-gap,1rem)}';

		if ( (bool) $s['image_zoom'] ) {
			$css .= '.woocommerce div.product div.images img{transition:transform .2s ease}.woocommerce div.product div.images:hover img{transform:scale(1.02)}';
		}

		if ( (bool) $s['checkout_style'] ) {
			$css .= '.woocommerce form.checkout .form-row{margin-bottom:var(--lumina-spacing-gap,1rem)}';
		}

		return sprintf(
			':root{--lumina-woo-columns:%d}%s',
			(int) $s['product_grid_columns'],
			$css
		);
	}

	/**
	 * Print CSS in <head> (guarded).
	 *
	 * @return void
	 */
	public function print_css(): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated from validated values.
		echo '<style id="lumina-woo-css">' . $this->css() . '</style>';
	}
}
