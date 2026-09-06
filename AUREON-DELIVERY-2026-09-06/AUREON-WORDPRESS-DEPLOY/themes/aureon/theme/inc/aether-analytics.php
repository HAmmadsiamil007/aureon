<?php
/**
 * AETHER Analytics — lightweight GA4 dataLayer events (M10).
 *
 * Emits the gtag bootstrap only when a measurement ID is configured
 * (Customizer → AETHER Frontend → GA4 Measurement ID, option
 * `aether_analytics_ga4_id`, filter `aether_ga4_id`). Events are queued
 * server-side and flushed as one dataLayer.push block in the footer:
 *
 * - `view_item`       product page view
 * - `view_item_list`  shop / product archive / category view
 * - `add_to_cart`     every WC cart add (AJAX and classic flows)
 * - `purchase`        order payment complete (order-received page)
 *
 * With no ID configured, not a single byte of tracking code is printed.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The configured GA4 measurement ID (option + filter).
 *
 * @return string
 */
function aether_analytics_id() {
	$id = function_exists( 'aureon_get_option' ) ? (string) aureon_get_option( 'aether_analytics_ga4_id', '' ) : '';
	return apply_filters( 'aether_ga4_id', trim( $id ) );
}

/**
 * Queue an ecommerce event for the footer flush.
 *
 * @param array $event dataLayer payload.
 * @return void
 */
function aether_analytics_track( $event ) {
	global $aether_ga4_events;

	if ( '' === aether_analytics_id() ) {
		return;
	}
	if ( ! is_array( $aether_ga4_events ) ) {
		$aether_ga4_events = array();
	}
	$aether_ga4_events[] = $event;
}

/**
 * gtag bootstrap in wp_head (only when configured).
 */
add_action( 'wp_head', 'aether_analytics_head', 1 );
function aether_analytics_head() {
	$id = aether_analytics_id();
	if ( '' === $id ) {
		return;
	}
	?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $id ); ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?php echo esc_js( $id ); ?>');
</script>
	<?php
}

/**
 * Flush queued events in the footer.
 */
add_action( 'wp_footer', 'aether_analytics_flush', 99 );
function aether_analytics_flush() {
	global $aether_ga4_events;

	if ( empty( $aether_ga4_events ) ) {
		return;
	}

	echo "\n<script>\nwindow.dataLayer = window.dataLayer || [];\n";
	foreach ( $aether_ga4_events as $event ) {
		echo 'dataLayer.push(' . wp_json_encode( $event ) . ");\n";
	}
	echo "</script>\n";
}

/**
 * view_item — product page view.
 */
add_action( 'template_redirect', 'aether_analytics_view_item', 5 );
function aether_analytics_view_item() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	$product = function_exists( 'wc_get_product' ) ? wc_get_product( get_queried_object_id() ) : false;
	if ( ! $product ) {
		return;
	}

	aether_analytics_track(
		array(
			'event'     => 'view_item',
			'ecommerce' => array(
				'currency' => get_woocommerce_currency(),
				'value'    => (float) $product->get_price(),
				'items'    => array(
					array(
						'item_id'   => (string) $product->get_id(),
						'item_name' => $product->get_name(),
						'price'     => (float) $product->get_price(),
					),
				),
			),
		)
	);
}

/**
 * view_item_list — shop, product taxonomy and category views.
 */
add_action( 'template_redirect', 'aether_analytics_view_item_list', 6 );
function aether_analytics_view_item_list() {
	if ( ! function_exists( 'is_shop' ) ) {
		return;
	}
	if ( ! is_shop() && ! is_product_taxonomy() && ! is_product_category() && ! is_product_tag() ) {
		return;
	}

	global $wp_query;
	$items = array();

	if ( ! empty( $wp_query->posts ) ) {
		foreach ( $wp_query->posts as $post ) {
			$product = wc_get_product( $post );
			if ( ! $product ) {
				continue;
			}
			$items[] = array(
				'item_id'   => (string) $product->get_id(),
				'item_name' => $product->get_name(),
				'price'     => (float) $product->get_price(),
			);
		}
	}

	if ( empty( $items ) ) {
		return;
	}

	aether_analytics_track(
		array(
			'event'     => 'view_item_list',
			'ecommerce' => array(
				'currency' => get_woocommerce_currency(),
				'items'    => $items,
			),
		)
	);
}

/**
 * add_to_cart — every WC cart add (AJAX via wc-ajax and classic GET flow).
 */
add_action( 'woocommerce_add_to_cart', 'aether_analytics_add_to_cart', 10, 6 );
function aether_analytics_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return;
	}

	aether_analytics_track(
		array(
			'event'     => 'add_to_cart',
			'ecommerce' => array(
				'currency' => get_woocommerce_currency(),
				'value'    => (float) $product->get_price() * (int) $quantity,
				'items'    => array(
					array(
						'item_id'   => (string) $product_id,
						'item_name' => $product->get_name(),
						'quantity'  => (int) $quantity,
						'price'     => (float) $product->get_price(),
					),
				),
			),
		)
	);
}

/**
 * purchase — fired when an order payment completes (order-received page).
 */
add_action( 'woocommerce_payment_complete', 'aether_analytics_purchase', 10, 1 );
function aether_analytics_purchase( $order_id ) {
	$order = function_exists( 'wc_get_order' ) ? wc_get_order( $order_id ) : false;
	if ( ! $order ) {
		return;
	}

	$items = array();
	foreach ( $order->get_items() as $item ) {
		$items[] = array(
			'item_id'   => (string) $item->get_product_id(),
			'item_name' => $item->get_name(),
			'quantity'  => (int) $item->get_quantity(),
			'price'     => (float) $order->get_item_total( $item ),
		);
	}

	aether_analytics_track(
		array(
			'event'     => 'purchase',
			'ecommerce' => array(
				'currency'       => $order->get_currency(),
				'transaction_id' => $order->get_order_number(),
				'value'          => (float) $order->get_total(),
				'tax'            => (float) $order->get_total_tax(),
				'shipping'       => (float) $order->get_shipping_total(),
				'items'          => $items,
			),
		)
	);
}