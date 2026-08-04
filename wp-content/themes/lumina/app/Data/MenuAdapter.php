<?php
/**
 * MenuAdapter — normalizes a navigation menu into a ViewModel.
 *
 * Phase 4 (Render Engine): the canonical nav DTO. WP-loaded contexts resolve
 * menu items via wp_get_nav_menu_items() when a menu id/location is given;
 * WP-free contexts accept an array of item arrays (CLI smoke fixtures) or an
 * empty menu. Items are normalized to a flat, tree-safe shape (id, title,
 * url, target, parent, children).
 *
 * @package Lumina\Core\Data
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Data;

use Lumina\Core\Render\ViewModel;

/**
 * Navigation menu data adapter.
 */
class MenuAdapter implements DataAdapterInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed $source Source value.
	 */
	public function supports( mixed $source ): bool {
		return is_array( $source ) || is_int( $source ) || is_string( $source );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed                $source  Source value.
	 * @param array<string, mixed> $options Adapter options.
	 */
	public function adapt( mixed $source, array $options = array() ): ViewModel {
		$items = array();

		if ( is_array( $source ) ) {
			$items = $this->from_array( $source );
		} elseif ( $this->wp_available() ) {
			$items = $this->from_wp( (int) $source );
		}

		return new ViewModel(
			array(
				'id'    => is_int( $source ) ? $source : 0,
				'items' => $items,
			)
		);
	}

	/**
	 * Normalize an array-shaped menu.
	 *
	 * @param array<mixed> $source Raw menu items.
	 * @return list<array<string, mixed>>
	 */
	private function from_array( array $source ): array {
		$items = array();

		foreach ( $source as $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}

			$items[] = array(
				'id'       => (int) ( $entry['id'] ?? 0 ),
				'title'    => (string) ( $entry['title'] ?? $entry['name'] ?? '' ),
				'url'      => (string) ( $entry['url'] ?? $entry['link'] ?? '' ),
				'target'   => (string) ( $entry['target'] ?? '' ),
				'parent'   => (int) ( $entry['parent'] ?? 0 ),
				'children' => array(),
			);
		}

		return $items;
	}

	/**
	 * Resolve menu items from WordPress ('' or [] in WP-free contexts).
	 *
	 * @param int $menu_id Menu term id.
	 * @return list<array<string, mixed>>
	 */
	private function from_wp( int $menu_id ): array {
		if ( $menu_id <= 0 ) {
			return array();
		}

		$wp_items = wp_get_nav_menu_items( $menu_id );

		if ( ! is_array( $wp_items ) ) {
			return array();
		}

		$items = array();

		foreach ( $wp_items as $wp_item ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core class.
			if ( ! class_exists( 'WP_Post' ) || ! $wp_item instanceof \WP_Post ) {
				continue;
			}

			$items[] = array(
				'id'       => (int) $wp_item->ID,
				'title'    => (string) $wp_item->post_title,
				'url'      => (string) ( $wp_item->url ?? '' ),
				'target'   => (string) ( $wp_item->target ?? '' ),
				'parent'   => (int) ( $wp_item->menu_item_parent ?? 0 ),
				'children' => array(),
			);
		}

		return $items;
	}

	/**
	 * Whether the WP nav-menu API is loadable.
	 *
	 * @return bool
	 */
	private function wp_available(): bool {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		return function_exists( 'wp_get_nav_menu_items' );
	}
}
