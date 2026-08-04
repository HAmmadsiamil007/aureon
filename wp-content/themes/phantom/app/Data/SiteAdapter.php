<?php
/**
 * SiteAdapter — normalizes site-wide metadata into a ViewModel.
 *
 * Phase 4 (Render Engine): the canonical site DTO consumed by the header and
 * footer (site name, tagline, URL, logo, language, WP version, charset).
 * WP-loaded contexts read get_bloginfo(); WP-free contexts accept an
 * overrides array (CLI smoke fixtures).
 *
 * @package Phantom\Core\Data
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Data;

use Phantom\Core\Render\ViewModel;

/**
 * Site metadata data adapter.
 */
class SiteAdapter implements DataAdapterInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed $source Source value.
	 */
	public function supports( mixed $source ): bool {
		return is_array( $source ) || null === $source || is_string( $source );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed                $source  Source value.
	 * @param array<string, mixed> $options Adapter options.
	 */
	public function adapt( mixed $source, array $options = array() ): ViewModel {
		$overrides = is_array( $source ) ? $source : array();
		$wp        = function_exists( 'get_bloginfo' );

		return new ViewModel(
			array(
				'name'        => (string) ( $overrides['name'] ?? ( $wp ? get_bloginfo( 'name' ) : 'Phantom' ) ),
				'description' => (string) ( $overrides['description'] ?? ( $wp ? get_bloginfo( 'description' ) : '' ) ),
				'url'         => (string) ( $overrides['url'] ?? ( $wp ? get_bloginfo( 'url' ) : '' ) ),
				'language'    => (string) ( $overrides['language'] ?? ( $wp ? get_bloginfo( 'language' ) : 'en-US' ) ),
				'charset'     => (string) ( $overrides['charset'] ?? ( $wp ? get_bloginfo( 'charset' ) : 'UTF-8' ) ),
				'version'     => (string) ( $overrides['version'] ?? ( $wp ? get_bloginfo( 'version' ) : '' ) ),
			)
		);
	}
}
