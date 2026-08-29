<?php
/**
 * Ferm Living Design Pack — Composer
 *
 * Controls homepage section sequence and adapter data overrides.
 * Hooks into aether_frontpage_sections and adapter data filters.
 *
 * Architecture:
 *   WP/WooCommerce → Adapter (canonical) → Composer (Ferm transforms) → Section → Component
 *   Reference JSON  → Mapper (to canonical) → Composer (Ferm transforms) → Section → Component
 *
 * @package Aureon\Designs\FermLiving
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── Guard: only load when Ferm Living is active ────────────── */
if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
	return;
}

/* ── Load mapper layer ─────────────────────────────────────── */
$ferm_mapper = __DIR__ . '/mapper/ferm-mapper.php';
if ( file_exists( $ferm_mapper ) ) {
	require_once $ferm_mapper;
}

/* ── Homepage section sequence ───────────────────────────────── */
add_filter( 'aether_frontpage_sections', 'ferm_homepage_sections' );
function ferm_homepage_sections() {
	return array(
		'hero',
		'categories',
		'editorial-split',
		'bestsellers',
		'room-grid',
		'newsletter',
	);
}

/* ── Site data: Ferm-specific overrides ──────────────────────── */
add_filter( 'aether_adapter_site_data', 'ferm_site_data' );
function ferm_site_data( $data ) {
	if ( is_array( $data ) ) {
		$data['name'] = 'Ferm Living';
	}
	return $data;
}

/* ── Header: Ferm header data for window.FermPageData ─────────── */
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

/* ── Hero: use Ferm hero slides ──────────────────────────────── */
add_filter( 'aether_adapter_hero_data', 'ferm_hero_data' );
function ferm_hero_data( $data ) {
	$slides = aureon_get_option( 'aether_hero_slides', array() );
	if ( is_string( $slides ) && '' !== trim( $slides ) ) {
		$slides = json_decode( $slides, true );
	}
	if ( ! empty( $slides ) && is_array( $slides ) ) {
		$data['slides'] = $slides;
	}
	return $data;
}

/* ── Announcement: Ferm announcement items ────────────────────── */
add_filter( 'aether_adapter_announcement_data', 'ferm_announcement_data' );
function ferm_announcement_data( $data ) {
	return array(
		'items' => aureon_get_option( 'ferm_announcement_items', array() ),
	);
}

/* ── Footer: Ferm footer content ─────────────────────────────── */
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
			array( 'label' => 'Privacy Policy',       'url' => '#' ),
			array( 'label' => 'Cookies',              'url' => '#' ),
			array( 'label' => 'Follow Us',            'url' => '#' ),
		),
		'payments'   => aureon_get_option( 'aether_footer_payments', array() ),
		'socials'    => aureon_get_option( 'aether_social_items', array() ),
	);
}

/* ── WC Products: Ferm product overrides ─────────────────────── */
add_filter( 'aether_adapter_wc_products_data', 'ferm_wc_products_data' );
function ferm_wc_products_data( $data ) {
	if ( is_array( $data ) && isset( $data['items'] ) && is_array( $data['items'] ) ) {
		foreach ( $data['items'] as &$product ) {
			$product = ferm_remap_product( $product );
		}
	}
	return $data;
}

/* ── Demo Products: Ferm reference products ──────────────────── */
add_filter( 'aether_demo_products', 'ferm_demo_products', 10, 2 );
function ferm_demo_products( $items, $query_args ) {
	$pack_dir = aether_active_design_dir();
	$json_file = $pack_dir . 'data/products.json';
	if ( ! file_exists( $json_file ) ) {
		return $items;
	}

	$raw = json_decode( (string) file_get_contents( $json_file ), true );
	if ( ! is_array( $raw ) ) {
		return $items;
	}

	$pack_url = aether_pack_url();
	$result = array();

	foreach ( $raw as $product ) {
		$image = isset( $product['image'] ) ? $product['image'] : '';
		if ( $image && strpos( $image, 'http' ) === false ) {
			$image = $pack_url . $image;
		}

		$result[] = array(
			'id'              => isset( $product['id'] ) ? (int) $product['id'] : 0,
			'name'            => isset( $product['name'] ) ? $product['name'] : '',
			'price'           => isset( $product['price'] ) ? $product['price'] : '',
			'price_plain'     => isset( $product['price'] ) ? $product['price'] : '',
			'old_price_plain' => '',
			'tagline'         => isset( $product['tagline'] ) ? $product['tagline'] : '',
			'rating'          => 0,
			'reviews'         => 0,
			'image'           => $image,
			'alt'             => isset( $product['name'] ) ? $product['name'] : '',
			'url'             => isset( $product['url'] ) ? esc_url_raw( $product['url'] ) : '',
			'badge'           => isset( $product['badge'] ) ? $product['badge'] : '',
			'add_to_cart_url' => '',
			'product_type'    => 'simple',
			'behavior'        => array( 'tilt' => true ),
			'badges'          => isset( $product['badge'] ) && $product['badge'] ? array( $product['badge'] ) : array(),
			'swatches'        => isset( $product['colors'] ) ? ferm_format_swatches( $product['colors'], $product['url'] ?? '' ) : array(),
		);
	}

	/* Respect pagination from the adapter */
	$per_page = isset( $query_args['posts_per_page'] ) ? (int) $query_args['posts_per_page'] : 8;
	$paged    = isset( $query_args['paged'] ) ? (int) $query_args['paged'] : 1;
	$result   = array_slice( $result, ( $paged - 1 ) * $per_page, $per_page );

	return $result;
}

/* ── Single Product: Ferm product mapping ────────────────────── */
add_filter( 'aether_adapter_product_data', 'ferm_product_data' );
function ferm_product_data( $data ) {
	return ferm_remap_product( $data );
}

/* ── WC Categories: Ferm category overrides ──────────────────── */
add_filter( 'aether_adapter_wc_categories_data', 'ferm_wc_categories_data' );
function ferm_wc_categories_data( $data ) {
	/* Categories pass through unchanged — no remapping needed */
	return $data;
}

/* ── Demo Categories: Ferm reference categories ──────────────── */
add_filter( 'aether_demo_categories', 'ferm_demo_categories', 10, 2 );
function ferm_demo_categories( $items, $args ) {
	$pack_dir = aether_active_design_dir();
	$json_file = $pack_dir . 'data/categories.json';
	if ( ! file_exists( $json_file ) ) {
		return $items;
	}

	$raw = json_decode( (string) file_get_contents( $json_file ), true );
	if ( ! is_array( $raw ) ) {
		return $items;
	}

	$pack_url = aether_pack_url();
	$result = array();

	foreach ( $raw as $cat ) {
		$image = isset( $cat['image'] ) ? $cat['image'] : '';
		if ( $image && strpos( $image, 'http' ) === false ) {
			$image = $pack_url . $image;
		}

		$count_label = isset( $cat['count'] ) ? $cat['count'] : '';
		if ( is_numeric( $count_label ) ) {
			$count_label = sprintf( _n( '%d Product', '%d Products', (int) $count_label, 'aureon' ), (int) $count_label );
		}

		$result[] = array(
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

/* ── WC Filter: Ferm category filters ────────────────────────── */
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

/* ── Blog: Ferm blog overrides ───────────────────────────────── */
add_filter( 'aether_adapter_blog_data', 'ferm_blog_data' );
function ferm_blog_data( $data ) {
	if ( is_array( $data ) ) {
		$data['label'] = 'Stories';
		$data['title'] = 'From the Ferm Living Journal';
	}
	return $data;
}

/* ── About: Ferm about content ───────────────────────────────── */
add_filter( 'aether_adapter_about_data', 'ferm_about_data' );
function ferm_about_data( $data ) {
	return array(
		'heading'   => aureon_get_option( 'aether_about_heading', 'About Ferm Living' ),
		'body'      => aureon_get_option( 'aether_about_body', '' ),
		'features'  => aureon_get_option( 'aether_about_features', array() ),
		'values'    => aureon_get_option( 'aether_about_values', array() ),
		'stats'     => aureon_get_option( 'aether_about_stats', array() ),
	);
}

/* ── Contact: Ferm contact info ──────────────────────────────── */
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

/* ── Search: Ferm search overrides ───────────────────────────── */
add_filter( 'aether_adapter_search_data', 'ferm_search_data' );
function ferm_search_data( $data ) {
	return array(
		'placeholder' => aureon_get_option( 'aether_search_placeholder', 'Search Ferm Living...' ),
		'suggestions'  => array( 'Furniture', 'Lighting', 'Accessories', 'Kids', 'Kitchen' ),
	);
}

/* ── Shop Hero: Minimal for Ferm ─────────────────────────────── */
add_filter( 'aether_adapter_shop_hero_data', 'ferm_shop_hero_data' );
function ferm_shop_hero_data( $data ) {
	return array(
		'label'    => '',
		'title'    => 'Shop',
		'subtitle' => '',
	);
}

/* ── Newsletter: Ferm newsletter overrides ───────────────────── */
add_filter( 'aether_adapter_newsletter_data', 'ferm_newsletter_data' );
function ferm_newsletter_data( $data ) {
	return array(
		'heading' => aureon_get_option( 'aether_newsletter_heading', 'Ferm Living news' ),
		'text'    => aureon_get_option( 'aether_newsletter_text', 'Get exclusive drops, early access, and Ferm Living news.' ),
	);
}

/* ════════════════════════════════════════════════════════════════
   Product Remapping — Ferm badge logic
   ════════════════════════════════════════════════════════════════ */

/**
 * Remap a single product to Ferm badge logic.
 *
 * Ferm badges: Sale (highest priority) > Certified > New.
 * The adapter only provides one badge string; we recompute from raw signals.
 *
 * @param array $product Product data from adapter.
 * @return array Remapped product.
 */
function ferm_remap_product( $product ) {
	if ( ! is_array( $product ) ) {
		return $product;
	}

	/* Recompute badge from Ferm priority logic */
	$badge = '';
	if ( ! empty( $product['badge'] ) && 'Sale' === $product['badge'] ) {
		$badge = 'Sale';
	} elseif ( ! empty( $product['tagline'] ) && strpos( $product['tagline'], 'Certified' ) !== false ) {
		$badge = 'Certified';
	} elseif ( ! empty( $product['badge'] ) && 'New' === $product['badge'] ) {
		$badge = 'New';
	}

	$product['badge'] = $badge;

	/* Add swatches from color data if present */
	if ( empty( $product['swatches'] ) && ! empty( $product['colors'] ) ) {
		$product['swatches'] = ferm_format_swatches( $product['colors'], $product['url'] ?? '' );
	}

	return $product;
}

/* ════════════════════════════════════════════════════════════════
   Swatch Formatter
   ════════════════════════════════════════════════════════════════ */

/**
 * Convert color data to swatch format expected by card/product component.
 *
 * @param array  $colors Color data from reference JSON.
 * @param string $base_url Base product URL.
 * @return array Swatch array.
 */
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

/* ════════════════════════════════════════════════════════════════
   WooCommerce AJAX Cart Handlers — Bridge Endpoints
   ════════════════════════════════════════════════════════════════ */

/* Register AJAX handlers for the Ferm cart bridge JS */
add_action( 'wp_ajax_ferm_cart_add', 'ferm_wc_ajax_cart_add' );
add_action( 'wp_ajax_nopriv_ferm_cart_add', 'ferm_wc_ajax_cart_add' );
add_action( 'wp_ajax_ferm_cart_update', 'ferm_wc_ajax_cart_update' );
add_action( 'wp_ajax_nopriv_ferm_cart_update', 'ferm_wc_ajax_cart_update' );
add_action( 'wp_ajax_ferm_cart_get', 'ferm_wc_ajax_cart_get' );
add_action( 'wp_ajax_nopriv_ferm_cart_get', 'ferm_wc_ajax_cart_get' );

/**
 * AJAX: Add item to WooCommerce cart.
 * Returns Shopify-format response so frozen JS stays unchanged.
 */
function ferm_wc_ajax_cart_add() {
	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( 'WooCommerce not available' );
	}

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$quantity   = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;

	if ( ! $product_id ) {
		wp_send_json_error( 'Invalid product' );
	}

	$added = WC()->cart->add_to_cart( $product_id, $quantity );

	if ( $added ) {
		$response = ferm_build_cart_response();
		wp_send_json_success( $response );
	} else {
		wp_send_json_error( 'Could not add to cart' );
	}
}

/**
 * AJAX: Update WooCommerce cart quantities.
 */
function ferm_wc_ajax_cart_update() {
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

/**
 * AJAX: Get current WooCommerce cart state.
 */
function ferm_wc_ajax_cart_get() {
	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( 'WooCommerce not available' );
	}

	$response = ferm_build_cart_response();
	wp_send_json_success( $response );
}

/**
 * Build Shopify-format cart response from WC cart.
 *
 * @return array Cart state in Shopify /cart.js format.
 */
function ferm_build_cart_response() {
	$cart   = WC()->cart;
	$items  = array();

	foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
		$product = $cart_item['data'];
		if ( ! $product ) {
			continue;
		}

		$items[] = array(
			'id'         => $cart_item['product_id'],
			'variant_id' => isset( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'],
			'quantity'   => $cart_item['quantity'],
			'title'      => $product->get_name(),
			'price'      => (int) round( (float) $product->get_price() * 100 ),
			'line_price' => (int) round( (float) $cart_item['line_total'] * 100 ),
			'variant_title' => '',
			'product_id' => $cart_item['product_id'],
			'url'        => get_permalink( $cart_item['product_id'] ),
			'image'      => wp_get_attachment_url( $product->get_image_id() ),
		);
	}

	return array(
		'item_count'  => $cart->get_cart_contents_count(),
		'items'       => $items,
		'total_price' => (int) round( (float) $cart->get_cart_contents_total() * 100 ),
		'sections'    => array(),
	);
}

/**
 * Enqueue the cart bridge JS with WP AJAX URL.
 */
add_action( 'wp_enqueue_scripts', 'ferm_enqueue_cart_bridge', 5 );
function ferm_enqueue_cart_bridge() {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return;
	}

	$pack_url = aether_pack_url();
	if ( ! $pack_url ) {
		return;
	}

	wp_register_script(
		'ferm-cart-bridge',
		$pack_url . 'js/ferm-cart-bridge.js',
		array(),
		'1.0.0',
		true
	);

	wp_localize_script( 'ferm-cart-bridge', 'ferm_bridge', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'ferm_cart_nonce' ),
	) );
}

/* Enqueue product JS only on single product pages */
add_action( 'wp_enqueue_scripts', 'ferm_enqueue_product_js', 15 );
function ferm_enqueue_product_js() {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return;
	}

	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	$pack_url = aether_pack_url();
	if ( ! $pack_url ) {
		return;
	}

	wp_enqueue_script(
		'ferm-product',
		$pack_url . 'js/ferm-product.js',
		array( 'ferm-cart-bridge' ),
		'1.0.0',
		true
	);
}

/* ════════════════════════════════════════════════════════════════
   FermPageData Builder — Thin Bridge for Product Pages
   ════════════════════════════════════════════════════════════════ */

/**
 * Build window.FermPageData from adapter output.
 *
 * This is the ONLY layer that converts canonical AUREON/WooCommerce data
 * into the Ferm presentation schema. JS reads this — never WooCommerce internals.
 *
 * @param array $adapter Data from aether_adapter_product().
 * @return array FermPageData for the product page.
 */
function ferm_build_product_page_data( $adapter ) {
	if ( ! is_array( $adapter ) ) {
		return array();
	}

	$product_id = isset( $adapter['id'] ) ? (int) $adapter['id'] : 0;
	$product    = $product_id ? wc_get_product( $product_id ) : false;

	/* ── Price in cents (for JS) ──────────────────────────────── */
	$price_cents = 0;
	$price_raw   = '';
	if ( $product ) {
		$price_cents = (int) round( (float) $product->get_price() * 100 );
		$price_raw   = $product->get_price();
	}

	/* ── Currency ─────────────────────────────────────────────── */
	$currency = function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'EUR';

	/* ── Variant ID (simple product = product ID) ─────────────── */
	$variant_id = $product_id;
	if ( $product && is_a( $product, 'WC_Product_Variable' ) ) {
		$children   = $product->get_children();
		$variant_id = ! empty( $children ) ? (int) $children[0] : $product_id;
	}

	/* ── SKU ──────────────────────────────────────────────────── */
	$sku = '';
	if ( $product ) {
		$sku = $product->get_sku();
	}

	/* ── Gallery ──────────────────────────────────────────────── */
	$gallery = isset( $adapter['gallery'] ) ? $adapter['gallery'] : array();

	/* ── Colors ───────────────────────────────────────────────── */
	$colors    = isset( $adapter['colors'] ) ? $adapter['colors'] : array();
	$color_name = '';
	if ( ! empty( $colors ) ) {
		$color_name = isset( $colors[0]['name'] ) ? $colors[0]['name'] : '';
	}

	/* ── Description ──────────────────────────────────────────── */
	$description = isset( $adapter['description'] ) ? $adapter['description'] : '';

	/* ── Specs → Ferm fields ──────────────────────────────────── */
	$specs           = isset( $adapter['specs'] ) ? $adapter['specs'] : array();
	$dimensions      = '';
	$weight          = '';
	$seat_height     = '';
	$backrest_height = '';
	$material        = '';
	$care            = '';

	foreach ( $specs as $spec ) {
		$title = isset( $spec['title'] ) ? strtolower( $spec['title'] ) : '';
		$body  = isset( $spec['body'] ) ? $spec['body'] : '';

		if ( false !== strpos( $title, 'size' ) || false !== strpos( $title, 'dimension' ) ) {
			$dimensions = $body;
		} elseif ( false !== strpos( $title, 'weight' ) ) {
			$weight = $body;
		} elseif ( false !== strpos( $title, 'seat' ) ) {
			$seat_height = $body;
		} elseif ( false !== strpos( $title, 'backrest' ) ) {
			$backrest_height = $body;
		} elseif ( false !== strpos( $title, 'material' ) ) {
			$material = $body;
		} elseif ( false !== strpos( $title, 'care' ) ) {
			$care = $body;
		}
	}

	/* ── MTO detection ────────────────────────────────────────── */
	$is_mto = false;
	if ( $product ) {
		$tags = $product->get_tag_ids();
		if ( ! empty( $tags ) ) {
			foreach ( $tags as $tag_id ) {
				$tag_name = get_term( $tag_id, 'product_tag' );
				if ( $tag_name && is_object( $tag_name ) && 'mto' === strtolower( $tag_name->name ) ) {
					$is_mto = true;
					break;
				}
			}
		}
	}

	/* ── USPs ─────────────────────────────────────────────────── */
	$usps = array(
		'Handmade in Europe',
		'Delivered within 8 weeks',
		'Personal styling sessions',
		'3-year warranty for members',
	);

	/* ── Delivery times ───────────────────────────────────────── */
	$delivery_time    = 56;
	$delivery_time_us = 119;

	/* ── Sample URL ───────────────────────────────────────────── */
	$sample_url = '';
	if ( $product && $product->has_child() ) {
		/* For variable products, try to find a sample product */
		$sample_url = '';
	}

	/* ── Dimension drawing ────────────────────────────────────── */
	$dimension_drawing = '';
	if ( $product ) {
		$dimension_drawing = get_post_meta( $product_id, '_dimension_drawing', true );
	}

	/* ── Build the schema ─────────────────────────────────────── */
	return array(
		/* Identity */
		'product_id'  => $product_id,
		'variant_id'  => $variant_id,
		'sku'         => $sku,
		'title'       => isset( $adapter['title'] ) ? $adapter['title'] : '',
		'url'         => get_permalink( $product_id ),

		/* Pricing */
		'price_cents' => $price_cents,
		'price_html'  => isset( $adapter['price'] ) ? $adapter['price'] : '',
		'currency'    => $currency,

		/* Gallery */
		'gallery'     => $gallery,

		/* Variants */
		'colors'      => $colors,
		'color_name'  => $color_name,
		'sizes'       => isset( $adapter['sizes'] ) ? $adapter['sizes'] : array(),

		/* Content */
		'description' => $description,
		'badge'       => isset( $adapter['badge'] ) ? $adapter['badge'] : '',
		'tagline'     => '',

		/* Specs */
		'dimensions'       => $dimensions,
		'weight'           => $weight,
		'seat_height'      => $seat_height,
		'backrest_height'  => $backrest_height,
		'material'         => $material,
		'care'             => $care,
		'dimension_drawing' => $dimension_drawing,

		/* Commerce */
		'is_mto'           => $is_mto,
		'sample_url'       => $sample_url,
		'delivery_time'    => $delivery_time,
		'delivery_time_us' => $delivery_time_us,
		'usps'             => $usps,

		/* Trust */
		'trust'            => isset( $adapter['trust'] ) ? $adapter['trust'] : array(),
	);
}
