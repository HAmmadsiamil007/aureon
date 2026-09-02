<?php
/**
 * Ferm Living Design Pack — Thin Composer (Data Bridge)
 *
 * Maps AUREON/WooCommerce data to Ferm presentation format.
 * Handles cart AJAX, demo data fallback, and product remapping.
 *
 * This file is loaded by the frontend engine for the fermliving design.
 * It does NOT contain any presentation logic — only data transformation.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
	return;
}

// --- Disable external Google Fonts (self-hosted via pack CSS) ---
// AUREON enqueues Google Fonts via wp_enqueue_scripts at priority 0.
// We dequeue the stylesheet after it's been enqueued.
add_action( 'wp_enqueue_scripts', 'ferm_disable_google_fonts', 999 );
function ferm_disable_google_fonts() {
	wp_dequeue_style( 'aureon-google-fonts' );
	wp_deregister_style( 'aureon-google-fonts' );
}

// --- Mapper (product remapping helpers) ---
$mapper = __DIR__ . '/mapper/ferm-mapper.php';
if ( file_exists( $mapper ) ) {
	require_once $mapper;
}

// --- Homepage section sequence ---
add_filter( 'aether_frontpage_sections', 'ferm_homepage_sections' );
function ferm_homepage_sections() {
	return array( 'hero', 'categories', 'editorial-split', 'bestsellers', 'room-grid', 'newsletter' );
}

// --- Site data ---
add_filter( 'aether_adapter_site_data', 'ferm_site_data' );
function ferm_site_data( $data ) {
	if ( is_array( $data ) ) {
		$data['name'] = 'Ferm Living';
	}
	return $data;
}

// --- Header data ---
add_filter( 'aether_adapter_header_data', 'ferm_header_data' );
function ferm_header_data( $data ) {
	$cart_count = 0;
	if ( function_exists( 'WC' ) && WC()->cart ) {
		$cart_count = (int) WC()->cart->get_cart_contents_count();
	}
	$data['cart_count'] = $cart_count;
	$data['is_home']    = is_front_page() || ( is_home() && ! is_paged() );
	return $data;
}

// --- Footer data ---
add_filter( 'aether_adapter_footer_data', 'ferm_footer_data' );
function ferm_footer_data( $data ) {
	return array(
		'usp_items'  => aureon_get_option( 'aether_footer_usp_items', array() ),
		'newsletter' => array(
			'heading' => aureon_get_option( 'aether_newsletter_heading', 'Ferm Living news' ),
			'text'    => aureon_get_option( 'aether_newsletter_text', '' ),
		),
		'columns'    => aureon_get_option( 'aether_footer_columns', array() ),
		'legal'      => array(
			array( 'label' => 'Terms and Conditions', 'url' => '#' ),
			array( 'label' => 'Privacy Policy', 'url' => '#' ),
			array( 'label' => 'Cookies', 'url' => '#' ),
			array( 'label' => 'Follow Us', 'url' => '#' ),
		),
		'payments'   => aureon_get_option( 'aether_footer_payments', array() ),
		'socials'    => aureon_get_option( 'aether_social_items', array() ),
	);
}

// --- WC Products data mapping ---
add_filter( 'aether_adapter_wc_products_data', 'ferm_wc_products_data' );
function ferm_wc_products_data( $data ) {
	if ( is_array( $data ) && isset( $data['items'] ) && is_array( $data['items'] ) ) {
		foreach ( $data['items'] as &$product ) {
			$product = ferm_remap_product( $product );
		}
	}
	return $data;
}

// --- Single product data ---
add_filter( 'aether_adapter_product_data', 'ferm_product_data' );
function ferm_product_data( $data ) {
	return ferm_remap_product( $data );
}

// --- WC Categories ---
add_filter( 'aether_adapter_wc_categories_data', 'ferm_wc_categories_data' );
function ferm_wc_categories_data( $data ) {
	return $data;
}

// --- WC Filter ---
add_filter( 'aether_adapter_wc_filter_data', 'ferm_wc_filter_data' );
function ferm_wc_filter_data( $data ) {
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
add_filter( 'aether_adapter_blog_data', 'ferm_blog_data' );
function ferm_blog_data( $data ) {
	if ( is_array( $data ) ) {
		$data['label'] = 'Stories';
		$data['title'] = 'From the Ferm Living Journal';
	}
	return $data;
}

// --- About ---
add_filter( 'aether_adapter_about_data', 'ferm_about_data' );
function ferm_about_data( $data ) {
	return array(
		'heading'  => aureon_get_option( 'aether_about_heading', 'About Ferm Living' ),
		'body'     => aureon_get_option( 'aether_about_body', '' ),
		'features' => aureon_get_option( 'aether_about_features', array() ),
		'values'   => aureon_get_option( 'aether_about_values', array() ),
		'stats'    => aureon_get_option( 'aether_about_stats', array() ),
	);
}

// --- Contact ---
add_filter( 'aether_adapter_contact_data', 'ferm_contact_data' );
function ferm_contact_data( $data ) {
	return array(
		'heading' => 'Get in Touch',
		'address' => "Ferm Living ApS\nNørrebrogade 42\n2200 Copenhagen N\nDenmark",
		'phone'   => '+45 7022 7523',
		'email'   => 'info@fermliving.com',
		'hours'   => 'Monday - Friday: 9:00 - 17:00 CET',
	);
}

// --- Search ---
add_filter( 'aether_adapter_search_data', 'ferm_search_data' );
function ferm_search_data( $data ) {
	return array(
		'placeholder' => aureon_get_option( 'aether_search_placeholder', 'Search Ferm Living...' ),
		'suggestions'  => array( 'Furniture', 'Lighting', 'Accessories', 'Kids', 'Kitchen' ),
	);
}

// --- Newsletter ---
add_filter( 'aether_adapter_newsletter_data', 'ferm_newsletter_data' );
function ferm_newsletter_data( $data ) {
	return array(
		'heading' => aureon_get_option( 'aether_newsletter_heading', 'Ferm Living news' ),
		'text'    => aureon_get_option( 'aether_newsletter_text', 'Get exclusive drops, early access, and Ferm Living news.' ),
	);
}

// --- Demo Products (fallback when no WooCommerce products exist) ---
add_filter( 'aether_demo_products', 'ferm_demo_products', 10, 2 );
function ferm_demo_products( $items, $query_args ) {
	$pack_dir = aether_active_design_dir();
	$json_file = $pack_dir . 'demo/demo-products.json';
	if ( ! file_exists( $json_file ) ) {
		// Fallback to legacy location.
		$json_file = $pack_dir . 'data/products.json';
	}
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
		// Use demo_id for stable identification, fall back to numeric id.
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
			'swatches'        => isset( $product['colors'] ) ? ferm_format_swatches( $product['colors'], $product['url'] ?? '' ) : array(),
		);
	}
	$per_page = isset( $query_args['posts_per_page'] ) ? (int) $query_args['posts_per_page'] : 8;
	$paged    = isset( $query_args['paged'] ) ? (int) $query_args['paged'] : 1;
	return array_slice( $result, ( $paged - 1 ) * $per_page, $per_page );
}

// --- Demo Categories (fallback) ---
add_filter( 'aether_demo_categories', 'ferm_demo_categories', 10, 2 );
function ferm_demo_categories( $items, $args ) {
	$pack_dir  = aether_active_design_dir();
	$json_file = $pack_dir . 'demo/demo-categories.json';
	if ( ! file_exists( $json_file ) ) {
		// Fallback to legacy location.
		$json_file = $pack_dir . 'data/categories.json';
	}
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
 * Demo modes:
 *   'auto'       → real content exists → hide demos; no real → show demos
 *   'force_demo' → show demos regardless of real content (admin/dev only)
 *   'disabled'   → never show demo content (client handoff)
 *
 * MODE SCOPE:
 *   AUTO       → client/default
 *   FORCE_DEMO → admin/development/demo environment only
 *   DISABLED   → client handoff when real content is complete
 *
 * FORCE_DEMO controls PRESENTATION only — it does NOT:
 *   - delete real WooCommerce data
 *   - hide real products from admin
 *   - affect checkout/cart for real products
 *   - modify WooCommerce business logic
 */
function ferm_get_demo_mode() {
	$mode = aureon_get_option( 'aether_demo_mode', 'auto' );
	if ( ! in_array( $mode, array( 'auto', 'force_demo', 'disabled' ), true ) ) {
		$mode = 'auto';
	}
	return $mode;
}

// --- Check if demo content should be shown ---
function ferm_show_demo_content() {
	$mode = ferm_get_demo_mode();
	if ( 'disabled' === $mode ) {
		return false;
	}
	if ( 'force_demo' === $mode ) {
		return true;
	}
	// AUTO mode: show demo only when no real content exists.
	return true; // Default — filtering happens at query level.
}

/**
 * Check if real products exist (not demo).
 *
 * REAL CLIENT PRODUCT =
 *   published
 *   + public/catalog-eligible
 *   + not marked demo (aureon_demo != 1)
 *
 * Excludes: trash, draft, private, pending, auto-draft.
 * This prevents a product created during setup from triggering
 * demo hiding before the client has actually published a real product.
 */
function ferm_has_real_products() {
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
	// Verify at least one published product is not demo and is public.
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

/**
 * Check if real categories exist (not demo).
 *
 * REAL CLIENT CATEGORY =
 *   valid/public WooCommerce category
 *   + not marked demo (aureon_demo_category != 1)
 *   + has published products (hide_empty=true semantics)
 */
function ferm_has_real_categories() {
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
 *
 * When real client products exist, automatically hide products marked
 * aureon_demo=true from client-facing queries. Demo records are never
 * deleted — just filtered out when real content is present.
 *
 * DEMO FILTERS ARE PRESENTATION/QUERY CONTROLS,
 * NOT DESTRUCTIVE ADMIN DATA DELETION.
 *
 * Scope: front-end only (is_admin() check).
 * Admin products list shows ALL products including demo.
 */
add_action( 'woocommerce_product_query', 'ferm_filter_demo_products' );
function ferm_filter_demo_products( $q ) {
	static $in_filter = false;
	if ( $in_filter || is_admin() ) {
		return;
	}
	$in_filter = true;

	// Only filter when real products exist or FORCE_DEMO is active.
	$mode = ferm_get_demo_mode();
	if ( 'disabled' === $mode ) {
		$in_filter = false;
		return;
	}

	// Check if any real (non-demo) products exist.
	$has_real = ferm_has_real_products();

	if ( 'force_demo' === $mode ) {
		// FORCE_DEMO: show everything including demo — don't filter.
		$in_filter = false;
		return;
	}

	// AUTO mode: if real products exist, filter out demo products.
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
 *
 * When real categories exist, hide demo categories from results.
 * Uses a static guard to prevent recursion (get_terms triggers this filter).
 *
 * Scope: front-end only (is_admin() check).
 * Admin category list shows ALL categories including demo.
 */
add_filter( 'get_terms', 'ferm_filter_demo_categories', 10, 3 );
function ferm_filter_demo_categories( $terms, $taxonomies, $args ) {
	static $in_filter = false;
	if ( $in_filter || is_admin() || ! in_array( 'product_cat', (array) $taxonomies, true ) ) {
		return $terms;
	}

	$mode = ferm_get_demo_mode();
	if ( 'disabled' === $mode || 'force_demo' === $mode ) {
		return $terms;
	}

	$in_filter = true;

	// Check if any non-demo categories exist.
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
		return $terms; // No real categories — show demo.
	}
	// Filter out demo categories from results.
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
add_action( 'wp_ajax_ferm_cart_add', 'ferm_wc_ajax_cart_add' );
add_action( 'wp_ajax_nopriv_ferm_cart_add', 'ferm_wc_ajax_cart_add' );
add_action( 'wp_ajax_ferm_cart_update', 'ferm_wc_ajax_cart_update' );
add_action( 'wp_ajax_nopriv_ferm_cart_update', 'ferm_wc_ajax_cart_update' );
add_action( 'wp_ajax_ferm_cart_get', 'ferm_wc_ajax_cart_get' );
add_action( 'wp_ajax_nopriv_ferm_cart_get', 'ferm_wc_ajax_cart_get' );

/**
 * Cart AJAX: Add to Cart
 *
 * CART SAFETY — MULTI-LAYER PROTECTION:
 * 1. Frontend guard: demo products have purchasable=false, button hidden
 * 2. AJAX business-boundary guard: this function checks aureon_demo meta
 * 3. Server-side purchase validation: WC checkout validates product status
 *
 * DEMO PRODUCT → never purchasable, never addable to cart,
 *                never allowed into checkout, never orderable.
 */
function ferm_wc_ajax_cart_add() {
	check_ajax_referer( 'ferm_cart_nonce', 'nonce' );
	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( 'WooCommerce not available' );
	}
	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$quantity   = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;
	if ( ! $product_id ) {
		wp_send_json_error( 'Invalid product' );
	}

	// --- CART SAFETY: Demo products must not enter real cart ---
	// This is the business-boundary guard. Even if the frontend guard is
	// bypassed (direct request, malicious input), this check prevents
	// demo products from entering the real WooCommerce cart.
	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		wp_send_json_error( 'Product not found' );
	}

	// Check 1: Demo flag.
	$demo_flag = $product->get_meta( 'aureon_demo' );
	if ( '1' === $demo_flag ) {
		wp_send_json_error( 'Demo products are not available for purchase' );
	}

	// Check 2: Product must be published and publicly visible.
	if ( 'publish' !== $product->get_status() ) {
		wp_send_json_error( 'Product is not available' );
	}

	// Check 3: Product must be in stock.
	if ( ! $product->is_in_stock() ) {
		wp_send_json_error( 'Product is out of stock' );
	}

	$added = WC()->cart->add_to_cart( $product_id, $quantity );
	if ( $added ) {
		$response = ferm_build_cart_response();
		wp_send_json_success( $response );
	} else {
		wp_send_json_error( 'Could not add to cart' );
	}
}

function ferm_wc_ajax_cart_update() {
	check_ajax_referer( 'ferm_cart_nonce', 'nonce' );
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
	$response = ferm_build_cart_response();
	wp_send_json_success( $response );
}

function ferm_wc_ajax_cart_get() {
	check_ajax_referer( 'ferm_cart_nonce', 'nonce' );
	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( 'WooCommerce not available' );
	}
	$response = ferm_build_cart_response();
	wp_send_json_success( $response );
}

function ferm_build_cart_response() {
	$cart   = WC()->cart;
	$items  = array();
	foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
		$product = $cart_item['data'];
		if ( ! $product ) {
			continue;
		}			$items[] = array(
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
add_action( 'wp_enqueue_scripts', 'ferm_enqueue_cart_bridge', 5 );
function ferm_enqueue_cart_bridge() {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return;
	}
	$pack_url = aether_pack_url();
	if ( ! $pack_url ) {
		return;
	}
	wp_register_script( 'ferm-data-shims', $pack_url . 'cdn/shop/t/164/assets/ferm-data-shims.js', array(), '1.0.0', true );
	wp_localize_script(
		'ferm-data-shims',
		'ferm_bridge',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'ferm_cart_nonce' ),
		)
	);

	// Inject FermPageData for complete-page dynamic data.
	$page_data = ferm_build_page_data();
	wp_localize_script( 'ferm-data-shims', 'FermPageData', $page_data );

	// Enqueue cart-page.ferm.js on cart pages.
	$cart_page_id = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'cart' ) : 0;
	$queried_id   = get_queried_object_id();
	$is_cart      = ( $cart_page_id && $queried_id === $cart_page_id )
		|| ( isset( $_SERVER['REQUEST_URI'] ) && 0 === strpos( strtolower( wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) ), '/cart' ) );
	if ( $is_cart ) {
		$cart_js_path = WP_CONTENT_DIR . '/frontend/designs/fermliving/cdn/shop/t/164/assets/cart-page.ferm.js';
		if ( file_exists( $cart_js_path ) ) {
			wp_enqueue_script( 'ferm-cart-page', $pack_url . 'cdn/shop/t/164/assets/cart-page.ferm.js', array( 'ferm-data-shims' ), '1.0.0', true );
		}
	}

	// Enqueue search bridge on all complete-page routes.
	$search_js_path = WP_CONTENT_DIR . '/frontend/designs/fermliving/cdn/shop/t/164/assets/search-bridge.js';
	if ( file_exists( $search_js_path ) ) {
		wp_enqueue_script( 'ferm-search-bridge', $pack_url . 'cdn/shop/t/164/assets/search-bridge.js', array( 'ferm-data-shims' ), '1.0.0', true );
	}

	// Enqueue customizer bridge on all complete-page routes.
	// Consumes FermPageData.customizer and updates frozen DOM with
	// WordPress Customizer values (announcement, footer, social, colors, fonts).
	$customizer_js_path = WP_CONTENT_DIR . '/frontend/designs/fermliving/cdn/shop/t/164/assets/customizer-bridge.js';
	if ( file_exists( $customizer_js_path ) ) {
		wp_enqueue_script( 'ferm-customizer-bridge', $pack_url . 'cdn/shop/t/164/assets/customizer-bridge.js', array( 'ferm-data-shims' ), '1.0.0', true );
	}
}

// --- Inject FermPageData as inline script for collection/archive pages ---
// The frozen Ferm collection HTML doesn't load the cart bridge script,
// so we inject FermPageData directly via wp_head.
add_action( 'wp_head', 'ferm_inject_collection_fermpagedata', 5 );
function ferm_inject_collection_fermpagedata() {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return;
	}
	// Only on collection/archive pages (not product pages — those use the bridge).
	if ( is_product() ) {
		return;
	}
	if ( ! ( is_tax( 'product_cat' ) || is_post_type_archive( 'product' ) || is_page( 'shop' ) ) ) {
		return;
	}
	$page_data = ferm_build_page_data();
	$json = wp_json_encode( $page_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	echo '<script>window.FermPageData = ' . $json . ';</script>' . "\n";
}

/**
 * Build the FermPageData object injected into complete-page templates.
 *
 * Provides real AUREON/WooCommerce data to the frozen Ferm frontend
 * without modifying the HTML. Ferm's JS reads these globals to populate
 * dynamic content (cart, customer, navigation, etc.).
 *
 * @return array Structured data for Ferm JS consumption.
 */
function ferm_build_page_data() {
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
	$currency = 'EUR';
	if ( class_exists( 'WooCommerce' ) ) {
		$currency = get_woocommerce_currency();
	}
	$money_format = 'EUR {{amount_with_comma_separator}}';
	if ( function_exists( 'wc_price' ) ) {
		$money_format = 'EUR {{amount_with_comma_separator}}';
	}

	// Navigation — map WP nav menus to Ferm format.
	$nav_main   = ferm_get_nav_menu( 'primary' );
	$nav_footer = ferm_get_nav_menu( 'footer' );

	// Page info.
	$template = 'index';
	if ( is_product() ) {
		$template = 'product';
	} elseif ( is_404() ) {
		// Check if this looks like a product URL pattern.
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
		if ( preg_match( '#/product/([^/]+)/?$#', $request_uri ) ) {
			$template = 'product';
		}
	} elseif ( is_post_type_archive( 'product' ) || is_page( 'shop' ) ) {
		$template = 'collection';
	} elseif ( is_tax( 'product_cat' ) ) {
		$template = 'collection';
	} elseif ( is_page() ) {
		$template = 'page';
	} elseif ( is_home() || is_post_type_archive( 'post' ) ) {
		$template = 'blog';
	}

	// Build return array.
	$page_data = array(
		'cart' => array(
			'items'      => $cart_items,
			'item_count' => $cart_count,
			'total_price' => $cart_total,
			'currency'   => $currency,
		),
		'customer' => $customer,
		'shop'     => array(
			'name'             => get_bloginfo( 'name' ),
			'url'              => home_url( '/' ),
			'currency'         => $currency,
			'money_format'     => $money_format,
			'money_format_decimals' => $money_format,
		),
		'navigation' => array(
			'main'   => $nav_main,
			'footer' => $nav_footer,
		),
		'config' => array(
			'ajax_url'    => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'ferm_cart_nonce' ),
			'wc_ajax_url' => function_exists( 'WC' ) ? add_query_arg( 'wc-ajax', '%%endpoint%%', home_url( '/' ) ) : '',
			'is_logged_in' => is_user_logged_in(),
			'template'    => $template,
			'money_format' => $money_format,
			'shop_url'    => home_url( '/' ),
			'search_url'  => home_url( '/?s=' ),
		),
	);

	// Inject product data on single product pages.
	if ( ! empty( $GLOBALS['ferm_product_page_data'] ) ) {
		$page_data['product'] = $GLOBALS['ferm_product_page_data'];
	}

	// Inject collection data on product archive/category pages.
	if ( ( is_tax( 'product_cat' ) || is_post_type_archive( 'product' ) || is_page( 'shop' ) ) && ! is_product() ) {
		$page_data['collection'] = ferm_build_collection_data();
	}

	// --- Customizer content bridge ---
	// Provides AETHER Customizer values to the Ferm frontend so that
	// client-configurable content (hero, announcement, footer, newsletter,
	// social, site identity) is available via FermPageData.customizer.
	// The frozen HTML provides defaults; Customizer values override when set.
	// Demo fallback: when Customizer values are empty, demo assets from
	// demo/demo-assets.json are used as intermediate fallback.
	$demo_manifest = ferm_load_demo_assets();
	$demo_assets   = isset( $demo_manifest['assets'] ) ? $demo_manifest['assets'] : array();

	// Logo: Custom → demo → site name text.
	$custom_logo = function_exists( 'has_custom_logo' ) && has_custom_logo()
		? wp_get_attachment_image_url( get_theme_mod( 'custom_logo', '' ), 'full' )
		: '';
	$demo_logo = '';
	if ( empty( $custom_logo ) && ! empty( $demo_assets['logo']['src'] ) ) {
		$demo_logo = $demo_assets['logo']['src'];
		if ( strpos( $demo_logo, 'http' ) === false ) {
			$demo_logo = aether_pack_url() . $demo_logo;
		}
	}

	// Hero: Custom → demo → frozen HTML default.
	$custom_hero = array_filter(
		aureon_get_option( 'aether_hero_slides', array() ),
		function( $slide ) {
			return ! empty( $slide['visible'] );
		}
	);
	$demo_hero = array();
	if ( empty( $custom_hero ) && ! empty( $demo_assets['hero'] ) ) {
		$hero = $demo_assets['hero'];
		$hero_image = isset( $hero['image'] ) ? $hero['image'] : '';
		if ( $hero_image && strpos( $hero_image, 'http' ) === false ) {
			$hero_image = aether_pack_url() . $hero_image;
		}
		$demo_hero[] = array(
			'visible'      => true,
			'title'        => isset( $hero['headline'] ) ? $hero['headline'] : '',
			'subtitle'     => isset( $hero['subline'] ) ? $hero['subline'] : '',
			'accent'       => isset( $hero['accent'] ) ? $hero['accent'] : '',
			'badge'        => isset( $hero['badge'] ) ? $hero['badge'] : '',
			'image'        => $hero_image,
			'primary_cta'  => isset( $hero['primary_cta'] ) ? $hero['primary_cta'] : array(),
			'secondary_cta'=> isset( $hero['secondary_cta'] ) ? $hero['secondary_cta'] : array(),
		);
	}

	// Heading: Custom → demo → frozen HTML default.
	$custom_heading = get_option( 'aether_site_heading', '' );
	$demo_heading   = '';
	if ( empty( $custom_heading ) && ! empty( $demo_assets['heading']['text'] ) ) {
		$demo_heading = $demo_assets['heading']['text'];
	}

	$page_data['customizer'] = array(
		'site' => array(
			'name'        => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'logo_url'    => $custom_logo ? $custom_logo : $demo_logo,
		),
		'announcement' => aureon_get_option( 'aether_announcement_items', array() ),
		'hero'         => ! empty( $custom_hero ) ? $custom_hero : $demo_hero,
		'categories'   => aureon_get_option( 'aether_category_items', array() ),
		'footer'       => aureon_get_option( 'aether_footer_columns', array() ),
		'newsletter'   => array(
			'heading'  => aureon_get_option( 'aether_newsletter_heading', '' ),
			'text'     => aureon_get_option( 'aether_newsletter_text', '' ),
			'subtitle' => aureon_get_option( 'aether_newsletter_subtitle', '' ),
		),
		'social'       => aureon_get_option( 'aether_social_items', array() ),
		'usp_items'    => aureon_get_option( 'aether_footer_usp_items', array() ),
		'heading'      => $custom_heading ? $custom_heading : $demo_heading,
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
 * Get navigation menu items in Ferm format.
 *
 * @param string $location Nav menu location.
 * @return array Menu items with title, url, and children.
 */
function ferm_get_nav_menu( $location ) {
	$menus = wp_get_nav_menus();
	$items = array();

	// Find menu assigned to location.
	$theme_locations = get_nav_menu_locations();
	if ( empty( $theme_locations[ $location ] ) ) {
		return $items;
	}

	$menu_items = wp_get_nav_menu_items( $theme_locations[ $location ] );
	if ( ! is_array( $menu_items ) ) {
		return $items;
	}

	// Build hierarchical structure.
	$all_items = array();
	foreach ( $menu_items as $item ) {
		$all_items[ $item->menu_item_parent ?: $item->ID ] = $item;
	}

	foreach ( $menu_items as $item ) {
		// Only top-level items.
		if ( $item->menu_item_parent ) {
			continue;
		}

		$entry = array(
			'title'    => $item->title,
			'url'      => $item->url,
			'children' => array(),
		);

		// Find children.
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
 * Build FermPageData.product from WC data for single product pages.
 *
 * Injects real WooCommerce product data into the Ferm JS context
 * so the frozen product DOM can display live data.
 * Handles both simple and variable products with Shopify-compatible schema.
 *
 * @param int $product_id WC product ID.
 * @return array Product data in Ferm-compatible schema.
 */
function ferm_build_product_page_data( $product_id ) {
	// Handle both product ID (int) and adapter data (array).
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

	// Gallery images from parent product.
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

	// If no gallery images, use placeholder from pack.
	if ( empty( $gallery ) ) {
		$pack_url = aether_pack_url();
		if ( $pack_url ) {
			$gallery[] = array(
				'src' => $pack_url . 'cdn/shop/files/meridian-lamp-black.png',
				'alt' => $product->get_name(),
			);
		}
	}

	// Base price in cents (Ferm Shopify format).
	$price_cents = 0;
	if ( $product->get_price() ) {
		$price_cents = (int) round( (float) $product->get_price() * 100 );
	}

	// Availability.
	$availability = 'out-of-stock';
	if ( $product->is_in_stock() ) {
		$availability = $product->managing_stock() && $product->get_stock_quantity() <= 5
			? 'low-stock'
			: 'in-stock';
	}

	// --- Variable product: build variants, options, price range ---
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
					'id'         => $v_image_id,
					'src'        => $v_image_url,
					'alt'        => $variation->get_name(),
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

	// Gallery URLs.
	$gallery_urls = array();
	foreach ( $gallery as $g ) {
		$gallery_urls[] = $g['src'];
	}

	// Prepend first variant image if available.
	if ( ! empty( $variants ) && null !== $selected_variant_id ) {
		foreach ( $variants as $v ) {
			if ( $v['id'] === $selected_variant_id && ! empty( $v['featured_image'] ) ) {
				$v_img = $v['featured_image']['src'];
				if ( ! empty( $v_img ) && ( empty( $gallery_urls ) || $v_img !== $gallery_urls[0] ) ) {
					array_unshift( $gallery_urls, $v_img );
				}
				break;
			}
		}
	}

	// --- Color swatches for variable products ---
	$colors     = array();
	$color_name = '';

	if ( 'variable' === $product_type ) {
		$attrs = $product->get_attributes();
		$color_attr = null;
		foreach ( $attrs as $attr ) {
			$attr_name = strtolower( $attr->get_name() );
			if ( in_array( $attr_name, array( 'color', 'pa_color' ), true ) ) {
				$color_attr = $attr;
				break;
			}
		}

		if ( $color_attr ) {
			$color_hex_map = array(
				'black'      => '#1a1a1a',
				'off-white'  => '#f5f0e8',
				'green'      => '#2d5a3d',
				'dark green' => '#2d5a3d',
				'white'      => '#ffffff',
				'grey'       => '#888888',
				'gray'       => '#888888',
				'blue'       => '#2b4c7e',
				'red'        => '#8b2500',
				'beige'      => '#d4c5a9',
				'natural'    => '#c4a882',
				'brass'      => '#b5a642',
			);

			foreach ( $color_attr->get_options() as $opt ) {
				$slug = sanitize_title( $opt );
				$colors[] = array(
					'name'   => $opt,
					'hex'    => isset( $color_hex_map[ $slug ] ) ? $color_hex_map[ $slug ] : '#cccccc',
					'handle' => $slug,
					'url'    => '',
				);
			}

			if ( null !== $selected_variant_id ) {
				foreach ( $variants as $v ) {
					if ( $v['id'] === $selected_variant_id && ! empty( $v['option1'] ) ) {
						$color_name = $v['option1'];
						break;
					}
				}
			}
		}
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
		'colors'              => $colors,
		'color_name'          => $color_name,
		'media'               => array_map( function ( $g ) {
			return array(
				'src'        => $g['src'],
				'alt'        => $g['alt'],
				'media_type' => 'image',
			);
		}, $gallery ),
	);
}

// Store product data for FermPageData injection on single product pages.
add_action( 'wp', 'ferm_store_product_page_data' );
function ferm_store_product_page_data() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	global $post;
	if ( ! $post ) {
		return;
	}
	$GLOBALS['ferm_product_page_data'] = ferm_build_product_page_data( $post->ID );
}

// Handle product URLs that don't exist in WooCommerce (404 pages).
// Falls back to demo product data when available.
// Hooked into 'wp' at priority 1 — BEFORE ferm_store_product_page_data (priority 10)
// and BEFORE wp_enqueue_scripts (priority 10) so FermPageData.product is set
// when FermPageData is built and localized.
add_action( 'wp', 'ferm_handle_missing_product', 1 );
function ferm_handle_missing_product() {
	if ( ! is_404() ) {
		return;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
	if ( ! preg_match( '#/product/([^/]+)/?$#', $request_uri, $m ) ) {
		return;
	}

	$slug = sanitize_title( $m[1] );
	if ( empty( $slug ) ) {
		return;
	}

	// Try to find in demo dataset.
	$demo_product = ferm_find_demo_product_by_slug( $slug );
	if ( ! $demo_product ) {
		return;
	}

	// Do NOT overwrite real product data if already set by ferm_store_product_page_data.
	if ( ! empty( $GLOBALS['ferm_product_page_data'] ) ) {
		return;
	}

	// Build FermPageData.product from demo data.
	$GLOBALS['ferm_product_page_data'] = ferm_build_demo_product_data( $demo_product );

	// Force template detection to 'product' for FermPageData injection.
	add_filter( 'aether_design_manifest', function( $manifest ) {
		return $manifest;
	} );
}

/**
 * Find a demo product by slug from the demo-products.json file.
 *
 * @param string $slug Product slug to search for.
 * @return array|null Demo product data or null if not found.
 */
function ferm_find_demo_product_by_slug( $slug ) {
	static $demo_products = null;

	if ( null === $demo_products ) {
		$demo_file = __DIR__ . '/demo/demo-products.json';
		if ( ! file_exists( $demo_file ) ) {
			return null;
		}
		$json = file_get_contents( $demo_file );
		$data = json_decode( $json, true );
		$demo_products = isset( $data['products'] ) ? $data['products'] : array();

		// Index by slug for fast lookup.
		$indexed = array();
		foreach ( $demo_products as $product ) {
			if ( ! empty( $product['slug'] ) ) {
				$indexed[ $product['slug'] ] = $product;
			}
		}
		$demo_products = $indexed;
	}

	return isset( $demo_products[ $slug ] ) ? $demo_products[ $slug ] : null;
}

/**
 * Build FermPageData.product from a demo product array.
 *
 * @param array $demo_product Demo product data from demo-products.json.
 * @return array Product data in Ferm-compatible schema.
 */
function ferm_build_demo_product_data( $demo_product ) {
	$gallery = array();
	if ( ! empty( $demo_product['image'] ) ) {
		$gallery[] = array(
			'src' => $demo_product['image'],
			'alt' => $demo_product['name'] ?? '',
		);
	}
	if ( ! empty( $demo_product['gallery'] ) ) {
		foreach ( $demo_product['gallery'] as $img ) {
			$gallery[] = array(
				'src' => $img,
				'alt' => $demo_product['name'] ?? '',
			);
		}
	}

	return array(
		'id'                  => 0,
		'title'               => $demo_product['name'] ?? '',
		'handle'              => $demo_product['slug'] ?? '',
		'slug'                => $demo_product['slug'] ?? '',
		'url'                 => home_url( '/product/' . ( $demo_product['slug'] ?? '' ) . '/' ),
		'sku'                 => '',
		'price'               => $demo_product['price_cents'] ?? 0,
		'price_html'          => $demo_product['price'] ?? '',
		'compare_at_price'    => null,
		'currency'            => $demo_product['currency'] ?? 'EUR',
		'availability'        => 'in-stock',
		'purchasable'         => false,
		'inventory_quantity'  => 999,
		'requires_shipping'  => true,
		'taxable'             => true,
		'product_type'        => 'simple',
		'gallery'             => $gallery,
		'media'               => array_map( function( $g ) {
			return array(
				'src'        => $g['src'],
				'alt'        => $g['alt'],
				'media_type' => 'image',
			);
		}, $gallery ),
		'variants'            => array(),
		'options'             => array(),
		'colors'              => array(),
		'option1'             => null,
		'option2'             => null,
		'option3'             => null,
		'selected_variant_id' => null,
		'featured_image'      => ! empty( $gallery ) ? $gallery[0] : null,
		'badge'               => $demo_product['badge'] ?? '',
		'description'         => $demo_product['short_description'] ?? '',
		'short_description'   => $demo_product['short_description'] ?? '',
		'categories'          => $demo_product['categories'] ?? array(),
		'collection'          => $demo_product['collection'] ?? '',
		'source'              => 'demo',
		'demo_id'             => $demo_product['demo_id'] ?? '',
		'shopify_id'          => $demo_product['shopify_id'] ?? null,
	);
}

// --- Collection/Archive Data ---
function ferm_build_collection_data() {
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

	// Query WC products in this category/archive.
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

	// Filter out demo products (aureon_demo=1) when real products exist.
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
			'id'       => $product->get_id(),
			'title'    => $product->get_name(),
			'handle'   => $product->get_slug(),
			'url'      => $product->get_permalink(),
			'sku'      => $product->get_sku(),
			'price'    => $price_cents,
			'price_html' => $product->get_price_html(),
			'image'    => $image_url,
			'gallery'  => $gallery_urls,
			'available' => $product->is_in_stock(),
			'badge'    => $product->is_on_sale() ? 'Sale' : '',
		);
	}

	// --- Demo product fallback ---
	// When no real WC products exist, load demo products from the
	// client-pack JSON and filter by the current category slug.
	if ( empty( $products ) ) {
		$products = ferm_get_demo_products_for_collection( $term ? $term->slug : '' );
	}

	// Category info.
	$term_image = '';
	if ( $term && ! empty( $term->term_id ) ) {
		$term_image_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
		if ( $term_image_id ) {
			$term_image = wp_get_attachment_url( $term_image_id );
		}
	}

	// --- Demo category image fallback ---
	// When no WC term image exists, resolve from the demo assets.
	if ( empty( $term_image ) && $term && ! empty( $term->slug ) ) {
		$term_image = ferm_resolve_demo_category_image( $term->slug );
	}

	return array(
		'title'       => $term_name,
		'description' => $term_description,
		'image'       => $term_image,
		'product_count' => count( $products ),
		'products'    => $products,
	);
}

/**
 * Get demo products for a collection/category page.
 *
 * Loads demo products from the client-pack JSON and filters by
 * category slug. Returns products in the format expected by the
 * frontend collection bridge.
 *
 * @param string $category_slug Category slug to filter by (empty = all).
 * @return array Filtered demo products.
 */
function ferm_get_demo_products_for_collection( $category_slug = '' ) {
	$pack_dir = aether_active_design_dir();
	$json_file = $pack_dir . 'demo/demo-products.json';
	if ( ! file_exists( $json_file ) ) {
		return array();
	}

	$raw = json_decode( (string) file_get_contents( $json_file ), true );
	if ( ! is_array( $raw ) ) {
		return array();
	}

	// Normalize: handle both wrapped {products: [...]} and flat array.
	$all_products = isset( $raw['products'] ) && is_array( $raw['products'] )
		? $raw['products']
		: $raw;

	$result = array();
	foreach ( $all_products as $product ) {
		if ( ! is_array( $product ) ) {
			continue;
		}

		// Filter by category if specified.
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
		$demo_id = isset( $product['demo_id'] ) ? $product['demo_id'] : '';
		$url = isset( $product['url'] ) ? $product['url'] : '#';

		// Build gallery from primary image + gallery array.
		$gallery = array();
		if ( $image ) {
			$gallery[] = $image;
		}
		if ( ! empty( $product['gallery'] ) && is_array( $product['gallery'] ) ) {
			foreach ( $product['gallery'] as $img ) {
				if ( ! empty( $img ) && ! in_array( $img, $gallery, true ) ) {
					$gallery[] = $img;
				}
			}
		}

		$result[] = array(
			'id'         => 0,
			'title'      => isset( $product['name'] ) ? $product['name'] : '',
			'handle'     => $demo_id,
			'url'        => $url,
			'sku'        => '',
			'price'      => isset( $product['price_cents'] ) ? (int) $product['price_cents'] : 0,
			'price_html' => isset( $product['price'] ) ? $product['price'] : '',
			'image'      => $image,
			'gallery'    => $gallery,
			'available'  => true,
			'badge'      => isset( $product['badge'] ) ? $product['badge'] : '',
			'source'     => 'demo',
			'demo_id'    => $demo_id,
		);
	}

	return $result;
}

/**
 * Resolve a demo category image URL from demo-assets.json.
 *
 * @param string $slug Category slug.
 * @return string Image URL or empty string.
 */
function ferm_resolve_demo_category_image( $slug ) {
	$manifest = ferm_load_demo_assets();
	if ( empty( $manifest['assets']['categories']['images'] ) ) {
		return '';
	}
	$images = $manifest['assets']['categories']['images'];
	if ( isset( $images[ $slug ] ) && $images[ $slug ] ) {
		return $images[ $slug ];
	}
	return '';
}

// --- Demo Asset Manifest ---
// Loads demo asset configuration from the pack's demo/ directory.
function ferm_load_demo_assets() {
	$pack_dir = aether_active_design_dir();
	$json_file = $pack_dir . 'demo/demo-assets.json';
	if ( ! file_exists( $json_file ) ) {
		return array();
	}
	$raw = json_decode( (string) file_get_contents( $json_file ), true );
	return is_array( $raw ) ? $raw : array();
}

// --- Demo Asset Resolver ---
// Resolves a demo asset URL with fallback hierarchy:
// 1. Custom client asset
// 2. Active client demo/default
// 3. Generic AUREON fallback
function ferm_resolve_demo_asset( $type, $key = '' ) {
	$manifest = ferm_load_demo_assets();
	$pack_url = aether_pack_url();
	
	if ( empty( $manifest['assets'][ $type ] ) ) {
		return '';
	}
	
	$asset = $manifest['assets'][ $type ];
	
	// Check for custom value first (from Customizer).
	$custom_key = 'aether_demo_' . $type;
	if ( $key ) {
		$custom_key .= '_' . $key;
	}
	$custom = aureon_get_option( $custom_key, '' );
	if ( $custom ) {
		return $custom;
	}
	
	// Use demo asset from manifest.
	if ( isset( $asset['src'] ) && $asset['src'] ) {
		$src = $asset['src'];
		if ( strpos( $src, 'http' ) === false && $pack_url ) {
			$src = $pack_url . $src;
		}
		return $src;
	}
	
	// Use fallback.
	if ( isset( $asset['fallback'] ) && is_string( $asset['fallback'] ) ) {
		$fallback = $asset['fallback'];
		if ( strpos( $fallback, 'http' ) === false && $pack_url ) {
			$fallback = $pack_url . $fallback;
		}
		return $fallback;
	}
	
	return '';
}

// --- Product Remapping ---
function ferm_remap_product( $product ) {
	if ( ! is_array( $product ) ) {
		return $product;
	}
	$badge = '';
	if ( ! empty( $product['badge'] ) && 'Sale' === $product['badge'] ) {
		$badge = 'Sale';
	} elseif ( ! empty( $product['tagline'] ) && strpos( $product['tagline'], 'Certified' ) !== false ) {
		$badge = 'Certified';
	} elseif ( ! empty( $product['badge'] ) && 'New' === $product['badge'] ) {
		$badge = 'New';
	}
	$product['badge'] = $badge;

	if ( empty( $product['swatches'] ) && ! empty( $product['colors'] ) ) {
		$product['swatches'] = ferm_format_swatches( $product['colors'], $product['url'] ?? '' );
	}
	return $product;
}

function ferm_format_swatches( $colors, $base_url = '' ) {
	$swatches = array();
	if ( ! is_array( $colors ) ) {
		return $swatches;
	}
	foreach ( $colors as $color ) {
		$hex  = isset( $color['hex'] ) ? $color['hex'] : '';
		$name = isset( $color['name'] ) ? $color['name'] : '';
		if ( empty( $hex ) ) {
			continue;
		}
		$label = $name;
		if ( ! empty( $color['secondary_name'] ) ) {
			$label .= ' / ' . $color['secondary_name'];
		}
		$swatches[] = array(
			'color' => $hex,
			'label' => $label,
			'url'   => $base_url,
		);
	}
	return $swatches;
}
