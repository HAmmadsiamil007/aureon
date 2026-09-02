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
			'variant_title' => '',
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
	if ( ! is_product() ) {
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
		' dimensions' => $product->get_dimensions( false ),
		'is_variable' => $product->is_type( 'variable' ),
		'variation_attributes' => $variation_attributes,
		'variations'  => $variations_data,
		'categories'  => wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) ),
		'tags'        => wp_get_post_terms( $product->get_id(), 'product_tag', array( 'fields' => 'names' ) ),
		'review_count' => $product->get_review_count(),
		'average_rating' => $product->get_average_rating(),
	);

	$GLOBALS['vineta_product_page_data'] = $product_data;

	// Output JavaScript to update DOM with product data
	$json = wp_json_encode( $product_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	echo "<script>\n";
	echo "document.addEventListener('DOMContentLoaded',function(){\n";
	echo "var p={$json};\n";
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
	echo "  if(newPrice)newPrice.textContent='$'+p.price;\n";
	echo "  if(oldPrice){\n";
	echo "    if(p.sale_price&&p.regular_price!=p.price){oldPrice.textContent='$'+p.regular_price;oldPrice.style.display='';}\n";
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
	echo "var desc=document.querySelector('[data-aureon-slot=\"product.short_description\"],.product-short-description');\n";
	echo "if(desc)desc.innerHTML=p.short_description||'';\n";
	// Update main image
	echo "var img=document.querySelector('.product-img img,[data-aureon-slot=\"product.image\"]');\n";
	echo "if(img){img.src=p.image;}\n";
	// Update gallery images
	echo "if(p.gallery.length){\n";
	echo "  var galImgs=document.querySelectorAll('.product-gallery img,.thumb-slide img');\n";
	echo "  p.gallery.forEach(function(url,i){if(galImgs[i])galImgs[i].src=url;});\n";
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
	echo "          if(priceEl)priceEl.textContent='$'+match.price;\n";
	echo "          var oldPriceEl=document.querySelector('.product-price .price-old');\n";
	echo "          if(oldPriceEl){\n";
	echo "            if(match.regular_price&&match.regular_price!=match.price){oldPriceEl.textContent='$'+match.regular_price;oldPriceEl.style.display='';}\n";
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
	// Inject WooCommerce add-to-cart and hook up buttons
	echo "var addBtns=document.querySelectorAll('a[href*=\"shoppingCart\"],.btn-add-to-cart,.add-to-cart-btn,.btn-submit-total,.single_add_to_cart_button,button[name=\"add-to-cart\"]');\n";
	echo "var qtyInput=document.querySelector('input[type=\"number\"],.quantity input');\n";
	echo "addBtns.forEach(function(btn){\n";
	echo "  btn.addEventListener('click',function(e){\n";
	echo "    e.preventDefault();\n";
	echo "    var q=qtyInput?parseInt(qtyInput.value)||1:1;\n";
	echo "    var data={action:'vineta_add_to_cart',product_id:p.id,quantity:q};\n";
	echo "    if(p.is_variable&&window.vinetaSelectedVariationId){\n";
	echo "      data.variation_id=window.vinetaSelectedVariationId;\n";
	echo "    }\n";
	echo "    fetch(vineta_bridge.ajax_url,{\n";
	echo "      method:'POST',\n";
	echo "      headers:{'Content-Type':'application/x-www-form-urlencoded'},\n";
	echo "      body:new URLSearchParams(data),\n";
	echo "      credentials:'same-origin'\n";
	echo "    }).then(function(r){return r.json();}).then(function(res){\n";
	echo "      if(res.success){\n";
	echo "        window.dispatchEvent(new CustomEvent('vineta:cart-updated',{detail:{productId:p.id,quantity:q,cart:res.data}}));\n";
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
	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$quantity    = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;
	$variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;

	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => 'Invalid product' ) );
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

// --- Inject cart data on cart page ---
add_action( 'wp_head', 'vineta_inject_cart_data', 5 );
function vineta_inject_cart_data() {
	if ( ! function_exists( 'aether_active_design' ) || 'vineta' !== aether_active_design() ) {
		return;
	}
	if ( ! function_exists( 'aether_is_complete_page_design' ) || ! aether_is_complete_page_design() ) {
		return;
	}
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}
	if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
		return;
	}

	$cart = WC()->cart;
	$cart_data = array(
		'items'       => array(),
		'item_count'  => (int) $cart->get_cart_contents_count(),
		'total_price' => (int) round( (float) $cart->cart_contents_total * 100 ),
		'subtotal'    => (int) round( (float) $cart->subtotal * 100 ),
		'currency'    => get_woocommerce_currency(),
	);

	foreach ( $cart->get_cart() as $key => $item ) {
		$product = isset( $item['data'] ) ? $item['data'] : null;
		if ( ! $product ) {
			continue;
		}
		$image_id  = $product->get_image_id();
		$image_url = $image_id ? wp_get_attachment_url( $image_id ) : wc_placeholder_img_src();

		$cart_data['items'][] = array(
			'key'         => $key,
			'product_id'  => $item['product_id'],
			'quantity'    => $item['quantity'],
			'title'       => $product->get_name(),
			'sku'         => $product->get_sku(),
			'price'       => (int) round( (float) $product->get_price() * 100 ),
			'image'       => $image_url,
			'permalink'   => get_permalink( $product->get_id() ),
			'line_price'  => (int) round( (float) $item['line_total'] * 100 ),
		);
	}

	$json = wp_json_encode( $cart_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	echo "<script>\n";
	echo "document.addEventListener('DOMContentLoaded',function(){\n";
	echo "var c={$json};\n";
	echo "var badges=document.querySelectorAll('.tf-mini-cart-count,.shopping-cart-count,.cart-count');\n";
	echo "badges.forEach(function(b){b.textContent=c.item_count;});\n";
	echo "function fmtPrice(v){return '$'+(parseFloat(v)/100).toFixed(2);}\n";
	echo "function refreshCartUI(d){\n";
	echo "  if(!d)return;\n";
	echo "  if(totalEl)totalEl.textContent=fmtPrice(d.total_price)+' USD';\n";
	echo "  var subtEl=document.querySelector('.cart-head .subtotal');\n";
	echo "  if(subtEl)subtEl.textContent=fmtPrice(d.total_price)+' USD';\n";
	echo "  var rows=document.querySelectorAll('.tf-cart-item');\n";
	echo "  rows.forEach(function(row,i){\n";
	echo "    var it=d.items[i];\n";
	echo "    if(!it){row.remove();return;}\n";
	echo "    var img=row.querySelector('img');\n";
	echo "    if(img&&it.image)img.src=it.image;\n";
	echo "    var name=row.querySelector('.name');\n";
	echo "    if(name){name.textContent=it.title||it.name;name.href=it.permalink||'#';}\n";
	echo "    var priceEl=row.querySelector('.tf-cart-item_price');\n";
	echo "    if(priceEl)priceEl.textContent=fmtPrice(it.price);\n";
	echo "    var qty=row.querySelector('input[type=\"number\"]');\n";
	echo "    if(qty)qty.value=it.quantity;\n";
	echo "    var totalEl2=row.querySelector('.tf-cart-item_total');\n";
	echo "    if(totalEl2)totalEl2.textContent=fmtPrice(it.line_price);\n";
	echo "    row.setAttribute('data-cart-key',it.key||'');\n";
	echo "  });\n";
	echo "  if(d.item_count===0){\n";
	echo "    var cartTable=document.querySelector('table');\n";
	echo "    if(cartTable)cartTable.innerHTML='<tbody><tr><td colspan=\"5\" class=\"text-center\">Your cart is empty</td></tr></tbody>';\n";
	echo "  }\n";
	echo "}\n";
	echo "var rows=document.querySelectorAll('.tf-cart-item');\n";
	echo "if(rows.length&&c.items.length){\n";
	echo "  for(var ri=c.items.length;ri<rows.length;ri++){rows[ri].remove();}\n";
	echo "  rows=document.querySelectorAll('.tf-cart-item');\n";
	echo "  rows.forEach(function(row,i){\n";
	echo "    if(!c.items[i])return;\n";
	echo "    var it=c.items[i];\n";
	echo "    var img=row.querySelector('img');\n";
	echo "    if(img&&it.image)img.src=it.image;\n";
	echo "    var name=row.querySelector('.name');\n";
	echo "    if(name){name.textContent=it.title||it.name;name.href=it.permalink||'#';}\n";
	echo "    var priceEl=row.querySelector('.tf-cart-item_price');\n";
	echo "    if(priceEl)priceEl.textContent=fmtPrice(it.price);\n";
	echo "    var qty=row.querySelector('input[type=\"number\"]');\n";
	echo "    if(qty)qty.value=it.quantity;\n";
	echo "    var totalEl2=row.querySelector('.tf-cart-item_total');\n";
	echo "    if(totalEl2)totalEl2.textContent=fmtPrice(it.line_price);\n";
	echo "    row.setAttribute('data-cart-key',it.key||'');\n";
	echo "  });\n";
	echo "}\n";
	echo "var totalEl=document.querySelector('.cart-head .total');\n";
	echo "if(totalEl)totalEl.textContent=fmtPrice(c.total_price)+' USD';\n";
	echo "rows.forEach(function(row){\n";
	echo "  var qtyInput=row.querySelector('input[type=\"number\"]');\n";
	echo "  var incBtn=row.querySelector('.btn-increase');\n";
	echo "  var decBtn=row.querySelector('.btn-decrease');\n";
	echo "  var removeBtn=row.querySelector('.remove-cart');\n";
	echo "  function updateRowQty(newQty){\n";
	echo "    if(newQty<1)return;\n";
	echo "    var itemKey=row.getAttribute('data-cart-key')||'';\n";
	echo "    if(!itemKey)return;\n";
	echo "    var updates={};\n";
	echo "    updates[itemKey]=newQty;\n";
	echo "    var fd=new FormData();\n";
	echo "    fd.append('action','vineta_cart_update');\n";
	echo "    fd.append('nonce',vineta_bridge.nonce);\n";
	echo "    fd.append('updates',JSON.stringify(updates));\n";
	echo "    fetch(vineta_bridge.ajax_url,{method:'POST',body:fd,credentials:'same-origin'})\n";
	echo "    .then(function(r){return r.json();})\n";
	echo "    .then(function(res){\n";
	echo "      if(res.success)refreshCartUI(res.data);\n";
	echo "    });\n";
	echo "  }\n";
	echo "  if(incBtn)incBtn.addEventListener('click',function(){\n";
	echo "    var q=parseInt(qtyInput.value)||1;\n";
	echo "    qtyInput.value=q+1;\n";
	echo "    updateRowQty(q+1);\n";
	echo "  });\n";
	echo "  if(decBtn)decBtn.addEventListener('click',function(){\n";
	echo "    var q=parseInt(qtyInput.value)||1;\n";
	echo "    if(q>1){qtyInput.value=q-1;updateRowQty(q-1);}\n";
	echo "  });\n";
	echo "  if(removeBtn)removeBtn.addEventListener('click',function(e){\n";
	echo "    e.preventDefault();\n";
	echo "    var itemKey=row.getAttribute('data-cart-key')||'';\n";
	echo "    if(!itemKey)return;\n";
	echo "    var fd=new FormData();\n";
	echo "    fd.append('action','vineta_cart_update');\n";
	echo "    fd.append('nonce',vineta_bridge.nonce);\n";
	echo "    var updates={};\n";
	echo "    updates[itemKey]=0;\n";
	echo "    fd.append('updates',JSON.stringify(updates));\n";
	echo "    fetch(vineta_bridge.ajax_url,{method:'POST',body:fd,credentials:'same-origin'})\n";
	echo "    .then(function(r){return r.json();})\n";
	echo "    .then(function(res){\n";
	echo "      if(res.success)refreshCartUI(res.data);\n";
	echo "    });\n";
	echo "  });\n";
	echo "});\n";
	echo "});\n";
	echo "</script>\n";
}

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
				'variant_title' => '',
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
	} elseif ( is_product() ) {
		$template = 'product';
	} elseif ( is_404() ) {
		$template = '404';
	} elseif ( is_post_type_archive( 'product' ) || is_page( 'shop' ) ) {
		$template = 'collection';
	} elseif ( is_tax( 'product_cat' ) ) {
		$template = 'collection';
	} elseif ( is_cart() ) {
		$template = 'cart';
	} elseif ( is_checkout() ) {
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

	// Inject product data on single product pages.
	if ( ! empty( $GLOBALS['vineta_product_page_data'] ) ) {
		$page_data['product'] = $GLOBALS['vineta_product_page_data'];
	}

	// Inject collection data on product archive/category pages.
	if ( ( is_tax( 'product_cat' ) || is_post_type_archive( 'product' ) || is_page( 'shop' ) ) && ! is_product() ) {
		$page_data['collection'] = vineta_build_collection_data();
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
		'announcement' => aureon_get_option( 'aether_announcement_items', array() ),
		'hero'         => aureon_get_option( 'aether_hero_slides', array() ),
		'categories'   => aureon_get_option( 'aether_category_items', array() ),
		'footer'       => aureon_get_option( 'aether_footer_columns', array() ),
		'newsletter'   => array(
			'heading'  => aureon_get_option( 'aether_newsletter_heading', '' ),
			'text'     => aureon_get_option( 'aether_newsletter_text', '' ),
			'subtitle' => aureon_get_option( 'aether_newsletter_subtitle', '' ),
		),
		'social'       => aureon_get_option( 'aether_social_items', array() ),
		'usp_items'    => aureon_get_option( 'aether_footer_usp_items', array() ),
		'heading'      => get_option( 'aether_site_heading', '' ),
		'colors'       => array(
			'bg'           => aureon_get_option( 'aether_color_bg', '' ),
			'surface'      => aureon_get_option( 'aether_color_surface', '' ),
			'text'         => aureon_get_option( 'aether_color_text', '' ),
			'muted'        => aureon_get_option( 'aether_color_muted', '' ),
			'accent'       => aureon_get_option( 'aether_color_accent', '' ),
			'accent_hover' => aureon_get_option( 'aether_color_accent_hover', '' ),
			'border'       => aureon_get_option( 'aether_color_border', '' ),
		),
		'fonts'        => array(
			'heading' => aureon_get_option( 'aether_font_heading', '' ),
			'body'    => aureon_get_option( 'aether_font_body', '' ),
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
function vineta_build_collection_data() {
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
		$image_id = $product->get_image_id();
		$image_url = $image_id ? wp_get_attachment_url( $image_id ) : '';
		$gallery_urls = array();
		foreach ( $product->get_gallery_image_ids() as $gid ) {
			$gallery_urls[] = wp_get_attachment_url( $gid );
		}

		$price_cents = 0;
		if ( $product->get_price() ) {
			$price_cents = (int) round( (float) $product->get_price() * 100 );
		}

		$products[] = array(
			'id'         => $product->get_id(),
			'title'      => $product->get_name(),
			'handle'     => $product->get_slug(),
			'url'        => $product->get_permalink(),
			'sku'        => $product->get_sku(),
			'price'      => $price_cents,
			'price_html' => $product->get_price_html(),
			'image'      => $image_url,
			'gallery'    => $gallery_urls,
			'available'  => $product->is_in_stock(),
			'badge'      => $product->is_on_sale() ? 'Sale' : '',
		);
	}

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

// --- Inject search results on search page ---
add_action( 'wp_head', 'vineta_inject_search_results', 5 );
function vineta_inject_search_results() {
	if ( ! function_exists( 'aether_active_design' ) || 'vineta' !== aether_active_design() ) {
		return;
	}
	if ( ! function_exists( 'aether_is_complete_page_design' ) || ! aether_is_complete_page_design() ) {
		return;
	}
	if ( ! is_search() ) {
		return;
	}
	$query = get_search_query();
	$results = array();
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$post_id = get_the_ID();
			$product = wc_get_product( $post_id );
			$image_url = get_the_post_thumbnail_url( $post_id, 'medium' ) ?: '';
			$price = '';
			if ( $product ) {
				$price_cents = (int) round( (float) $product->get_price() * 100 );
				$price = '$' . number_format( $price_cents / 100, 2 );
			}
			$results[] = array(
				'id'      => $post_id,
				'title'   => get_the_title(),
				'url'     => get_permalink(),
				'image'   => $image_url,
				'excerpt' => wp_trim_words( get_the_excerpt(), 20 ),
				'price'   => $price,
				'type'    => $product ? 'product' : 'post',
			);
		}
	}
	wp_reset_postdata();
	$json = wp_json_encode( array( 'query' => $query, 'results' => $results, 'count' => count( $results ) ), JSON_UNESCAPED_SLASHES );
	echo "<script>\n";
	echo "document.addEventListener('DOMContentLoaded',function(){\n";
	echo '<script>' . "\n";
	echo 'document.addEventListener("DOMContentLoaded",function(){' . "\n";
	echo 'var s=' . $json . ';' . "\n";
	echo 'var c=document.querySelector(".shop-product-grid,.product-grid,.tf-product-grid,.blog-grid");' . "\n";
	echo 'if(!c)c=document.querySelector(".tf-content-inner,.content-wrapper,.main-content");' . "\n";
	echo 'if(!c)return;' . "\n";
	echo 'var h="<div class=search-results-header mb-4><h2>Search results for: "+s.query+"</h2><p>"+s.count+" result"+(s.count!==1?"s":"")+" found</p></div>";' . "\n";
	echo 'if(s.results.length){' . "\n";
	echo 'h+="<div class=row g-3>";' . "\n";
	echo 's.results.forEach(function(r){' . "\n";
	echo 'h+="<div class=col-6 col-md-4 col-lg-3><div class=product-card>";' . "\n";
	echo 'h+="<a href="+r.url+">";' . "\n";
	echo 'if(r.image)h+="<img src="+r.image+" alt="+r.title+" class=img-fluid loading=lazy>";' . "\n";
	echo 'h+="</a><div class=info p-2><h6><a href="+r.url+">"+r.title+"</a></h6>";' . "\n";
	echo 'if(r.price)h+="<div class=price>"+r.price+"</div>";' . "\n";
	echo 'h+="</div></div></div>";' . "\n";
	echo '});' . "\n";
	echo 'h+="</div>";' . "\n";
	echo '}else{' . "\n";
	echo 'h+="<div class=text-center py-5><p>No results found</p></div>";' . "\n";
	echo '}' . "\n";
	echo 'c.innerHTML=h;' . "\n";
	echo '});' . "\n";
	echo '</script>' . "\n";
}

// --- Authentication form bridge ---
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
	$my_account_url = esc_url( wc_get_page_permalink( 'myaccount' ) );
	$lost_password_url = esc_url( wc_lostpassword_url() );
	echo "<script>\n";
	echo 'document.addEventListener("DOMContentLoaded",function(){' . "\n";
	// Fix login form
	echo 'var loginForm=document.querySelector("#customer_login .login form,form.form-login");' . "\n";
	echo 'if(loginForm){' . "\n";
	echo '  loginForm.setAttribute("action","' . $my_account_url . '");' . "\n";
	echo '  var emailField=loginForm.querySelector("input[name*=email],input[name*=user]");' . "\n";
	echo '  if(emailField&&emailField.name!=="username")emailField.setAttribute("name","username");' . "\n";
	echo '}' . "\n";
	// Fix registration form
	echo 'var regForms=document.querySelectorAll("form.form-login");' . "\n";
	echo 'regForms.forEach(function(f){' . "\n";
	echo '  if(f.querySelector("input[name=register_email],input[name=email]")){' . "\n";
	echo '    f.setAttribute("action","' . $my_account_url . '");' . "\n";
	echo '  }' . "\n";
	echo '});' . "\n";
	// Fix lost password link
	echo 'var lpLinks=document.querySelectorAll("a[href*=lost-password],a[href*=recover],a[href*=\"#recover\"]");' . "\n";
	echo 'lpLinks.forEach(function(a){a.setAttribute("href","' . $lost_password_url . '");});' . "\n";
	// Fix logout link
	echo 'var logoutLinks=document.querySelectorAll("a[href*=logout],a[href*=customer-logout]");' . "\n";
	echo 'logoutLinks.forEach(function(a){a.setAttribute("href","' . esc_url( wp_logout_url( home_url() ) ) . '");});' . "\n";
	echo '});' . "\n";
	echo "</script>\n";
}
