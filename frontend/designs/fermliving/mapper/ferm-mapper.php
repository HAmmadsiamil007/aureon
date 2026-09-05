<?php
/**
 * Ferm Living Design Mapper
 *
 * Transforms canonical AUREON data into Ferm Living presentation model.
 * This is the critical missing layer that was causing "Ferm-colored but AETHER-shaped" output.
 *
 * Architecture:
 *   Canonical Data → Ferm Mapper → Ferm Presentation Model → Ferm UI
 *
 * @package Aureon\Designs\FermLiving
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map canonical product data to Ferm presentation model.
 *
 * @param array $canonical Canonical product data from adapter.
 * @return array Ferm presentation model.
 */
function ferm_map_product_to_presentation( $canonical ) {
	if ( ! is_array( $canonical ) ) {
		return $canonical;
	}

	$presentation = array(
		'id'              => isset( $canonical['id'] ) ? $canonical['id'] : 0,
		'title'           => isset( $canonical['name'] ) ? $canonical['name'] : '',
		'price'           => isset( $canonical['price'] ) ? $canonical['price'] : '',
		'formatted_price' => ferm_format_price( isset( $canonical['price'] ) ? $canonical['price'] : '' ),
		'image'           => isset( $canonical['image'] ) ? $canonical['image'] : '',
		'images'          => isset( $canonical['gallery'] ) ? $canonical['gallery'] : array(),
		'url'             => isset( $canonical['url'] ) ? $canonical['url'] : '#',
		'in_stock'        => isset( $canonical['in_stock'] ) ? (bool) $canonical['in_stock'] : true,
		'inventory'       => isset( $canonical['inventory'] ) ? (int) $canonical['inventory'] : 0,

		/* Ferm-specific presentation fields */
		'badges'          => ferm_compute_badges( $canonical ),
		'colors'          => ferm_map_colors( $canonical ),
		'tagline'         => ferm_compute_tagline( $canonical ),
		'wishlist'        => true,
		'quick_add'       => true,
		'layout'          => array(
			'aspect_ratio_mobile'  => '1/1.53',
			'aspect_ratio_desktop' => '1/1.33',
		),
	);

	return $presentation;
}

/**
 * Map canonical category data to Ferm presentation model.
 *
 * @param array $canonical Canonical category data from adapter.
 * @return array Ferm presentation model.
 */
function ferm_map_category_to_presentation( $canonical ) {
	if ( ! is_array( $canonical ) ) {
		return $canonical;
	}

	$presentation = array(
		'id'       => isset( $canonical['id'] ) ? $canonical['id'] : 0,
		'name'     => isset( $canonical['name'] ) ? $canonical['name'] : '',
		'url'      => isset( $canonical['url'] ) ? $canonical['url'] : '#',
		'image'    => isset( $canonical['image'] ) ? $canonical['image'] : '',
		'count'    => isset( $canonical['count'] ) ? $canonical['count'] : 0,
		'modifier' => isset( $canonical['modifier'] ) ? $canonical['modifier'] : '',
	);

	return $presentation;
}

/**
 * Map canonical navigation data to Ferm presentation model.
 *
 * @param array $canonical Canonical navigation data from adapter.
 * @return array Ferm presentation model.
 */
function ferm_map_navigation_to_presentation( $canonical ) {
	if ( ! is_array( $canonical ) ) {
		return $canonical;
	}

	$presentation = array(
		'items'     => isset( $canonical['items'] ) ? $canonical['items'] : array(),
		'mega_menu' => ferm_build_mega_menu( $canonical ),
	);

	return $presentation;
}

/**
 * Map canonical footer data to Ferm presentation model.
 *
 * @param array $canonical Canonical footer data from adapter.
 * @return array Ferm presentation model.
 */
function ferm_map_footer_to_presentation( $canonical ) {
	return array(
		'usps'       => aether_get_option( 'aether_footer_usp_items', array() ),
		'newsletter' => array(
			'heading' => aether_get_option( 'aether_newsletter_heading', 'Ferm Living news' ),
			'text'    => aether_get_option( 'aether_newsletter_text', '' ),
		),
		'columns'    => aether_get_option( 'aether_footer_columns', array() ),
		'legal'      => array(),
		'company'    => aether_get_option( 'aether_footer_company', '' ),
		'payment'    => aether_get_option( 'aether_footer_payment_icon', '' ),
		'social'     => aether_get_option( 'aether_social_items', array() ),
	);
}

/**
 * Map canonical homepage data to Ferm presentation model.
 *
 * @param array $canonical Canonical homepage data from adapter.
 * @return array Ferm presentation model.
 */
function ferm_map_homepage_to_presentation( $canonical ) {
	return array(
		'hero'        => aether_get_option( 'aether_hero_slides', array() ),
		'categories'  => aether_get_option( 'aether_category_items', array() ),
		'products'    => aether_get_option( 'aether_product_items', array() ),
		'rooms'       => aether_get_option( 'aether_room_items', array() ),
		'editorial'   => aether_get_option( 'aether_editorial_items', array() ),
		'newsletter'  => array(
			'heading' => aether_get_option( 'aether_newsletter_heading', '' ),
			'text'    => aether_get_option( 'aether_newsletter_text', '' ),
		),
	);
}

/* ── Helper Functions ────────────────────────────────────────── */

/**
 * Format price in Ferm Living style (EUR with comma separator).
 *
 * @param string $price Raw price.
 * @return string Formatted price.
 */
function ferm_format_price( $price ) {
	if ( empty( $price ) ) {
		return '';
	}

	/* If already formatted (contains €), return as-is */
	if ( strpos( $price, '€' ) !== false ) {
		return $price;
	}

	/* Format as EUR */
	$clean = preg_replace( '/[^0-9.,]/', '', $price );
	$clean = str_replace( ',', '.', $clean );
	$amount = (float) $clean;

	if ( $amount === (int) $amount ) {
		return 'EUR ' . number_format( $amount, 0, ',', '.' );
	}

	return 'EUR ' . number_format( $amount, 2, ',', '.' );
}

/**
 * Compute Ferm badges from canonical product data.
 *
 * @param array $canonical Canonical product data.
 * @return array Array of badge strings.
 */
function ferm_compute_badges( $canonical ) {
	$badges = array();

	$sale      = ! empty( $canonical['sale'] ) || ! empty( $canonical['compare_at_price'] );
	$new       = ! empty( $canonical['new'] ) || ( ! empty( $canonical['date_created'] ) && strtotime( $canonical['date_created'] ) > strtotime( '-30 days' ) );
	$certified = ! empty( $canonical['certified'] ) || ( isset( $canonical['tagline'] ) && strpos( $canonical['tagline'], 'Certified' ) !== false );

	if ( $sale ) {
		$badges[] = 'Sale';
	}
	if ( $certified ) {
		$badges[] = 'Certified';
	}
	if ( $new ) {
		$badges[] = 'New';
	}

	return $badges;
}

/**
 * Map canonical color data to Ferm swatch format.
 *
 * @param array $canonical Canonical product data.
 * @return array Color swatches.
 */
function ferm_map_colors( $canonical ) {
	$colors = array();

	if ( isset( $canonical['colors'] ) && is_array( $canonical['colors'] ) ) {
		foreach ( $canonical['colors'] as $color ) {
			$colors[] = array(
				'name'           => isset( $color['name'] ) ? $color['name'] : '',
				'hex'            => isset( $color['hex'] ) ? $color['hex'] : '',
				'secondary_name' => isset( $color['secondary_name'] ) ? $color['secondary_name'] : '',
				'secondary_hex'  => isset( $color['secondary_hex'] ) ? $color['secondary_hex'] : '',
			);
		}
	}

	return $colors;
}

/**
 * Compute Ferm tagline from canonical product data.
 *
 * @param array $canonical Canonical product data.
 * @return string Tagline string.
 */
function ferm_compute_tagline( $canonical ) {
	if ( isset( $canonical['tagline'] ) && ! empty( $canonical['tagline'] ) ) {
		return $canonical['tagline'];
	}

	$parts = array();
	if ( isset( $canonical['material'] ) && ! empty( $canonical['material'] ) ) {
		$parts[] = $canonical['material'];
	}
	if ( isset( $canonical['color_name'] ) && ! empty( $canonical['color_name'] ) ) {
		$parts[] = $canonical['color_name'];
	}

	return implode( ' · ', $parts );
}

/**
 * Build mega menu structure from navigation data.
 *
 * @param array $navigation Navigation data.
 * @return array Mega menu structure.
 */
function ferm_build_mega_menu( $navigation ) {
	$mega_menu = array();

	if ( ! isset( $navigation['items'] ) || ! is_array( $navigation['items'] ) ) {
		return $mega_menu;
	}

	foreach ( $navigation['items'] as $item ) {
		if ( empty( $item['children'] ) ) {
			continue;
		}

		$mega_menu[] = array(
			'label'    => isset( $item['label'] ) ? $item['label'] : '',
			'url'      => isset( $item['url'] ) ? $item['url'] : '#',
			'children' => $item['children'],
		);
	}

	return $mega_menu;
}
