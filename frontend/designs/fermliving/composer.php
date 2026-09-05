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

/* ── Footer: Ferm footer content ─────────────────────────────── */
add_filter( 'aether_adapter_footer_data', 'ferm_footer_data' );
function ferm_footer_data( $data ) {
	return array(
		'usps'       => aureon_get_option( 'aether_footer_usp_items', array() ),
		'newsletter' => array(
			'heading' => aureon_get_option( 'aether_newsletter_heading', 'Ferm Living news' ),
			'text'    => aureon_get_option( 'aether_newsletter_text', '' ),
		),
		'columns'    => aureon_get_option( 'aether_footer_columns', array() ),
		'legal'      => array(
			array( 'label' => 'Terms & Conditions', 'url' => '#' ),
			array( 'label' => 'Privacy Policy',     'url' => '#' ),
			array( 'label' => 'Cookie Policy',      'url' => '#' ),
		),
		'company'    => aureon_get_option( 'aether_footer_company', '' ),
		'payment'    => aureon_get_option( 'aether_footer_payment_icon', '' ),
		'social'     => aureon_get_option( 'aether_social_items', array() ),
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
