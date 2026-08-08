<?php
/**
 * Menu adapter — maps WordPress nav menus to component-safe item data.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get menu items for a location as a flat { label, url, children[] } tree.
 *
 * Falls back to a curated structural menu when the location is empty so the
 * shell always renders navigable markup.
 *
 * @param string $location Menu location.
 * @return array
 */
function aether_adapter_menu( $location = 'primary' ) {
	$locations = get_nav_menu_locations();

	if ( ! empty( $locations[ $location ] ) ) {
		$items = wp_get_nav_menu_items( (int) $locations[ $location ] );

		if ( is_array( $items ) && ! empty( $items ) ) {
			return aether_build_menu_tree( $items );
		}
	}

	return aether_fallback_menu( $location );
}

/**
 * Build a parent/child tree from flat nav menu items.
 *
 * @param array $items Flat menu items from wp_get_nav_menu_items().
 * @return array
 */
function aether_build_menu_tree( $items ) {
	$by_id  = array();
	$root   = array();
	$object = get_queried_object();

	foreach ( $items as $item ) {
		$by_id[ (int) $item->ID ] = array(
			'label'    => $item->title,
			'url'      => $item->url,
			'active'   => aether_menu_item_is_active( $item, $object ),
			'children' => array(),
		);
	}

	foreach ( $items as $item ) {
		$node = &$by_id[ (int) $item->ID ];

		if ( ! empty( $item->menu_item_parent ) && isset( $by_id[ (int) $item->menu_item_parent ] ) ) {
			$by_id[ (int) $item->menu_item_parent ]['children'][] = $node;
		} else {
			$root[] = $node;
		}
	}

	return $root;
}

/**
 * Determine if a menu item targets the current page.
 *
 * @param object $item   Menu item.
 * @param object $object Queried object.
 * @return bool
 */
function aether_menu_item_is_active( $item, $object ) {
	if ( $object && ! empty( $object->ID ) && (int) $object->ID === (int) $item->object_id ) {
		return true;
	}

	return false;
}

/**
 * Curated fallback menu mirroring the AETHER template structure.
 *
 * @param string $location Menu location.
 * @return array
 */
function aether_fallback_menu( $location ) {
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

	if ( 'footer' === $location ) {
		return array(
			array( 'label' => 'Shop', 'url' => $shop, 'active' => false, 'children' => array() ),
			array( 'label' => 'About', 'url' => home_url( '/about/' ), 'active' => false, 'children' => array() ),
			array( 'label' => 'Blog', 'url' => home_url( '/blog/' ), 'active' => false, 'children' => array() ),
			array( 'label' => 'Contact', 'url' => home_url( '/contact/' ), 'active' => false, 'children' => array() ),
		);
	}

	return array(
		array(
			'label'    => 'Home',
			'url'      => home_url( '/' ),
			'active'   => is_front_page(),
			'children' => array(),
		),
		array(
			'label'    => 'Collection',
			'url'      => $shop,
			'active'   => false,
			'children' => array(
				array( 'label' => 'Men', 'url' => $shop, 'active' => false, 'children' => array() ),
				array( 'label' => 'Women', 'url' => $shop, 'active' => false, 'children' => array() ),
				array( 'label' => 'Kid', 'url' => $shop, 'active' => false, 'children' => array() ),
				array( 'label' => 'New Arrivals', 'url' => $shop, 'active' => false, 'children' => array() ),
				array( 'label' => 'Bestsellers', 'url' => $shop, 'active' => false, 'children' => array() ),
			),
		),
		array( 'label' => 'About', 'url' => home_url( '/about/' ), 'active' => is_page( 'about' ), 'children' => array() ),
		array( 'label' => 'Blog', 'url' => home_url( '/blog/' ), 'active' => false, 'children' => array() ),
		array( 'label' => 'Contact', 'url' => home_url( '/contact/' ), 'active' => is_page( 'contact' ), 'children' => array() ),
	);
}

/**
 * Social media links (footer + mobile menu).
 *
 * @return array
 */
function aether_adapter_socials() {
	return array(
		array( 'icon' => 'fab fa-instagram', 'label' => 'Instagram', 'url' => 'https://instagram.com/aethershoes' ),
		array( 'icon' => 'fab fa-twitter', 'label' => 'Twitter', 'url' => 'https://twitter.com/aethershoes' ),
		array( 'icon' => 'fab fa-tiktok', 'label' => 'TikTok', 'url' => 'https://tiktok.com/@aethershoes' ),
		array( 'icon' => 'fab fa-youtube', 'label' => 'YouTube', 'url' => 'https://youtube.com/@aethershoes' ),
	);
}
