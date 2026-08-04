<?php
/**
 * Minimal WooCommerce stubs for static analysis (PHPStan + Psalm).
 *
 * Phase 9 (WooCommerce Bridge): the Woo adapters glue against the *public*
 * WooCommerce API surface only (ADR-004). These stubs declare exactly the
 * classes/functions the bridge touches so PHPStan/Psalm can verify the glue
 * without a live WooCommerce install. They are analysis-only — never loaded
 * at runtime (guard: WooCommerce is absent → adapters return inert defaults).
 *
 * @package Phantom\Core\Woo\Stubs
 */

declare( strict_types=1 );

/**
 * WooCommerce product.
 */
class WC_Product {
	public function get_id(): int { return 0; }
	public function get_name(): string { return ''; }
	public function get_slug(): string { return ''; }
	public function get_type(): string { return 'simple'; }
	public function get_price(): string { return ''; }
	public function get_regular_price(): string { return ''; }
	public function get_sale_price(): ?string { return null; }
	public function get_image_id(): int { return 0; }
	/** @return int[] */
	public function get_gallery_image_ids(): array { return array(); }
	public function get_average_rating(): float { return 0.0; }
	public function get_rating_count(): int { return 0; }
	public function managing_stock(): bool { return false; }
	public function get_stock_status(): string { return 'instock'; }
	public function get_stock_quantity(): ?int { return null; }
	public function is_in_stock(): bool { return true; }
	public function get_backorders(): string { return 'no'; }
	public function get_status(): string { return 'publish'; }
	public function get_permalink(): string { return ''; }
	public function get_description(): string { return ''; }
	public function get_short_description(): string { return ''; }
}

/**
 * WooCommerce cart.
 */
class WC_Cart {
	/** @return array<string, array<string, mixed>> */
	public function get_cart(): array { return array(); }
	public function get_cart_contents_count(): int { return 0; }
	public function get_subtotal(): float { return 0.0; }
	public function get_total( string $context = 'view' ): float { return 0.0; } // phpcs:ignore Squiz.Commenting.FunctionComment
}

/**
 * WooCommerce checkout.
 */
class WC_Checkout {
	/** @return array<string, array<string, mixed>> */
	public function get_checkout_fields( string $fieldset = '' ): array { return array(); }
}

/**
 * WooCommerce session.
 */
class WC_Session {
	public function get( string $key, mixed $default = null ): mixed { return $default; }
}

/**
 * WooCommerce customer.
 */
class WC_Customer {
	public function get_id(): int { return 0; }
	public function get_email(): string { return ''; }
	public function get_first_name(): string { return ''; }
	public function get_last_name(): string { return ''; }
	public function get_billing_country(): string { return ''; }
}

/**
 * WooCommerce order.
 */
class WC_Order {
	public function get_id(): int { return 0; }
	public function get_order_number(): string { return ''; }
	public function get_status(): string { return ''; }
	public function get_total( string $context = 'view' ): float { return 0.0; } // phpcs:ignore Squiz.Commenting.FunctionComment
	public function get_currency(): string { return ''; }
	public function get_date_created(): ?WC_DateTime { return null; }
	public function get_billing_email(): string { return ''; }
	public function get_billing_first_name(): string { return ''; }
	public function get_billing_last_name(): string { return ''; }
	public function get_billing_country(): string { return ''; }
	public function get_billing_city(): string { return ''; }
	public function get_shipping_first_name(): string { return ''; }
	public function get_shipping_last_name(): string { return ''; }
	public function get_shipping_country(): string { return ''; }
	public function get_shipping_city(): string { return ''; }
	/** @return array<int, WC_Order_Item_Product> */
	public function get_items( string $type = 'line_item' ): array { return array(); }
	public function get_payment_method_title(): string { return ''; }
}

/**
 * WooCommerce order line item.
 */
class WC_Order_Item_Product {
	public function get_product_id(): int { return 0; }
	public function get_name(): string { return ''; }
	public function get_quantity(): int { return 0; }
	public function get_total( string $context = 'view' ): float { return 0.0; } // phpcs:ignore Squiz.Commenting.FunctionComment
}

/**
 * WooCommerce date value object.
 */
class WC_DateTime {
	public function date( string $format = 'Y-m-d H:i:s' ): string { return ''; } // phpcs:ignore Squiz.Commenting.FunctionComment
}

/**
 * WooCommerce main instance.
 */
class WooCommerce {
	public WC_Cart $cart;
	public ?WC_Session $session = null;
	public ?WC_Customer $customer = null;

	public function checkout(): WC_Checkout { return new WC_Checkout(); }
}

/**
 * WooCommerce main instance accessor.
 */
function WC(): WooCommerce {
	return new WooCommerce();
}

/**
 * Load a product.
 */
function wc_get_product( int $id ): ?WC_Product { // phpcs:ignore Squiz.Commenting.FunctionComment
	return null;
}

/**
 * Load an order (HPOS-safe).
 */
function wc_get_order( int $id ): ?WC_Order { // phpcs:ignore Squiz.Commenting.FunctionComment
	return null;
}

/**
 * Cart page permalink.
 */
function wc_get_cart_url(): string { return ''; } // phpcs:ignore Squiz.Commenting.FunctionComment

/**
 * Checkout page permalink.
 */
function wc_get_checkout_url(): string { return ''; } // phpcs:ignore Squiz.Commenting.FunctionComment

/**
 * Page permalink by WC page id.
 */
function wc_get_page_permalink( string $page ): string { return ''; } // phpcs:ignore Squiz.Commenting.FunctionComment

/**
 * Account menu items.
 *
 * @return array<string, string>
 */
function wc_get_account_menu_items(): array { return array(); }

/**
 * Store currency code.
 */
function get_woocommerce_currency(): string { return 'USD'; } // phpcs:ignore Squiz.Commenting.FunctionComment
