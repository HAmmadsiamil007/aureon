<?php
/**
 * Vineta Design Pack — Thin Composer (Data Bridge)
 *
 * Maps AUREON/WooCommerce data to Vineta presentation format.
 * Handles cart AJAX, demo data fallback, and product remapping.
 *
 * This file is loaded by the frontend engine for the vineta design.
 * It does NOT contain any presentation logic — only data transformation.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'aether_active_design' ) || 'vineta' !== aether_active_design() ) {
	return;
}

// --- Homepage section sequence ---
add_filter( 'aether_frontpage_sections', 'vineta_homepage_sections' );
function vineta_homepage_sections() {
	return array( 'hero', 'categories', 'featured_products', 'newsletter' );
}

// --- Site data ---
add_filter( 'aether_adapter_site_data', 'vineta_site_data' );
function vineta_site_data( $data ) {
	if ( is_array( $data ) ) {
		$data['name'] = 'Vineta';
	}
	return $data;
}

// --- Header data ---
add_filter( 'aether_adapter_header_data', 'vineta_header_data' );
function vineta_header_data( $data ) {
	$cart_count = 0;
	if ( function_exists( 'WC' ) && WC()->cart ) {
		$cart_count = (int) WC()->cart->get_cart_contents_count();
	}
	$data['cart_count'] = $cart_count;
	$data['is_home']    = is_front_page() || ( is_home() && ! is_paged() );
	return $data;
}

// --- Footer data ---
add_filter( 'aether_adapter_footer_data', 'vineta_footer_data' );
function vineta_footer_data( $data ) {
	return array(
		'usp_items'  => aureon_get_option( 'aether_footer_usp_items', array() ),
		'newsletter' => array(
			'heading' => aureon_get_option( 'aether_newsletter_heading', 'Subscribe Newsletter' ),
			'text'    => aureon_get_option( 'aether_newsletter_text', '' ),
		),
		'columns'    => aureon_get_option( 'aether_footer_columns', array() ),
		'legal'      => array(
			array( 'label' => 'Privacy Policy', 'url' => '#' ),
			array( 'label' => 'Terms & Conditions', 'url' => '#' ),
			array( 'label' => 'Returns & Refunds', 'url' => '#' ),
			array( 'label' => 'FAQ', 'url' => '#' ),
		),
		'payments'   => aureon_get_option( 'aether_footer_payments', array() ),
		'socials'    => aureon_get_option( 'aether_social_items', array() ),
	);
}

// --- WC Products data mapping ---
add_filter( 'aether_adapter_wc_products_data', 'vineta_wc_products_data' );
function vineta_wc_products_data( $data ) {
	if ( is_array( $data ) && isset( $data['items'] ) && is_array( $data['items'] ) ) {
		foreach ( $data['items'] as &$product ) {
			$product = vineta_remap_product( $product );
		}
	}
	return $data;
}

// --- Single product data ---
add_filter( 'aether_adapter_product_data', 'vineta_product_data' );
function vineta_product_data( $data ) {
	return vineta_remap_product( $data );
}

// --- WC Categories ---
add_filter( 'aether_adapter_wc_categories_data', 'vineta_wc_categories_data' );
function vineta_wc_categories_data( $data ) {
	return $data;
}

// --- WC Filter ---
add_filter( 'aether_adapter_wc_filter_data', 'vineta_wc_filter_data' );
function vineta_wc_filter_data( $data ) {
	$categories = aureon_get_option( 'aether_category_items', array() );
	if ( ! empty( $categories ) && is_array( $categories ) ) {
		$filters = array();
		foreach ( $categories as $cat ) {
			$filters[] = array(
				'label'  => isset( $cat['name'] ) ? $cat['name'] : '',
				'url'    => isset( $cat['url'] ) ? $cat['url'] : '#',
				'active' => false,
			);
		}
		$data['filters'] = $filters;
	}
	return $data;
}

// --- Blog ---
add_filter( 'aether_adapter_blog_data', 'vineta_blog_data' );
function vineta_blog_data( $data ) {
	if ( is_array( $data ) ) {
		$data['label'] = 'Blog';
		$data['title'] = 'Latest News & Articles';
	}
	return $data;
}

// --- Search ---
add_filter( 'aether_adapter_search_data', 'vineta_search_data' );
function vineta_search_data( $data ) {
	return array(
		'placeholder' => aureon_get_option( 'aether_search_placeholder', 'Search products...' ),
		'suggestions'  => array( 'Fashion', 'Electronics', 'Jewelry', 'Skincare', 'Furniture' ),
	);
}

// --- Newsletter ---
add_filter( 'aether_adapter_newsletter_data', 'vineta_newsletter_data' );
function vineta_newsletter_data( $data ) {
	return array(
		'heading' => aureon_get_option( 'aether_newsletter_heading', 'Subscribe Newsletter' ),
		'text'    => aureon_get_option( 'aether_newsletter_text', 'Register to read the latest news, offers and events about our company. We promise not spam your inbox.' ),
	);
}

// --- Product remapping helper ---
function vineta_remap_product( $product ) {
	if ( ! is_array( $product ) ) {
		return $product;
	}

	// Ensure price is in cents format for Vineta JS.
	if ( isset( $product['price'] ) && is_string( $product['price'] ) ) {
		$clean = preg_replace( '/[^\d.,]/', '', $product['price'] );
		$clean = str_replace( ',', '.', $clean );
		$product['price_plain'] = $product['price'];
		$product['price']       = (int) round( (float) $clean * 100 );
		$product['price_cents'] = $product['price'];
	}

	// Ensure image URL is absolute.
	if ( isset( $product['image'] ) && $product['image'] && strpos( $product['image'], 'http' ) === false ) {
		$product['image'] = aether_pack_url() . $product['image'];
	}

	// Ensure URL is absolute.
	if ( isset( $product['url'] ) && $product['url'] && strpos( $product['url'], 'http' ) === false ) {
		$product['url'] = home_url( '/' . ltrim( $product['url'], '/' ) );
	}

	return $product;
}

// --- Demo Products (fallback when no WooCommerce products exist) ---
add_filter( 'aether_demo_products', 'vineta_demo_products', 10, 2 );
function vineta_demo_products( $items, $query_args ) {
	$pack_dir = aether_active_design_dir();
	$json_file = $pack_dir . 'demo/demo-products.json';
	if ( ! file_exists( $json_file ) ) {
		return $items;
	}
	$raw = json_decode( (string) file_get_contents( $json_file ), true );
	if ( ! is_array( $raw ) ) {
		return $items;
	}

	// Normalize: handle both wrapped {products: [...]} and legacy flat array.
	if ( isset( $raw['products'] ) && is_array( $raw['products'] ) ) {
		$raw = $raw['products'];
	}

	$pack_url = aether_pack_url();
	$result   = array();
	foreach ( $raw as $product ) {
		if ( ! is_array( $product ) ) {
			continue;
		}
		$image = isset( $product['image'] ) ? $product['image'] : '';
		if ( $image && strpos( $image, 'http' ) === false ) {
			$image = $pack_url . $image;
		}
		$demo_id = isset( $product['demo_id'] ) ? $product['demo_id'] : ( isset( $product['id'] ) ? (int) $product['id'] : 0 );
		$result[] = array(
			'source'          => 'demo',
			'business_id'     => null,
			'id'              => $demo_id,
			'demo_id'         => $demo_id,
			'name'            => isset( $product['name'] ) ? $product['name'] : '',
			'price'           => isset( $product['price'] ) ? $product['price'] : '',
			'price_plain'     => isset( $product['price'] ) ? $product['price'] : '',
			'price_cents'     => isset( $product['price_cents'] ) ? (int) $product['price_cents'] : 0,
			'old_price_plain' => '',
			'tagline'         => isset( $product['tagline'] ) ? $product['tagline'] : '',
			'rating'          => 0,
			'reviews'         => 0,
			'image'           => $image,
			'alt'             => isset( $product['name'] ) ? $product['name'] : '',
			'url'             => isset( $product['url'] ) ? $product['url'] : '#',
			'badge'           => isset( $product['badge'] ) ? $product['badge'] : '',
			'purchasable'     => false,
			'add_to_cart_url' => '',
			'product_type'    => 'simple',
			'behavior'        => array( 'tilt' => true ),
			'badges'          => isset( $product['badge'] ) && $product['badge'] ? array( $product['badge'] ) : array(),
		);
	}
	$per_page = isset( $query_args['posts_per_page'] ) ? (int) $query_args['posts_per_page'] : 8;
	$paged    = isset( $query_args['paged'] ) ? (int) $query_args['paged'] : 1;
	return array_slice( $result, ( $paged - 1 ) * $per_page, $per_page );
}

// --- Demo Categories (fallback) ---
add_filter( 'aether_demo_categories', 'vineta_demo_categories', 10, 2 );
function vineta_demo_categories( $items, $args ) {
	$pack_dir  = aether_active_design_dir();
	$json_file = $pack_dir . 'demo/demo-categories.json';
	if ( ! file_exists( $json_file ) ) {
		return $items;
	}
	$raw = json_decode( (string) file_get_contents( $json_file ), true );
	if ( ! is_array( $raw ) ) {
		return $items;
	}

	// Normalize: handle both wrapped {categories: [...]} and legacy flat array.
	if ( isset( $raw['categories'] ) && is_array( $raw['categories'] ) ) {
		$raw = $raw['categories'];
	}

	$pack_url = aether_pack_url();
	$result   = array();
	foreach ( $raw as $cat ) {
		if ( ! is_array( $cat ) ) {
			continue;
		}
		$image = isset( $cat['image'] ) ? $cat['image'] : '';
		if ( $image && strpos( $image, 'http' ) === false ) {
			$image = $pack_url . $image;
		}
		$count_label = isset( $cat['count_label'] ) ? $cat['count_label'] : '';
		if ( ! $count_label && isset( $cat['count'] ) ) {
			$count = (int) $cat['count'];
			$count_label = sprintf( _n( '%d Product', '%d Products', $count, 'aureon' ), $count );
		}
		$result[] = array(
			'source'   => 'demo',
			'name'     => isset( $cat['name'] ) ? $cat['name'] : '',
			'count'    => $count_label,
			'image'    => $image,
			'alt'      => isset( $cat['name'] ) ? sprintf( __( 'Shop %s', 'aureon' ), $cat['name'] ) : '',
			'url'      => isset( $cat['url'] ) ? $cat['url'] : '#',
			'modifier' => isset( $cat['modifier'] ) ? $cat['modifier'] : '',
			'behavior' => array( 'reveal' => true ),
		);
	}
	return $result;
}

/**
 * Demo Mode Configuration
 *
 * Same logic as Ferm: auto/force_demo/disabled.
 */
function vineta_get_demo_mode() {
	$mode = aureon_get_option( 'aether_demo_mode', 'auto' );
	if ( ! in_array( $mode, array( 'auto', 'force_demo', 'disabled' ), true ) ) {
		$mode = 'auto';
	}
	return $mode;
}

function vineta_show_demo_content() {
	$mode = vineta_get_demo_mode();
	if ( 'disabled' === $mode ) {
		return false;
	}
	if ( 'force_demo' === $mode ) {
		return true;
	}
	return true;
}

function vineta_has_real_products() {
	static $has_real = null;
	if ( null !== $has_real ) {
		return $has_real;
	}
	$count = wp_count_posts( 'product' );
	$published = isset( $count->publish ) ? (int) $count->publish : 0;
	if ( $published <= 0 ) {
		$has_real = false;
		return false;
	}
	$real_query = new WP_Query( array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_query'     => array(
			array(
				'key'     => 'aureon_demo',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => 'aureon_demo',
				'value'   => '1',
				'compare' => '!=',
			),
		),
	) );
	$has_real = ! empty( $real_query->posts );
	return $has_real;
}

function vineta_has_real_categories() {
	static $has_real = null;
	if ( null !== $has_real ) {
		return $has_real;
	}
	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'fields'     => 'ids',
		'meta_query' => array(
			array(
				'key'     => 'aureon_demo_category',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => 'aureon_demo_category',
				'value'   => '1',
				'compare' => '!=',
			),
		),
	) );
	$has_real = ! is_wp_error( $terms ) && count( $terms ) > 0;
	return $has_real;
}

/**
 * Demo Content Filtering — Products
 */
add_action( 'woocommerce_product_query', 'vineta_filter_demo_products' );
function vineta_filter_demo_products( $q ) {
	static $in_filter = false;
	if ( $in_filter || is_admin() ) {
		return;
	}
	$in_filter = true;

	$mode = vineta_get_demo_mode();
	if ( 'disabled' === $mode ) {
		$in_filter = false;
		return;
	}

	$has_real = vineta_has_real_products();

	if ( 'force_demo' === $mode ) {
		$in_filter = false;
		return;
	}

	if ( $has_real ) {
		$meta_query = $q->get( 'meta_query' );
		if ( ! is_array( $meta_query ) ) {
			$meta_query = array();
		}
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key'     => 'aureon_demo',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => 'aureon_demo',
				'value'   => '1',
				'compare' => '!=',
			),
		);
		$q->set( 'meta_query', $meta_query );
	}

	$in_filter = false;
}

/**
 * Demo Content Filtering — Categories
 */
add_filter( 'get_terms', 'vineta_filter_demo_categories', 10, 3 );
function vineta_filter_demo_categories( $terms, $taxonomies, $args ) {
	static $in_filter = false;
	if ( $in_filter || is_admin() || ! in_array( 'product_cat', (array) $taxonomies, true ) ) {
		return $terms;
	}

	$mode = vineta_get_demo_mode();
	if ( 'disabled' === $mode || 'force_demo' === $mode ) {
		return $terms;
	}

	$in_filter = true;

	$real_ids = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'meta_query' => array(
			'relation' => 'OR',
			array(
				'key'     => 'aureon_demo_category',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => 'aureon_demo_category',
				'value'   => '1',
				'compare' => '!=',
			),
		),
		'fields' => 'ids',
	) );
	$in_filter = false;
	if ( is_wp_error( $real_ids ) || empty( $real_ids ) ) {
		return $terms;
	}
	if ( is_array( $terms ) ) {
		$terms = array_filter( $terms, function( $term ) {
			$demo = get_term_meta( $term->term_id, 'aureon_demo_category', true );
			return '1' !== $demo;
		} );
		$terms = array_values( $terms );
	}
	return $terms;
}

// --- Cart AJAX Handlers ---
add_action( 'wp_ajax_vineta_cart_add', 'vineta_wc_ajax_cart_add' );
add_action( 'wp_ajax_nopriv_vineta_cart_add', 'vineta_wc_ajax_cart_add' );
add_action( 'wp_ajax_vineta_cart_update', 'vineta_wc_ajax_cart_update' );
add_action( 'wp_ajax_nopriv_vineta_cart_update', 'vineta_wc_ajax_cart_update' );
add_action( 'wp_ajax_vineta_cart_get', 'vineta_wc_ajax_cart_get' );
add_action( 'wp_ajax_nopriv_vineta_cart_get', 'vineta_wc_ajax_cart_get' );

function vineta_wc_ajax_cart_add() {
	check_ajax_referer( 'vineta_cart_nonce', 'nonce' );
	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( 'WooCommerce not available' );
	}
	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$quantity   = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;
	if ( ! $product_id ) {
		wp_send_json_error( 'Invalid product' );
	}

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		wp_send_json_error( 'Product not found' );
	}

	$demo_flag = $product->get_meta( 'aureon_demo' );
	if ( '1' === $demo_flag ) {
		wp_send_json_error( 'Demo products are not available for purchase' );
	}

	if ( 'publish' !== $product->get_status() ) {
		wp_send_json_error( 'Product is not available' );
	}

	if ( ! $product->is_in_stock() ) {
		wp_send_json_error( 'Product is out of stock' );
	}

	$added = WC()->cart->add_to_cart( $product_id, $quantity );
	if ( $added ) {
		$response = vineta_build_cart_response();
		wp_send_json_success( $response );
	} else {
		wp_send_json_error( 'Could not add to cart' );
	}
}

function vineta_wc_ajax_cart_update() {
	check_ajax_referer( 'vineta_cart_nonce', 'nonce' );
	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( 'WooCommerce not available' );
	}
	$updates_json = isset( $_POST['updates'] ) ? sanitize_text_field( wp_unslash( $_POST['updates'] ) ) : '{}';
	$updates      = json_decode( $updates_json, true );
	if ( is_array( $updates ) ) {
		foreach ( $updates as $cart_item_key => $quantity ) {
			WC()->cart->set_quantity( $cart_item_key, $quantity );
		}
	}
	$response = vineta_build_cart_response();
	wp_send_json_success( $response );
}

function vineta_wc_ajax_cart_get() {
	check_ajax_referer( 'vineta_cart_nonce', 'nonce' );
	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( 'WooCommerce not available' );
	}
	$response = vineta_build_cart_response();
	wp_send_json_success( $response );
}

/**
 * Compose a human-readable variant title for a cart item (e.g. "Blue / M").
 */
function vineta_cart_item_variant_title( $cart_item ) {
	if ( empty( $cart_item['variation'] ) || ! is_array( $cart_item['variation'] ) ) {
		return '';
	}
	$parts = array();
	foreach ( $cart_item['variation'] as $attr_key => $attr_value ) {
		if ( '' === $attr_value ) {
			continue;
		}
		// attribute_pa_color -> Color
		$label = str_replace( 'attribute_', '', $attr_key );
		$label = str_replace( 'pa_', '', $label );
		$label = ucwords( str_replace( array( '-', '_' ), ' ', $label ) );
		$parts[] = $label . ': ' . $attr_value;
	}
	return implode( ' / ', $parts );
}

function vineta_build_cart_response() {
	$cart   = WC()->cart;
	$items  = array();
	foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
		$product = $cart_item['data'];
		if ( ! $product ) {
			continue;
		}
		$items[] = array(
			'key'           => $cart_item_key,
			'id'            => $cart_item['product_id'],
			'variant_id'    => isset( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'],
			'quantity'      => $cart_item['quantity'],
			'title'         => $product->get_name(),
			'price'         => (int) round( (float) $product->get_price() * 100 ),
			'line_price'    => (int) round( (float) $cart_item['line_total'] * 100 ),
			'variant_title' => vineta_cart_item_variant_title( $cart_item ),
			'product_id'    => $cart_item['product_id'],
			'url'           => get_permalink( $cart_item['product_id'] ),
			'image'         => wp_get_attachment_url( $product->get_image_id() ),
		);
	}
	return array(
		'item_count'  => $cart->get_cart_contents_count(),
		'items'       => $items,
		'total_price' => (int) round( (float) $cart->get_cart_contents_total() * 100 ),
		'sections'    => array(),
	);
}

// --- Enqueue cart bridge ---
add_action( 'wp_enqueue_scripts', 'vineta_enqueue_cart_bridge', 5 );
function vineta_enqueue_cart_bridge() {
	if ( ! function_exists( 'aether_active_design' ) || 'vineta' !== aether_active_design() ) {
		return;
	}
	$pack_url = aether_pack_url();
	if ( ! $pack_url ) {
		return;
	}

	// Register the main Vineta data bridge script.
	wp_register_script( 'vineta-data-shims', $pack_url . 'js/vineta-data-shims.js', array(), '1.0.0', true );
	wp_localize_script(
		'vineta-data-shims',
		'vineta_bridge',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'vineta_cart_nonce' ),
			'site_url' => home_url( '/' ),
			// Platform form endpoints (aether-newsletter.php / aether-ajax.php) —
			// the frozen Vineta forms POST to demo files that do not exist here.
			'aether_nonce'  => wp_create_nonce( 'aether_nonce' ),
			'contact_nonce' => wp_create_nonce( 'aether_contact' ),
			// WooCommerce placeholder for products without a featured image.
			'placeholder_image' => ( class_exists( 'WooCommerce' ) && function_exists( 'WC' ) && WC() )
				? WC()->plugin_url() . '/assets/images/placeholder.png'
				: '',
		)
	);
	wp_enqueue_script( 'vineta-data-shims' );

	// Register the Vineta path bridge (rewrites frozen-HTML links to WP permalinks).
	wp_register_script( 'vineta-path-bridge', $pack_url . 'js/vineta-path-bridge.js', array(), '1.0.0', true );
	wp_enqueue_script( 'vineta-path-bridge' );

	// Inject VinetaPageData for complete-page dynamic data.
	$page_data = vineta_build_page_data();
	wp_localize_script( 'vineta-data-shims', 'VinetaPageData', $page_data );
}

// --- Inject VinetaPageData as inline script for all complete-page routes ---
add_action( 'wp_head', 'vineta_inject_page_data', 5 );
// --- Inject <base> tag for frozen HTML relative path resolution ---
// --- Save WP jQuery reference BEFORE frozen HTML scripts load ---
add_action( 'wp_head', 'vineta_inject_base_tag', 1 );
function vineta_inject_base_tag() {
	if ( ! function_exists( 'aether_active_design' ) || 'vineta' !== aether_active_design() ) {
		return;
	}
	if ( ! function_exists( 'aether_is_complete_page_design' ) || ! aether_is_complete_page_design() ) {
		return;
	}
	$pack_url = aether_pack_url();
	if ( ! $pack_url ) {
		return;
	}
	echo '<base href="' . esc_url( $pack_url ) . '">' . "\n";
	// Save WordPress jQuery reference before frozen HTML scripts overwrite it
	echo "<script>if(window.jQuery){window._vinetaWpJQuery=window.jQuery;window._vinetaWpJQueryFn=window.jQuery.fn;}</script>\n";
}

// --- jQuery compatibility bridge ---
// The frozen HTML loads Bootstrap BEFORE its own jQuery, but WordPress jQuery
// is already loaded. Bootstrap attaches to WP jQuery. Then Vineta jQuery loads
// and overwrites window.jQuery. This bridge ensures Bootstrap plugins are
// available on whichever jQuery is active when main.js runs.
add_action( 'wp_footer', 'vineta_jquery_bridge', 999 );
function vineta_jquery_bridge() {
	if ( ! function_exists( 'aether_active_design' ) || 'vineta' !== aether_active_design() ) {
		return;
	}
	if ( ! function_exists( 'aether_is_complete_page_design' ) || ! aether_is_complete_page_design() ) {
		return;
	}
	echo "<script>\n";
	echo "(function(){\n";
	// Restore WordPress jQuery if Vineta jQuery overwrote it
	echo "var w=window;\n";
	echo "if(w._vinetaWpJQuery&&w.jQuery!==w._vinetaWpJQuery){\n";
	echo "  var oldJq=w.jQuery;\n";
	echo "  w.jQuery=w._vinetaWpJQuery;\n";
	echo "  w.jQuery.fn=w._vinetaWpJQueryFn;\n";
	echo "  // Copy Bootstrap plugins from old jQuery to restored jQuery\n";
	echo "  if(oldJq&&oldJq.fn){\n";
	echo "    var fns=['modal','tooltip','popover','collapse','dropdown','tab','alert','button','carousel','scrollspy'];\n";
	echo "    fns.forEach(function(n){if(oldJq.fn[n]&&!w.jQuery.fn[n])w.jQuery.fn[n]=oldJq.fn[n];});\n";
	echo "  }\n";
	echo "}\n";
	echo "})()\n";
	echo "</script>\n";
}

// --- Inject product data on single product pages ---
add_action( 'wp_head', 'vineta_inject_product_data', 3 );
function vineta_inject_product_data() {
	if ( ! function_exists( 'aether_active_design' ) || 'vineta' !== aether_active_design() ) {
		return;
	}
	if ( ! function_exists( 'aether_is_complete_page_design' ) || ! aether_is_complete_page_design() ) {
		return;
	}
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	global $post;
	$product = wc_get_product( $post->ID );
	if ( ! $product ) {
		return;
	}

	$image_id  = $product->get_image_id();
	$image_url = $image_id ? wp_get_attachment_url( $image_id ) : wc_placeholder_img_src();

	$gallery_ids = $product->get_gallery_image_ids();
	$gallery     = array();
	foreach ( $gallery_ids as $gid ) {
		$gallery[] = wp_get_attachment_url( $gid );
	}

	$variation_attributes = array();
	$variations_data = array();
	if ( $product->is_type( 'variable' ) ) {
		$attributes = $product->get_attributes();
		foreach ( $attributes as $attr ) {
			$variation_attributes[ $attr->get_name() ] = $attr->get_options();
		}
		$variations = $product->get_children();
		$parent_attr_names = array();
		$parent_attrs = $product->get_attributes();
		foreach ( $parent_attrs as $attr_obj ) {
			$parent_attr_names[] = $attr_obj->get_name();
		}
		foreach ( $variations as $var_id ) {
			$var = wc_get_product( $var_id );
			if ( ! $var ) {
				continue;
			}
			$var_attributes = array();
			// Get attribute values from post meta using original attribute name
			foreach ( $parent_attr_names as $attr_name ) {
				$meta_val = get_post_meta( $var_id, 'attribute_' . $attr_name, true );
				if ( ! $meta_val ) {
					// Try slug variants
					$slug = sanitize_title( $attr_name );
					$meta_val = get_post_meta( $var_id, 'attribute_' . $slug, true );
				}
				$var_attributes[ $attr_name ] = $meta_val ? $meta_val : '';
			}
			$variations_data[] = array(
				'id'         => $var_id,
				'attributes' => $var_attributes,
				'price'      => $var->get_price(),
				'regular_price' => $var->get_regular_price(),
				'sku'        => $var->get_sku(),
				'in_stock'   => $var->is_in_stock(),
				'image'      => wp_get_attachment_url( $var->get_image_id() ),
			);
		}
	}

	$product_data = array(
		'id'          => $product->get_id(),
		'name'        => $product->get_name(),
		'sku'         => $product->get_sku(),
		'price'       => $product->get_price(),
		'regular_price' => $product->get_regular_price(),
		'sale_price'  => $product->get_sale_price(),
		'description' => wp_kses_post( $product->get_description() ),
		'short_description' => wp_kses_post( $product->get_short_description() ),
		'image'       => $image_url,
		'gallery'     => $gallery,
		'permalink'   => get_permalink( $product->get_id() ),
		'add_to_cart_url' => $product->add_to_cart_url(),
		'in_stock'    => $product->is_in_stock(),
		'stock_quantity' => $product->get_stock_quantity(),
		'weight'      => $product->get_weight(),
		'dimensions'  => $product->get_dimensions( false ),
		'is_variable' => $product->is_type( 'variable' ),
		'variation_attributes' => $variation_attributes,
		'variations'  => $variations_data,
		'categories'  => wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) ),
		'tags'        => wp_get_post_terms( $product->get_id(), 'product_tag', array( 'fields' => 'names' ) ),
		'review_count' => $product->get_review_count(),
		'average_rating' => $product->get_average_rating(),
	);

	// Related products: real WC products sharing the primary category, minus self.
	$related = array();
	if ( function_exists( 'wc_get_products' ) ) {
		$cat_ids = $product->get_category_ids();
		$cat_id  = ! empty( $cat_ids ) ? $cat_ids[0] : 0;
		if ( $cat_id ) {
			$term = get_term( $cat_id, 'product_cat' );
			if ( $term && ! is_wp_error( $term ) ) {
				$rel_products = wc_get_products( array(
					'status'  => 'publish',
					'limit'   => 8,
					'orderby' => 'date',
					'order'   => 'DESC',
					'return'  => 'objects',
					'category' => array( $term->slug ),
					'exclude' => array( $product->get_id() ),
				) );
				foreach ( $rel_products as $rel ) {
					if ( '1' === $rel->get_meta( 'aureon_demo' ) ) {
						continue;
					}
					$mapped = vineta_map_wc_product( $rel );
					if ( $mapped ) {
						$related[] = $mapped;
					}
				}
			}
		}
	}
	$product_data['related'] = $related;

	$GLOBALS['vineta_product_page_data'] = $product_data;

	// Output JavaScript to update DOM with product data
	$json = wp_json_encode( $product_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	echo "<script>\n";
	echo "document.addEventListener('DOMContentLoaded',function(){\n";
	echo "var p={$json};\n";
	// Currency formatting: WC symbol + position (e.g. CHF + left_space → "CHF 139.00").
	$cur_symbol = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$';
	$cur_pos    = get_option( 'woocommerce_currency_pos', 'left' );
	// WC returns some symbols entity-encoded (e.g. CHF as &#67;&#72;&#70;) — decode
	// before emitting into the inline script so the JS sees the real symbol.
	$cur_symbol = html_entity_decode( (string) $cur_symbol, ENT_QUOTES, 'UTF-8' );
	echo 'var curSym=' . wp_json_encode( $cur_symbol ) . ',curPos=' . wp_json_encode( $cur_pos ) . ";\n";
	echo "function vFmt(v){v=String(v);if(!v||v==='')return v;if(curPos==='right')return v+curSym;if(curPos==='right_space')return v+' '+curSym;if(curPos==='left_space')return curSym+' '+v;return curSym+v;}\n";
	// Update product name (Vineta uses h5.product-name with data-aureon-slot)
	echo "var t=document.querySelector('h5.product-name,[data-aureon-slot=\"product.title\"]');\n";
	echo "if(t)t.textContent=p.name;\n";
	// Update brand
	echo "var b=document.querySelector('.brand-product');\n";
	echo "if(b)b.textContent=p.categories.length?p.categories[0]:'';\n";
	// Update price (Vineta uses .product-price with .price-new and .price-old)
	echo "var priceWrap=document.querySelector('.product-price');\n";
	echo "if(priceWrap){\n";
	echo "  var newPrice=priceWrap.querySelector('.price-new');\n";
	echo "  var oldPrice=priceWrap.querySelector('.price-old');\n";
	echo "  var badge=priceWrap.querySelector('.badge-sale');\n";
	echo "  if(newPrice)newPrice.textContent=vFmt(p.price);\n";
	echo "  if(oldPrice){\n";
	echo "    if(p.sale_price&&p.regular_price!=p.price){oldPrice.textContent=vFmt(p.regular_price);oldPrice.style.display='';}\n";
	echo "    else{oldPrice.style.display='none';}\n";
	echo "  }\n";
	echo "  if(badge){\n";
	echo "    if(p.sale_price){var disc=Math.round((1-p.price/p.regular_price)*100);badge.textContent=disc+'% Off';badge.style.display='';}\n";
	echo "    else{badge.style.display='none';}\n";
	echo "  }\n";
	echo "}\n";
	// Update stock
	echo "var stock=document.querySelector('.product-stock span,[data-aureon-slot=\"product.stock\"]');\n";
	echo "if(stock){\n";
	echo "  if(p.in_stock){stock.textContent='In Stock';stock.className='stock in-stock';}\n";
	echo "  else{stock.textContent='Out of Stock';stock.className='stock out-of-stock';}\n";
	echo "}\n";
	// Update SKU
	echo "var sku=document.querySelector('[data-aureon-slot=\"product.sku\"]');\n";
	echo "if(sku)sku.textContent=p.sku;\n";
	// Update short description
	echo "var desc=document.querySelector('[data-aureon-slot=\"product.description\"],.widget-desc,[data-aureon-slot=\"product.short_description\"]');\n";
	echo "if(desc)desc.innerHTML=p.description||p.short_description||'';\n";
	// Update main image
	// Main media: the [data-aureon-slot="product.image"] element is the main
	// swiper container, NOT an <img>. Rebuild each slide (image + gallery) into
	// the frozen Vineta slide structure (<a.item><img class="tf-image-zoom">).
	echo "var mainSwiper=document.querySelector('[data-aureon-slot=\"product.image\"]');\n";
	echo "if(mainSwiper){\n";
	echo "  var mediaImages=[];\n";
	echo "  if(p.image)mediaImages.push(p.image);\n";
	echo "  (p.gallery||[]).forEach(function(u){mediaImages.push(u);});\n";
	echo "  if(mediaImages.length){\n";
	echo "    var mainTrack=mainSwiper.querySelector('.swiper-wrapper');\n";
	echo "    if(mainTrack&&mainTrack.querySelector('.swiper-slide')){\n";
	echo "      var mainTmpl=mainTrack.querySelector('.swiper-slide');\n";
	echo "      var fragMain=document.createDocumentFragment();\n";
	echo "      mediaImages.forEach(function(url){\n";
	echo "        var slide=mainTmpl.cloneNode(true);\n";
	echo "        var link=slide.querySelector('a.item');\n";
	echo "        var im=slide.querySelector('img');\n";
	echo "        if(link){link.href=url;}\n";
	echo "        if(im){im.src=url;im.setAttribute('data-src',url);im.setAttribute('data-zoom',url);}\n";
	echo "        fragMain.appendChild(slide);\n";
	echo "      });\n";
	echo "      mainTrack.innerHTML='';\n";
	echo "      mainTrack.appendChild(fragMain);\n";
	echo "      if(window.VinetaMediaInit)VinetaMediaInit();\n";
	echo "    }\n";
	echo "  }\n";
	echo "}\n";
	// Gallery thumbs: rebuild from the same image set when real images exist.
	echo "if(mediaImages&&mediaImages.length){\n";
	echo "  var thumbSwiper=document.querySelector('[data-aureon-slot=\"product.gallery\"]');\n";
	echo "  if(thumbSwiper){var thumbTrack=thumbSwiper.querySelector('.swiper-wrapper');\n";
	echo "    if(thumbTrack&&thumbTrack.querySelector('.swiper-slide')){\n";
	echo "      var thumbTmpl=thumbTrack.querySelector('.swiper-slide');\n";
	echo "      var fragThumb=document.createDocumentFragment();\n";
	echo "      mediaImages.forEach(function(url){\n";
	echo "        var slide=thumbTmpl.cloneNode(true);\n";
	echo "        var im=slide.querySelector('img');\n";
	echo "        if(im){im.src=url;im.setAttribute('data-src',url);}\n";
	echo "        fragThumb.appendChild(slide);\n";
	echo "      });\n";
	echo "      thumbTrack.innerHTML='';\n";
	echo "      thumbTrack.appendChild(fragThumb);\n";
	echo "    }\n";
	echo "  }\n";
	echo "}\n";
	// Inject variation selectors for variable products
	echo "if(p.is_variable&&p.variation_attributes){\n";
	// Hide frozen HTML duplicate selectors - the data-aureon-slot="product.variation" block
	echo "  var frozenVariationSlot=document.querySelector('[data-aureon-slot=\"product.variation\"]');\n";
	echo "  if(frozenVariationSlot)frozenVariationSlot.style.display='none';\n";
	echo "  var variantSection=document.querySelector('.tf-product-info-variation');\n";
	echo "  if(!variantSection){\n";
	echo "    variantSection=document.createElement('div');\n";
	echo "    variantSection.className='tf-product-info-variation';\n";
	echo "    var infoWrap=document.querySelector('.tf-product-info-wrap');\n";
	echo "    if(infoWrap)infoWrap.insertBefore(variantSection,infoWrap.firstChild);\n";
	echo "  }\n";
	echo "  var html='';\n";
	echo "  var selectedVariations={};\n";
	echo "  window.vinetaSelectedVariationId=null;\n";
	echo "  Object.keys(p.variation_attributes).forEach(function(attrName){\n";
	echo "    var options=p.variation_attributes[attrName];\n";
	echo "    html+='<div class=\"variant-option mb-3\"><label class=\"text-sm fw-medium mb-2 d-block\">'+attrName+':</label><div class=\"list-color-product d-flex gap-2\">';\n";
	echo "    options.forEach(function(opt,i){\n";
	echo "      html+='<button type=\"button\" class=\"btn-variant\" data-attr=\"'+attrName+'\" data-value=\"'+opt+'\" style=\"padding:8px 16px;border:1px solid #ccc;border-radius:4px;cursor:pointer;background:'+(i===0?'#333':'#fff')+';color:'+(i===0?'#fff':'#333')+'\">'+opt+'</button>';\n";
	echo "    });\n";
	echo "    html+='</div></div>';\n";
	echo "  });\n";
	echo "  variantSection.innerHTML=html;\n";
	echo "  variantSection.querySelectorAll('.btn-variant').forEach(function(btn){\n";
	echo "    btn.addEventListener('click',function(){\n";
	echo "      var attr=btn.dataset.attr;\n";
	echo "      var val=btn.dataset.value;\n";
	echo "      selectedVariations[attr]=val;\n";
	echo "      btn.parentElement.querySelectorAll('.btn-variant').forEach(function(b){b.style.background='#fff';b.style.color='#333';});\n";
	echo "      btn.style.background='#333';btn.style.color='#fff';\n";
	echo "      // Find matching variation\n";
	echo "      if(p.variations&&p.variations.length){\n";
	echo "        var match=p.variations.find(function(v){\n";
	echo "          return Object.keys(selectedVariations).every(function(a){return v.attributes[a]===selectedVariations[a];});\n";
	echo "        });\n";
	echo "        if(match){\n";
	echo "          window.vinetaSelectedVariationId=match.id;\n";
	echo "          var priceEl=document.querySelector('.product-price .price-new');\n";
	echo "          if(priceEl)priceEl.textContent=vFmt(match.price);\n";
	echo "          var oldPriceEl=document.querySelector('.product-price .price-old');\n";
	echo "          if(oldPriceEl){\n";
	echo "            if(match.regular_price&&match.regular_price!=match.price){oldPriceEl.textContent=vFmt(match.regular_price);oldPriceEl.style.display='';}\n";
	echo "            else{oldPriceEl.style.display='none';}\n";
	echo "          }\n";
	echo "          // Update SKU\n";
	echo "          var skuEl=document.querySelector('[data-aureon-slot=\"product.sku\"]');\n";
	echo "          if(skuEl&&match.sku)skuEl.textContent=match.sku;\n";
	echo "        }\n";
	echo "      }\n";
	echo "    });\n";
	echo "  });\n";
	echo "}\n";
	// Related products: fill the frozen "People Also Bought" swiper from real data.
	echo "if(p.related&&p.related.length&&window.VinetaShop&&window.VinetaShop.fillCard){\n";
	echo "  var relSection=document.querySelector('[data-aureon-slot=\"product.related\"]');\n";
	echo "  if(relSection){\n";
	echo "    var relTrack=relSection.querySelector('.swiper .swiper-wrapper,.swiper-wrapper');\n";
	echo "    if(relTrack){\n";
	echo "      var relSlides=relTrack.querySelectorAll(':scope > .swiper-slide');\n";
	echo "      if(relSlides.length){\n";
	echo "        var relTmpl=relSlides[0];\n";
	echo "        var fragRel=document.createDocumentFragment();\n";
	echo "        p.related.forEach(function(rp,i){\n";
	echo "          var slide=relSlides[i]?relSlides[i]:relTmpl.cloneNode(true);\n";
	echo "          var card=slide.querySelector('.card-product');\n";
	echo "          if(card)window.VinetaShop.fillCard(card,rp);\n";
	echo "          fragRel.appendChild(slide);\n";
	echo "        });\n";
	echo "        relTrack.innerHTML='';\n";
	echo "        relTrack.appendChild(fragRel);\n";
	echo "      }\n";
	echo "    }\n";
	echo "  }\n";
	echo "}\n";
	// Inject WooCommerce add-to-cart and hook up buttons
	echo "var addBtns=document.querySelectorAll('a[href*=\"shoppingCart\"],.btn-add-to-cart,.add-to-cart-btn,.btn-submit-total,.single_add_to_cart_button,button[name=\"add-to-cart\"]');\n";
	echo "var qtyInput=document.querySelector('input[type=\"number\"],.quantity input');\n";
	echo "addBtns.forEach(function(btn){\n";
	echo "  btn.addEventListener('click',function(e){\n";
	echo "    e.preventDefault();\n";
	echo "    var q=qtyInput?parseInt(qtyInput.value)||1:1;\n";
	echo "    var data={action:'vineta_add_to_cart',product_id:p.id,quantity:q,nonce:vineta_bridge.nonce};\n";
	echo "    if(p.is_variable){\n";
	echo "      if(!window.vinetaSelectedVariationId){alert('Please select a variation first');return;}\n";
	echo "      data.variation_id=window.vinetaSelectedVariationId;\n";
	echo "    }\n";
	echo "    fetch(vineta_bridge.ajax_url,{\n";
	echo "      method:'POST',\n";
	echo "      headers:{'Content-Type':'application/x-www-form-urlencoded'},\n";
	echo "      body:new URLSearchParams(data),\n";
	echo "      credentials:'same-origin'\n";
	echo "    }).then(function(r){return r.json();}).then(function(res){\n";
	echo "      if(res.success){\n";
	echo "        // Normalize before announcing: vineta_add_to_cart answers with raw WC\n";
	echo "        // cart contents, but the vineta:cart-updated consumers (badge, drawer,\n";
	echo "        // cart page) expect the {items,item_count,total_price} payload — re-fetch\n";
	echo "        // via vineta_cart_get like the variation path does.\n";
	echo "        if(window.VinetaCart&&window.VinetaCart.get){\n";
	echo "          window.VinetaCart.get().then(function(cart){\n";
	echo "            if(cart&&cart.success){document.dispatchEvent(new CustomEvent('vineta:cart-updated',{detail:cart.data}));}\n";
	echo "          });\n";
	echo "        }\n";
	echo "        var mc=document.querySelector('.tf-mini-cart-wrap');\n";
	echo "        if(mc)mc.classList.add('active-open');\n";
	echo "      }\n";
	echo "    });\n";
	echo "  });\n";
	echo "});\n";
	echo "});\n";
	echo "</script>\n";
}

// --- WC AJAX handler for add-to-cart ---
add_action( 'wp_ajax_vineta_add_to_cart', 'vineta_wc_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_vineta_add_to_cart', 'vineta_wc_ajax_add_to_cart' );
function vineta_wc_ajax_add_to_cart() {
	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( array( 'message' => 'WooCommerce not available' ) );
	}
	check_ajax_referer( 'vineta_cart_nonce', 'nonce' );
	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$quantity    = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;
	$variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;

	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => 'Invalid product' ) );
	}

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		wp_send_json_error( array( 'message' => 'Product not found' ) );
	}
	// A variable product must have an explicit variation selected by the client.
	if ( $product->is_type( 'variable' ) && ! $variation_id ) {
		wp_send_json_error( array( 'message' => 'Please select a variation first' ) );
	}
	if ( $variation_id && $product->is_type( 'variable' ) ) {
		$children = $product->get_children();
		if ( ! in_array( $variation_id, $children, true ) ) {
			wp_send_json_error( array( 'message' => 'Invalid variation' ) );
		}
	}

	$variation = array();
	if ( $variation_id ) {
		$product = wc_get_product( $product_id );
		if ( $product && $product->is_type( 'variable' ) ) {
			$parent_attrs = $product->get_attributes();
			foreach ( $parent_attrs as $attr ) {
				$attr_name = $attr->get_name();
				// Try original attribute name first, then sanitized slug
				$meta_val = get_post_meta( $variation_id, 'attribute_' . $attr_name, true );
				if ( ! $meta_val ) {
					$meta_val = get_post_meta( $variation_id, 'attribute_' . sanitize_title( $attr_name ), true );
				}
				if ( $meta_val ) {
					// WC normalizes attribute keys to lowercase slug format
					$variation[ 'attribute_' . sanitize_title( $attr_name ) ] = $meta_val;
				}
			}
		}
	}

	$cart = WC()->cart;
	$result = false;
	if ( $variation_id ) {
		$result = $cart->add_to_cart( $product_id, $quantity, $variation_id, $variation );
	} else {
		$result = $cart->add_to_cart( $product_id, $quantity );
	}

	if ( ! $result ) {
		wp_send_json_error( array( 'message' => 'Could not add to cart' ) );
	}

	$cart->calculate_totals();

	wp_send_json_success( array(
		'cart_contents' => $cart->get_cart_contents(),
		'item_count'    => $cart->get_cart_contents_count(),
		'total'         => $cart->get_cart_contents_total( 'edit' ),
	) );
}

/**
 * Read a Customizer-driven aether_* value for the Vineta pack.
 *
 * Resolution order (mirrors every storage path this project has used):
 *   1. aureon_settings bucket  (Customizer UI saves here: aureon_settings[<key>])
 *   2. top-level option        (wp-cli / legacy seeds; ferm-page.php reads these)
 *   3. design-token default    (aureon_get_option -> vineta tokens.php)
 * Returns '' when nothing is set so callers can keep pack defaults.
 *
 * @param string $key     Option key WITHOUT aether_ prefix handling (full key expected, e.g. aether_color_accent).
 * @param mixed  $default Fallback when unset.
 * @return mixed
 */
function vineta_get_customizer_value( $key, $default = '' ) {
	// 1. Customizer UI bucket.
	$bucket = get_option( 'aureon_settings', array() );
	if ( is_array( $bucket ) && array_key_exists( $key, $bucket ) && '' !== $bucket[ $key ] && null !== $bucket[ $key ] ) {
		return $bucket[ $key ];
	}
	// 2. Top-level option (legacy / wp-cli seeds).
	$top = get_option( $key, null );
	if ( null !== $top && '' !== $top ) {
		return $top;
	}
	// 3. Design-token defaults.
	if ( function_exists( 'aureon_get_option' ) ) {
		$def = aureon_get_option( $key, $default );
		if ( '' !== $def && null !== $def && false !== $def ) {
			return $def;
		}
	}
	return $default;
}

/**
 * Emit Vineta-native CSS custom properties from AUREON Customizer values.
 *
 * ferm-page.php injects AUREON-token variables (--accent, --bg, --main-color)
 * that Vineta styles.css never consumes. Vineta actually styles from
 * --primary/--dark/--white/--line/--surface/--text. This bridge re-maps the
 * Customizer colors onto the variables Vineta consumes, at pack level, so no
 * Golden Core change is needed.
 *
 * Runs late (priority 20) so the rules appear after the enqueued pack CSS.
 */
function vineta_emit_customizer_css() {
	if ( ! function_exists( 'aether_active_design' ) || 'vineta' !== aether_active_design() ) {
		return;
	}
	if ( ! function_exists( 'aether_is_complete_page_design' ) || ! aether_is_complete_page_design() ) {
		return;
	}

	$rules = array();
	$body  = array();

	// accent -> --primary (brand color on buttons/links/badges)
	$accent = vineta_get_customizer_value( 'aether_color_accent', '' );
	if ( $accent ) {
		$rules[] = '--primary:' . $accent;
	}
	// accent hover -> --primary-2 (Vineta hover tint slot used across the pack)
	$hover = vineta_get_customizer_value( 'aether_color_accent_hover', '' );
	if ( $hover ) {
		$rules[] = '--primary-2:' . $hover;
	}
	// text -> --dark (primary ink: headings/body copy)
	$text = vineta_get_customizer_value( 'aether_color_text', '' );
	if ( $text ) {
		$rules[] = '--dark:' . $text;
	}
	// muted -> --text (secondary copy)
	$muted = vineta_get_customizer_value( 'aether_color_muted', '' );
	if ( $muted ) {
		$rules[] = '--text:' . $muted;
	}
	// border -> --line (hairlines/dividers)
	$border = vineta_get_customizer_value( 'aether_color_border', '' );
	if ( $border ) {
		$rules[] = '--line:' . $border;
	}
	// surface -> --surface
	$surface = vineta_get_customizer_value( 'aether_color_surface', '' );
	if ( $surface ) {
		$rules[] = '--surface:' . $surface;
	}
	// bg -> body background (NOT --white: --white is also white text on dark
	// buttons; remapping it would break button contrast).
	$bg = vineta_get_customizer_value( 'aether_color_bg', '' );
	if ( $bg ) {
		$body[] = 'background-color:' . $bg;
	}

	// Fonts -> body/headings. Vineta styles.css hardcodes "Poppins" in the
	// body rule; an explicit later declaration wins without touching Core.
	$font_body = vineta_get_customizer_value( 'aether_font_body', '' );
	if ( $font_body ) {
		$body[] = 'font-family:' . $font_body;
	}
	$font_heading = vineta_get_customizer_value( 'aether_font_heading', '' );
	if ( $font_heading ) {
		$rules[] = '--vineta-font-heading:' . $font_heading;
	}

	if ( empty( $rules ) && empty( $body ) ) {
		return;
	}

	echo '<style id="vineta-customizer-bridge">' . "
";
	if ( ! empty( $rules ) ) {
		echo ':root{' . implode( ';', $rules ) . '}' . "
";
	}
	if ( ! empty( $body ) ) {
		echo 'body{' . implode( ';', $body ) . '}' . "
";
	}
	if ( $font_heading ) {
		echo 'h1,h2,h3,h4,h5,h6,.heading{font-family:var(--vineta-font-heading)}' . "
";
	}
	echo '</style>' . "
";
}
add_action( 'wp_head', 'vineta_emit_customizer_css', 20 );

function vineta_inject_page_data() {
	if ( ! function_exists( 'aether_active_design' ) || 'vineta' !== aether_active_design() ) {
		return;
	}
	$page_data = vineta_build_page_data();
	$json = wp_json_encode( $page_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	echo '<script>window.VinetaPageData = ' . $json . ';</script>' . "\n";
}

/**
 * Build the VinetaPageData object injected into complete-page templates.
 */
function vineta_build_page_data() {
	// Cart state.
	$cart_items  = array();
	$cart_count  = 0;
	$cart_total  = 0;
	if ( function_exists( 'WC' ) && WC()->cart ) {
		$cart_count = (int) WC()->cart->get_cart_contents_count();
		$cart_total = (int) round( (float) WC()->cart->get_cart_contents_total() * 100 );
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$product = $cart_item['data'];
			if ( ! $product ) {
				continue;
			}
			$image_id = $product->get_image_id();
			$cart_items[] = array(
				'key'           => $cart_item_key,
				'id'            => $cart_item['product_id'],
				'variant_id'    => isset( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'],
				'quantity'      => $cart_item['quantity'],
				'title'         => $product->get_name(),
				'price'         => (int) round( (float) $product->get_price() * 100 ),
				'line_price'    => (int) round( (float) $cart_item['line_total'] * 100 ),
				'variant_title' => vineta_cart_item_variant_title( $cart_item ),
				'product_id'    => $cart_item['product_id'],
				'url'           => get_permalink( $cart_item['product_id'] ),
				'image'         => $image_id ? wp_get_attachment_url( $image_id ) : '',
			);
		}
	}

	// Customer state.
	$customer = array(
		'logged_in'  => is_user_logged_in(),
		'id'         => null,
		'email'      => null,
		'first_name' => null,
		'last_name'  => null,
		'addresses'  => array(),
	);
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$customer['id']         = $user->ID;
		$customer['email']      = $user->user_email;
		$customer['first_name'] = $user->first_name;
		$customer['last_name']  = $user->last_name;
	}

	// Shop config.
	$currency = 'USD';
	if ( class_exists( 'WooCommerce' ) ) {
		$currency = get_woocommerce_currency();
	}

	// Navigation — map WP nav menus to Vineta format.
	$nav_main   = vineta_get_nav_menu( 'primary' );
	$nav_footer = vineta_get_nav_menu( 'footer' );

	// Page info.
	$template = 'index';
	if ( is_front_page() && ! is_paged() ) {
		$template = 'index';
	} elseif ( function_exists( 'is_product' ) && is_product() ) {
		$template = 'product';
	} elseif ( is_404() ) {
		$template = '404';
	} elseif ( is_post_type_archive( 'product' ) || is_page( 'shop' ) ) {
		$template = 'collection';
	} elseif ( is_tax( 'product_cat' ) ) {
		$template = 'collection';
	} elseif ( function_exists( 'is_cart' ) && is_cart() ) {
		$template = 'cart';
	} elseif ( function_exists( 'is_checkout' ) && is_checkout() ) {
		$template = 'checkout';
	} elseif ( function_exists( 'is_account_page' ) && is_account_page() ) {
		$template = 'account';
	} elseif ( is_page() ) {
		$template = 'page';
	} elseif ( is_home() || is_post_type_archive( 'post' ) ) {
		$template = 'blog';
	} elseif ( is_search() ) {
		$template = 'search';
	}

	// Build return array.
	$page_data = array(
		'cart' => array(
			'items'       => $cart_items,
			'item_count'  => $cart_count,
			'total_price' => $cart_total,
			'currency'    => $currency,
		),
		'customer' => $customer,
		'shop'     => array(
			'name'             => get_bloginfo( 'name' ),
			'url'              => home_url( '/' ),
			'currency'         => $currency,
		),
		'navigation' => array(
			'main'   => $nav_main,
			'footer' => $nav_footer,
		),
		'config' => array(
			'ajax_url'     => admin_url( 'admin-ajax.php' ),
			'nonce'        => wp_create_nonce( 'vineta_cart_nonce' ),
			'wc_ajax_url'  => function_exists( 'WC' ) ? add_query_arg( 'wc-ajax', '%%endpoint%%', home_url( '/' ) ) : '',
			'is_logged_in' => is_user_logged_in(),
			'template'     => $template,
			'shop_url'     => home_url( '/' ),
			'search_url'   => home_url( '/?s=' ),
		),
	);

	// Contact details for the static.contact_info slot — driven by options so a
	// client can update address/hours/email without editing the frozen template.
	$contact_address = vineta_get_customizer_value( 'aether_contact_address', array( '15 Yarran st, Punchbowl, NSW, Australia' ) );
	if ( is_string( $contact_address ) && '' !== trim( $contact_address ) ) {
		$decoded = json_decode( $contact_address, true );
		$contact_address = is_array( $decoded ) ? $decoded : array( $contact_address );
	}
	$page_data['contact'] = array(
		'address' => array_map( 'sanitize_text_field', (array) $contact_address ),
		'hours'   => sanitize_text_field( (string) vineta_get_customizer_value( 'aether_contact_hours', '8am - 7pm, Mon - Sat' ) ),
		'email'   => sanitize_email( (string) get_option( 'admin_email', 'contact@vineta.com' ) ),
		'phone'   => sanitize_text_field( (string) vineta_get_customizer_value( 'aether_contact_phone', '' ) ),
	);

	// Search UI text — drive the frozen header/search placeholder from
	// aether_search_placeholder instead of the static "Search" copy. Uses
	// vineta_get_customizer_value() (same canonical reader as colors/hero/
	// newsletter below) so Customizer UI, raw options and tokens all resolve.
	$page_data['search'] = array(
		'placeholder' => sanitize_text_field( (string) vineta_get_customizer_value( 'aether_search_placeholder', 'Search products...' ) ),
		'suggestions' => array( 'Fashion', 'Electronics', 'Jewelry', 'Skincare', 'Furniture' ),
	);

	// Inject product data on single product pages.
	if ( ! empty( $GLOBALS['vineta_product_page_data'] ) ) {
		$page_data['product'] = $GLOBALS['vineta_product_page_data'];
	}

	// Inject home featured datasets on the front page.
	if ( is_front_page() && ! is_paged() ) {
		$page_data['home'] = vineta_build_home_data();
	}

	// Inject collection data on product archive/category pages.
	if ( ( is_tax( 'product_cat' ) || is_post_type_archive( 'product' ) || is_page( 'shop' ) ) && ( ! function_exists( 'is_product' ) || ! is_product() ) ) {
		$page_data['collection'] = vineta_build_collection_data();
	}

	// Inject search dataset on search pages (rendered by the same grid consumer).
	if ( is_search() ) {
		$page_data['collection'] = vineta_build_search_data();
	}

	// Inject blog archive dataset (real WP posts) on the blog archive.
	// Matches the resolver's blog-archive guard (is_home / post archive / blog page).
	if ( is_home() || is_post_type_archive( 'post' ) || is_page( 'blog' ) ) {
		$page_data['blog'] = vineta_build_blog_data();
	}

	// Inject single-article dataset on blog single pages.
	if ( is_singular( 'post' ) ) {
		$article_post = get_queried_object();
		if ( $article_post && isset( $article_post->ID ) ) {
			$article = vineta_build_article_data( $article_post->ID );
			if ( $article ) {
				$page_data['article'] = $article;
			}
		}
	}

	// Inject generic WP page content on WordPress pages that actually carry
	// content. The frozen Vineta templates for the legal/info pages ship demo
	// placeholder copy ("The Company Pte Ltd", "[Email Address]" etc.); when the
	// WP page has real content it must win so clients own their published copy.
	// The shims consumer replaces the shared .s-term-user .content region.
	if ( is_page() ) {
		$wp_page = get_queried_object();
		if ( $wp_page && isset( $wp_page->ID ) && '' !== trim( (string) $wp_page->post_content ) ) {
			$page_content_html = $wp_page->post_content;
			if ( function_exists( 'apply_filters' ) ) {
				$page_content_html = apply_filters( 'the_content', $page_content_html );
			}
			$page_data['page'] = array(
				'id'      => (int) $wp_page->ID,
				'title'   => get_the_title( $wp_page ),
				'content' => $page_content_html,
			);
		}
	}

	// Customizer content bridge.
	$custom_logo = function_exists( 'has_custom_logo' ) && has_custom_logo()
		? wp_get_attachment_image_url( get_theme_mod( 'custom_logo', '' ), 'full' )
		: '';

	$page_data['customizer'] = array(
		'site' => array(
			'name'        => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'logo_url'    => $custom_logo ? $custom_logo : '',
		),
		'announcement' => vineta_get_customizer_value( 'aether_announcement_items', array() ),
		'hero'         => vineta_get_customizer_value( 'aether_hero_slides', array() ),
		'categories'   => vineta_get_customizer_value( 'aether_category_items', array() ),
		'footer'       => vineta_get_customizer_value( 'aether_footer_columns', array() ),
		'newsletter'   => array(
			'heading'  => vineta_get_customizer_value( 'aether_newsletter_heading', '' ),
			'text'     => vineta_get_customizer_value( 'aether_newsletter_text', '' ),
			'subtitle' => vineta_get_customizer_value( 'aether_newsletter_subtitle', '' ),
		),
		'social'       => vineta_get_customizer_value( 'aether_social_items', array() ),
		'usp_items'    => vineta_get_customizer_value( 'aether_footer_usp_items', array() ),
		'heading'      => vineta_get_customizer_value( 'aether_site_heading', '' ),
		'colors'       => array(
			'bg'           => vineta_get_customizer_value( 'aether_color_bg', '' ),
			'surface'      => vineta_get_customizer_value( 'aether_color_surface', '' ),
			'text'         => vineta_get_customizer_value( 'aether_color_text', '' ),
			'muted'        => vineta_get_customizer_value( 'aether_color_muted', '' ),
			'accent'       => vineta_get_customizer_value( 'aether_color_accent', '' ),
			'accent_hover' => vineta_get_customizer_value( 'aether_color_accent_hover', '' ),
			'border'       => vineta_get_customizer_value( 'aether_color_border', '' ),
		),
		'fonts'        => array(
			'heading' => vineta_get_customizer_value( 'aether_font_heading', '' ),
			'body'    => vineta_get_customizer_value( 'aether_font_body', '' ),
		),
	);

	return $page_data;
}

/**
 * Get navigation menu items in Vineta format.
 */
function vineta_get_nav_menu( $location ) {
	$menus = wp_get_nav_menus();
	$items = array();

	$theme_locations = get_nav_menu_locations();
	if ( empty( $theme_locations[ $location ] ) ) {
		return $items;
	}

	$menu_items = wp_get_nav_menu_items( $theme_locations[ $location ] );
	if ( ! is_array( $menu_items ) ) {
		return $items;
	}

	$all_items = array();
	foreach ( $menu_items as $item ) {
		$all_items[ $item->menu_item_parent ?: $item->ID ] = $item;
	}

	foreach ( $menu_items as $item ) {
		if ( $item->menu_item_parent ) {
			continue;
		}

		$entry = array(
			'title'    => $item->title,
			'url'      => $item->url,
			'children' => array(),
		);

		foreach ( $menu_items as $child ) {
			if ( (string) $child->menu_item_parent === (string) $item->ID ) {
				$entry['children'][] = array(
					'title' => $child->title,
					'url'   => $child->url,
				);
			}
		}

		$items[] = $entry;
	}

	return $items;
}

/**
 * Build VinetaPageData.product from WC data for single product pages.
 */
function vineta_build_product_page_data( $product_id ) {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return array();
	}

	if ( is_array( $product_id ) ) {
		$product_id = isset( $product_id['id'] ) ? (int) $product_id['id'] : 0;
	}
	if ( ! $product_id ) {
		global $post;
		$product_id = $post ? (int) $post->ID : 0;
	}
	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return array();
	}

	$product_type = $product->get_type();

	// Gallery images.
	$gallery = array();
	$image_id = $product->get_image_id();
	if ( $image_id ) {
		$gallery[] = array(
			'src' => wp_get_attachment_url( $image_id ),
			'alt' => $product->get_name(),
		);
	}
	foreach ( $product->get_gallery_image_ids() as $gid ) {
		$gallery[] = array(
			'src' => wp_get_attachment_url( $gid ),
			'alt' => $product->get_name(),
		);
	}

	if ( empty( $gallery ) ) {
		$pack_url = aether_pack_url();
		if ( $pack_url ) {
			$gallery[] = array(
				'src' => $pack_url . 'images/products/fashion/product-1.jpg',
				'alt' => $product->get_name(),
			);
		}
	}

	$price_cents = 0;
	if ( $product->get_price() ) {
		$price_cents = (int) round( (float) $product->get_price() * 100 );
	}

	$availability = 'out-of-stock';
	if ( $product->is_in_stock() ) {
		$availability = $product->managing_stock() && $product->get_stock_quantity() <= 5
			? 'low-stock'
			: 'in-stock';
	}

	// Variable product.
	$variants     = array();
	$options      = array();
	$price_varies = false;
	$price_min    = $price_cents;
	$price_max    = $price_cents;
	$selected_variant_id = null;

	if ( 'variable' === $product_type ) {
		$attributes = $product->get_attributes();
		foreach ( $attributes as $attr ) {
			$options[] = $attr->get_name();
		}

		$variation_ids = $product->get_children();
		$first         = true;

		foreach ( $variation_ids as $vid ) {
			$variation = wc_get_product( $vid );
			if ( ! $variation || 'publish' !== $variation->get_status() ) {
				continue;
			}

			$v_price = 0;
			if ( $variation->get_price() ) {
				$v_price = (int) round( (float) $variation->get_price() * 100 );
			}

			if ( $first ) {
				$price_min = $v_price;
				$price_max = $v_price;
			} else {
				if ( $v_price < $price_min ) {
					$price_min = $v_price;
				}
				if ( $v_price > $price_max ) {
					$price_max = $v_price;
				}
			}
			$first = false;

			$v_avail = 'out-of-stock';
			if ( $variation->is_in_stock() ) {
				$v_avail = $variation->managing_stock() && $variation->get_stock_quantity() <= 5
					? 'low-stock'
					: 'in-stock';
			}

			$v_image_url = '';
			$v_image_id  = $variation->get_image_id();
			if ( $v_image_id ) {
				$v_image_url = wp_get_attachment_url( $v_image_id );
			}

			$v_attrs = $variation->get_attributes();
			$option1 = null;
			$option2 = null;
			$option3 = null;
			$opt_idx = 0;
			foreach ( $v_attrs as $attr_name => $attr_val ) {
				if ( 0 === $opt_idx ) {
					$option1 = $attr_val;
				} elseif ( 1 === $opt_idx ) {
					$option2 = $attr_val;
				} elseif ( 2 === $opt_idx ) {
					$option3 = $attr_val;
				}
				$opt_idx++;
			}

			$variants[] = array(
				'id'                => $vid,
				'title'             => $variation->get_name(),
				'option1'           => $option1,
				'option2'           => $option2,
				'option3'           => $option3,
				'sku'               => $variation->get_sku(),
				'price'             => $v_price,
				'compare_at_price'  => $variation->get_sale_price()
					? (int) round( (float) $variation->get_regular_price() * 100 )
					: null,
				'available'         => 'out-of-stock' !== $v_avail,
				'inventory_quantity' => $variation->get_stock_quantity(),
				'featured_image'    => $v_image_url ? array(
					'id'  => $v_image_id,
					'src' => $v_image_url,
					'alt' => $variation->get_name(),
				) : null,
				'requires_shipping' => true,
				'taxable'           => true,
			);

			if ( null === $selected_variant_id && 'out-of-stock' !== $v_avail ) {
				$selected_variant_id = $vid;
			}
		}

		$price_varies = ( $price_min !== $price_max );
	}

	$gallery_urls = array();
	foreach ( $gallery as $g ) {
		$gallery_urls[] = $g['src'];
	}

	return array(
		'id'                  => $product->get_id(),
		'title'               => $product->get_name(),
		'handle'              => $product->get_slug(),
		'slug'                => $product->get_slug(),
		'url'                 => $product->get_permalink(),
		'sku'                 => $product->get_sku(),
		'price'               => $price_cents,
		'price_min'           => 'variable' === $product_type ? $price_min : $price_cents,
		'price_max'           => 'variable' === $product_type ? $price_max : $price_cents,
		'price_varies'        => $price_varies,
		'price_html'          => $product->get_price_html(),
		'compare_at_price'    => $product->get_sale_price()
			? (int) round( (float) $product->get_regular_price() * 100 )
			: null,
		'currency'            => get_woocommerce_currency(),
		'availability'        => $availability,
		'description'         => $product->get_short_description() ?: $product->get_description(),
		'gallery'             => $gallery,
		'images'              => $gallery_urls,
		'featured_image'      => ! empty( $gallery_urls ) ? $gallery_urls[0] : '',
		'options'             => $options,
		'variants'            => $variants,
		'selected_variant_id' => $selected_variant_id,
		'variant_id'          => $selected_variant_id,
		'badge'               => null,
		'product_type'        => $product_type,
		'tags'                => wp_get_post_terms( $product->get_id(), 'product_tag', array( 'fields' => 'names' ) ),
	);
}

// Store product data for VinetaPageData injection on single product pages.
add_action( 'wp', 'vineta_store_product_page_data' );
function vineta_store_product_page_data() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	global $post;
	if ( ! $post ) {
		return;
	}
	$GLOBALS['vineta_product_page_data'] = vineta_build_product_page_data( $post->ID );
}

// --- Collection/Archive Data ---
/**
 * Build home-page featured datasets (products + categories) from real
 * WordPress/WooCommerce data. Falls back to demo data only when no real
 * products/categories exist (demo mode).
 */
function vineta_build_home_data() {
	$data = array(
		'products'   => array(),
		'categories' => array(),
	);

	$products = array();
	if ( function_exists( 'wc_get_products' ) ) {
		$args = array(
			'status'  => 'publish',
			'limit'   => 8,
			'orderby' => 'date',
			'order'   => 'DESC',
			'return'  => 'objects',
		);
		$wc_products = wc_get_products( $args );
		foreach ( $wc_products as $product ) {
			if ( '1' === $product->get_meta( 'aureon_demo' ) ) {
				continue;
			}
			$mapped = vineta_map_wc_product( $product );
			if ( $mapped ) {
				$products[] = $mapped;
			}
		}
	}
	if ( ! empty( $products ) ) {
		$data['products'] = $products;
	} else {
		$data['products'] = vineta_get_demo_products_for_collection( '' );
	}

	$categories = array();
	if ( function_exists( 'get_terms' ) ) {
		$terms = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => 8,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'exclude'    => array( get_option( 'default_product_cat' ) ),
		) );
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$image_url = '';
				$thumb_id  = get_term_meta( $term->term_id, 'thumbnail_id', true );
				if ( $thumb_id ) {
					$image_url = wp_get_attachment_url( $thumb_id );
				}
				$categories[] = array(
					'name'  => $term->name,
					'slug'  => $term->slug,
					'url'   => get_term_link( $term ),
					'image' => $image_url,
					'count' => (int) $term->count,
				);
			}
		}
	}
	$data['categories'] = $categories;

	return $data;
}

/**
 * Map a WC_Product object to the Vineta product payload (shared by collection,
 * home-featured and product page data builders).
 */
function vineta_map_wc_product( $product ) {
	if ( ! is_object( $product ) || ! method_exists( $product, 'get_id' ) ) {
		return null;
	}
	$image_id     = $product->get_image_id();
	$image_url    = $image_id ? wp_get_attachment_url( $image_id ) : '';
	$gallery_urls = array();
	foreach ( $product->get_gallery_image_ids() as $gid ) {
		$gallery_urls[] = wp_get_attachment_url( $gid );
	}

	$price_cents = 0;
	if ( $product->get_price() ) {
		$price_cents = (int) round( (float) $product->get_price() * 100 );
	}
	$regular_cents = 0;
	if ( method_exists( $product, 'get_regular_price' ) && $product->get_regular_price() ) {
		$regular_cents = (int) round( (float) $product->get_regular_price() * 100 );
	}
	$sale_cents = 0;
	if ( method_exists( $product, 'get_sale_price' ) && $product->get_sale_price() ) {
		$sale_cents = (int) round( (float) $product->get_sale_price() * 100 );
	}
	$on_sale   = $product->is_on_sale();
	$hover_url = ! empty( $gallery_urls ) ? $gallery_urls[0] : '';

	return array(
		'id'            => $product->get_id(),
		'title'         => $product->get_name(),
		'handle'        => $product->get_slug(),
		'url'           => $product->get_permalink(),
		'sku'           => $product->get_sku(),
		'price'         => $price_cents,
		'price_regular' => $on_sale && $regular_cents ? $regular_cents : $price_cents,
		'price_sale'    => $on_sale && $sale_cents ? $sale_cents : 0,
		'on_sale'       => $on_sale,
		'type'          => method_exists( $product, 'get_type' ) ? $product->get_type() : 'simple',
		'price_html'    => method_exists( $product, 'get_price_html' ) ? $product->get_price_html() : '',
		'image'         => $image_url,
		'hover_image'   => $hover_url,
		'gallery'       => $gallery_urls,
		'available'     => $product->is_in_stock(),
		'badge'         => $on_sale ? 'Sale' : '',
	);
}

function vineta_build_collection_data() {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return array();
	}

	$products = array();
	$term = null;
	$term_id = 0;
	$term_name = '';
	$term_description = '';

	if ( is_tax( 'product_cat' ) ) {
		$term = get_queried_object();
		$term_id = $term->term_id;
		$term_name = $term->name;
		$term_description = $term->description;
	} elseif ( is_post_type_archive( 'product' ) || is_page( 'shop' ) ) {
		$term_name = 'Shop';
	}

	$args = array(
		'status'   => 'publish',
		'limit'    => 48,
		'orderby'  => 'date',
		'order'    => 'DESC',
		'return'   => 'objects',
	);
	if ( $term_id ) {
		$args['category'] = array( $term->slug );
	}

	$wc_products = wc_get_products( $args );

	$wc_products = array_filter( $wc_products, function( $product ) {
		$demo = $product->get_meta( 'aureon_demo' );
		return '1' !== $demo;
	} );

	foreach ( $wc_products as $product ) {
		$mapped = vineta_map_wc_product( $product );
		if ( $mapped ) {
			$products[] = $mapped;
		}
	}

	// Demo product fallback.
	// Demo product fallback.
	if ( empty( $products ) ) {
		$products = vineta_get_demo_products_for_collection( $term ? $term->slug : '' );
	}

	$term_image = '';
	if ( $term && ! empty( $term->term_id ) ) {
		$term_image_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
		if ( $term_image_id ) {
			$term_image = wp_get_attachment_url( $term_image_id );
		}
	}

	return array(
		'title'         => $term_name,
		'description'   => $term_description,
		'image'         => $term_image,
		'product_count' => count( $products ),
		'products'      => $products,
	);
}

/**
 * Build search-results dataset from real WooCommerce product search.
 * Same shape as vineta_build_collection_data() so the existing VinetaShop
 * grid consumer renders it. Never falls back to demo products: a search
 * with no matches must show an empty state, not demo cards.
 */
function vineta_build_search_data() {
	$query = trim( (string) get_search_query() );
	$products = array();

	if ( '' !== $query && function_exists( 'wc_get_product' ) ) {
		$ids = get_posts( array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 48,
			's'              => $query,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		) );
		foreach ( $ids as $pid ) {
			$product = wc_get_product( $pid );
			if ( ! $product || '1' === $product->get_meta( 'aureon_demo' ) ) {
				continue;
			}
			$mapped = vineta_map_wc_product( $product );
			if ( $mapped ) {
				$products[] = $mapped;
			}
		}
	}

	return array(
		'title'         => '' !== $query ? sprintf( 'Results for "%s"', $query ) : 'Search',
		'description'   => '',
		'image'         => '',
		'product_count' => count( $products ),
		'products'      => $products,
		'is_search'     => true,
		'query'         => $query,
	);
}

/**
 * Build blog-archive dataset (real WP posts) for the Vineta blog grid.
 * Falls back to demo entries only when no real posts exist.
 */
function vineta_build_blog_data() {
	$data = array( 'posts' => array() );

	$query = new WP_Query( array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'paged'          => max( 1, (int) get_query_var( 'paged' ) ),
	) );

	if ( $query->have_posts() ) {
		foreach ( $query->posts as $post ) {
			$cats = get_the_category( $post->ID );
			$data['posts'][] = array(
				'id'        => $post->ID,
				'title'     => get_the_title( $post ),
				'url'       => get_permalink( $post ),
				'date'      => get_the_date( 'M j Y', $post ),
				'author'    => get_the_author_meta( 'display_name', $post->post_author ),
				'image'     => get_the_post_thumbnail_url( $post->ID, 'large' ) ?: '',
				'excerpt'   => wp_trim_words( wp_strip_all_tags( $post->post_excerpt ?: wp_trim_words( $post->post_content, 60, '' ) ), 24 ),
				'category'  => ! empty( $cats ) && ! is_wp_error( $cats ) ? $cats[0]->name : '',
				'cat_url'   => ! empty( $cats ) && ! is_wp_error( $cats ) ? get_category_link( $cats[0]->term_id ) : '',
				'count'     => (int) $query->found_posts,
				'is_search' => false,
			);
		}
	}
	wp_reset_postdata();

	return $data;
}

/**
 * Build single-article dataset (real WP post) for the Vineta blog-single slots.
 */
function vineta_build_article_data( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return null;
	}

	$cats = get_the_category( $post->ID );
	$tags = get_the_tags( $post->ID );
	$image = get_the_post_thumbnail_url( $post->ID, 'large' ) ?: '';

	$tag_list = array();
	if ( $tags && ! is_wp_error( $tags ) ) {
		foreach ( $tags as $tag ) {
			$tag_list[] = array( 'name' => $tag->name, 'url' => get_tag_link( $tag->term_id ) );
		}
	}

	$content_html = $post->post_content;
	if ( function_exists( 'apply_filters' ) ) {
		$content_html = apply_filters( 'the_content', $content_html );
	}

	return array(
		'id'       => $post->ID,
		'title'    => get_the_title( $post ),
		'url'      => get_permalink( $post ),
		'date'     => get_the_date( 'M j Y', $post ),
		'author'   => get_the_author_meta( 'display_name', $post->post_author ),
		'image'    => $image,
		'category' => ! empty( $cats ) && ! is_wp_error( $cats ) ? $cats[0]->name : '',
		'cat_url'  => ! empty( $cats ) && ! is_wp_error( $cats ) ? get_category_link( $cats[0]->term_id ) : '',
		'tags'     => $tag_list,
		'content'  => $content_html,
		'comment_count' => (int) $post->comment_count,
		'related'  => array(),
	);

}

function vineta_get_demo_products_for_collection( $category_slug = '' ) {
	$pack_dir = aether_active_design_dir();
	$json_file = $pack_dir . 'demo/demo-products.json';
	if ( ! file_exists( $json_file ) ) {
		return array();
	}

	$raw = json_decode( (string) file_get_contents( $json_file ), true );
	if ( ! is_array( $raw ) ) {
		return array();
	}

	$all_products = isset( $raw['products'] ) && is_array( $raw['products'] )
		? $raw['products']
		: $raw;

	$result = array();
	$pack_url = aether_pack_url();
	foreach ( $all_products as $product ) {
		if ( ! is_array( $product ) ) {
			continue;
		}

		if ( $category_slug ) {
			$categories = isset( $product['categories'] ) ? $product['categories'] : array();
			if ( ! in_array( $category_slug, $categories, true ) ) {
				$collection = isset( $product['collection'] ) ? $product['collection'] : '';
				if ( $collection !== $category_slug ) {
					continue;
				}
			}
		}

		$image = isset( $product['image'] ) ? $product['image'] : '';
		if ( $image && strpos( $image, 'http' ) === false ) {
			$image = $pack_url . $image;
		}

		$demo_id = isset( $product['demo_id'] ) ? $product['demo_id'] : '';
		$result[] = array(
			'source'    => 'demo',
			'id'        => $demo_id,
			'demo_id'   => $demo_id,
			'title'     => isset( $product['name'] ) ? $product['name'] : '',
			'handle'    => isset( $product['slug'] ) ? $product['slug'] : '',
			'url'       => isset( $product['url'] ) ? $product['url'] : '#',
			'price'     => isset( $product['price_cents'] ) ? (int) $product['price_cents'] : 0,
			'price_html' => isset( $product['price'] ) ? $product['price'] : '',
			'image'     => $image,
			'available' => true,
			'badge'     => isset( $product['badge'] ) ? $product['badge'] : '',
		);
	}
	return $result;
}

// --- Search form bridge ---
add_action( 'wp_footer', 'vineta_search_bridge', 10 );
function vineta_search_bridge() {
	if ( ! function_exists( 'aether_active_design' ) || 'vineta' !== aether_active_design() ) {
		return;
	}
	if ( ! function_exists( 'aether_is_complete_page_design' ) || ! aether_is_complete_page_design() ) {
		return;
	}
	$search_url = esc_url( home_url( '/' ) );
	echo "<script>\n";
	echo "document.addEventListener('DOMContentLoaded',function(){\n";
	echo "  document.querySelectorAll('form.form-search').forEach(function(f){\n";
	echo "    f.setAttribute('action','{$search_url}');\n";
	echo "    var inp=f.querySelector('input[name=text]');\n";
	echo "    if(inp)inp.setAttribute('name','s');\n";
	echo "  });\n";
	echo "  var params=new URLSearchParams(window.location.search);\n";
	echo "  var q=params.get('s');\n";
	echo "  if(q){\n";
	echo "    document.querySelectorAll('form.form-search input[name=s]').forEach(function(i){i.value=q;});\n";
	echo "  }\n";
	echo "});\n";
	echo "</script>\n";
}

// --- Authentication form bridge (real WordPress/WooCommerce auth) ---
// The frozen Vineta account page ships three demo forms (#login, #register,
// #resetPass) whose inputs carry NO name attributes. Bridge each to the real
// WooCommerce contract so submit -> WC form handler -> redirect works, while
// Vineta presentation stays untouched.
add_action( 'wp_footer', 'vineta_auth_bridge', 10 );
function vineta_auth_bridge() {
	if ( ! function_exists( 'aether_active_design' ) || 'vineta' !== aether_active_design() ) {
		return;
	}
	if ( ! function_exists( 'aether_is_complete_page_design' ) || ! aether_is_complete_page_design() ) {
		return;
	}
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
		return;
	}
	if ( is_user_logged_in() ) {
		return; // logged-in users are served the WC-native account template
	}
	$my_account_url     = esc_url( wc_get_page_permalink( 'myaccount' ) );
	$lost_password_url  = esc_url( wc_get_account_endpoint_url( 'lost-password' ) );
	$login_nonce        = wp_create_nonce( 'woocommerce-login' );
	$register_nonce     = wp_create_nonce( 'woocommerce-register' );
	$lost_nonce         = wp_create_nonce( 'lost_password' );

	// WC notices (login errors, register errors, etc.) — the frozen Vineta page
	// has no notice container; print into one the bridge relocates above forms.
	// The lost-password success state is delivered via ?reset-link-sent and only
	// printed by WC's native templates, so synthesize the same confirmation here.
	ob_start();
	if ( function_exists( 'wc_print_notices' ) ) {
		wc_print_notices();
	}
	if ( isset( $_GET['reset-link-sent'] ) && function_exists( 'wc_add_notice' ) && function_exists( 'wc_print_notices' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only flag.
		wc_add_notice( __( 'Password reset email has been sent.', 'woocommerce' ), 'success' );
		wc_print_notices();
	}
	$wc_notices = (string) ob_get_clean();
	if ( '' !== trim( $wc_notices ) ) {
		echo '<div id="vineta-wc-notices" class="mb_16">' . wp_kses_post( $wc_notices ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- notices escaped via wc_print_notices.
	}

	echo "<script>\n";
	echo "document.addEventListener('DOMContentLoaded',function(){\n";
	echo "  function addHidden(f,n,v){var h=document.createElement('input');h.type='hidden';h.name=n;h.value=v;f.appendChild(h);}\n";
	echo "  function nameInput(scope,type,fallbackName,assign){var el=scope.querySelector('input[type=\"'+type+'\"]');if(el&&(!el.name||assign))el.setAttribute('name',fallbackName);return el;}\n";

	// --- #login form -> WC login ---
	echo "  var loginF=document.querySelector('#login form.form-login, form.form-login');\n";
	echo "  if(loginF&&!loginF.getAttribute('data-auth-wired')){\n";
	echo "    loginF.setAttribute('data-auth-wired','1');\n";
	echo "    loginF.setAttribute('method','post');\n";
	echo "    loginF.setAttribute('action','" . $my_account_url . "');\n";    echo "    var lu=nameInput(loginF,'email','username',true);if(lu)lu.setAttribute('type','text');\n";
	echo "    nameInput(loginF,'password','password',true);\n";
	echo "    addHidden(loginF,'login','1');\n";
	echo "    addHidden(loginF,'woocommerce-login-nonce','" . $login_nonce . "');\n";
	echo "    addHidden(loginF,'redirect','" . esc_url( wc_get_page_permalink( 'myaccount' ) ) . "');\n";
	echo "  }\n";

	// --- #register form -> WC register ---
	echo "  var regF=document.querySelector('#register form.form-login');\n";
	echo "  if(regF&&!regF.getAttribute('data-auth-wired')){\n";
	echo "    regF.setAttribute('data-auth-wired','1');\n";
	echo "    regF.setAttribute('method','post');\n";
	echo "    regF.setAttribute('action','" . $my_account_url . "');\n";
	echo "    nameInput(regF,'email','email',true);\n";
	echo "    nameInput(regF,'password','password',true);\n";
	echo "    addHidden(regF,'register','1');\n";
	echo "    addHidden(regF,'woocommerce-register-nonce','" . $register_nonce . "');\n";
	echo "    addHidden(regF,'redirect','" . esc_url( wc_get_page_permalink( 'myaccount' ) ) . "');\n";
	echo "  }\n";

	// --- #resetPass form -> WC lost password ---
	echo "  var lpF=document.querySelector('#resetPass form.form-login');\n";
	echo "  if(lpF&&!lpF.getAttribute('data-auth-wired')){\n";
	echo "    lpF.setAttribute('data-auth-wired','1');\n";
	echo "    lpF.setAttribute('method','post');\n";
	echo "    lpF.setAttribute('action','" . $lost_password_url . "');\n";
	echo "    var lpEmail=nameInput(lpF,'email','user_login',true);\n";
	echo "    addHidden(lpF,'wc_reset_password','1');\n";
	echo "    addHidden(lpF,'woocommerce-lost-password-nonce','" . $lost_nonce . "');\n";
	echo "  }\n";

	// --- enable submit buttons + any leftover demo handlers disabled by the template ---
	echo "  document.querySelectorAll('#login form button[type=submit],#register form button[type=submit],#resetPass form button[type=submit]').forEach(function(b){b.disabled=false;});\n";
	echo "  // Logout links anywhere -> real WP logout\n";
	echo "  document.querySelectorAll('a[href*=logout],a[href*=customer-logout]').forEach(function(a){a.href='" . esc_url( wp_logout_url( home_url() ) ) . "';});\n";

	// --- logged-out landing: replace the demo dashboard with real login/register ---
	// The frozen account template renders a demo dashboard (\"Hello Vineta Pham\").
	// For logged-out visitors, clear that demo region and show the real (already
	// wired) WC login + register forms inline, keeping Vineta form classes.
	echo "  (function(){\n";
	echo "    var host = document.querySelector('.my-acount-content.account-dashboard, .account-dashboard, .my-acount-content');\n";
	echo "    if(!host || !host.parentNode) return;\n";
	echo "    // Hide demo sidebar nav (real account menus appear after login).\n";
	echo "    document.querySelectorAll('.sidebar-account-wrap,.btn-sidebar-mb').forEach(function(el){el.style.display='none';});\n";
	echo "    function cloneForm(sel,title){\n";
	echo "      var src=document.querySelector(sel);\n";
	echo "      var form=src?src.querySelector('form'):null;\n";
	echo "      if(!form) return null;\n";
	echo "      var box=document.createElement('div'); box.className='col-lg-6';\n";
	echo "      var head=document.createElement('h3'); head.className='box-account-title display-sm fw-medium mb_16'; head.textContent=title;\n";
	echo "      box.appendChild(head); box.appendChild(form.cloneNode(true));\n";
	echo "      return box;\n";
	echo "    }\n";
	echo "    var row=document.createElement('div'); row.className='row g-5';\n";
	echo "    var loginBox=cloneForm('#login .popup-inner','" . esc_html__( 'Log in', 'aureon' ) . "');\n";
	echo "    var regBox=cloneForm('#register .popup-inner','" . esc_html__( 'Create an account', 'aureon' ) . "');\n";
	echo "    if(loginBox) row.appendChild(loginBox);\n";
	echo "    if(regBox) row.appendChild(regBox);\n";
	echo "    host.innerHTML=''; host.className='my-acount-content account-dashboard';\n";
	echo "    var notices=document.getElementById('vineta-wc-notices');\n";
	echo "    if(notices){ host.appendChild(notices); }\n";
	echo "    if(row.childNodes.length){ host.appendChild(row); }\n";
	echo "    // Re-wire cloned login/register contracts exactly like the popups above.\n";
	echo "    host.querySelectorAll('form').forEach(function(f){\n";
	echo "      var isLogin = !!f.querySelector('[name=username]');\n";
	echo "      var email=f.querySelector('input[type=email]');\n";
	echo "      var pass=f.querySelector('input[type=password]');\n";
	echo "      if(email&&!email.name) email.setAttribute('name', isLogin ? 'username' : 'email');\n";
	echo "      if(pass&&!pass.name) pass.setAttribute('name','password');\n";
	echo "      if(!f.querySelector('[name=woocommerce-login-nonce]')&&!f.querySelector('[name=woocommerce-register-nonce]')){\n";    echo "        addHidden(f,'woocommerce-login-nonce','" . $login_nonce . "');\n";
    echo "        addHidden(f,'login','1');\n";
	echo "        f.setAttribute('action','" . $my_account_url . "'); f.setAttribute('method','post');\n";
	echo "      }\n";
	echo "    });\n";
	echo "  })();\n";
	echo "});\n";
	echo "</script>\n";
}

