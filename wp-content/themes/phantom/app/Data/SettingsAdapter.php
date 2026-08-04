<?php
/**
 * SettingsAdapter — normalizes site options into a ViewModel.
 *
 * Phase 4 (Render Engine): reads options through an explicit allow-list so
 * component data never depends on raw option keys leaking into templates.
 * WP-loaded contexts read get_option(); WP-free contexts accept an array of
 * key/value pairs (CLI smoke fixtures). Unknown keys resolve to the default.
 *
 * @package Phantom\Core\Data
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Data;

use Phantom\Core\Render\ViewModel;

/**
 * Site options data adapter.
 */
class SettingsAdapter implements DataAdapterInterface {

	/**
	 * Allowed option keys, mapped to normalized DTO keys.
	 *
	 * @var array<string, string>
	 */
	private const ALLOWED = array(
		'blogname'        => 'site_name',
		'blogdescription' => 'site_description',
		'date_format'     => 'date_format',
		'time_format'     => 'time_format',
		'start_of_week'   => 'start_of_week',
	);

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed $source Source value.
	 */
	public function supports( mixed $source ): bool {
		return is_array( $source ) || null === $source;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed                $source  Source value.
	 * @param array<string, mixed> $options Adapter options.
	 */
	public function adapt( mixed $source, array $options = array() ): ViewModel {
		$overrides = is_array( $source ) ? $source : array();
		$wp        = function_exists( 'get_option' );
		$data      = array();

		foreach ( self::ALLOWED as $option_key => $dto_key ) {
			if ( array_key_exists( $option_key, $overrides ) ) {
				$data[ $dto_key ] = (string) $overrides[ $option_key ];
				continue;
			}

			if ( $wp ) {
				$value            = get_option( $option_key, '' );
				$data[ $dto_key ] = is_scalar( $value ) ? (string) $value : '';
				continue;
			}

			$data[ $dto_key ] = '';
		}

		return new ViewModel( $data );
	}
}
