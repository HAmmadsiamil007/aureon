<?php
/**
 * AcfBridge — Advanced Custom Fields capability adapter.
 *
 * Phase 8 (Plugin Bridges): field/sub-field/image access through the public
 * ACF API only, capability-guarded; WP-free contexts return empty defaults.
 *
 * @package Lumina\Core\Bridges\Acf
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Bridges\Acf;

use Lumina\Core\Bridges\Bridge;

/**
 * ACF adapter.
 */
final class AcfBridge extends Bridge {

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'acf';
	}

	/**
	 * {@inheritDoc}
	 */
	public function name(): string {
		return 'Advanced Custom Fields';
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_active(): bool {
		return $this->guard( 'ACF', 'get_field' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->constant_version( 'ACF_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function capabilities(): array {
		return array( 'fields', 'sub_fields', 'image', 'group', 'repeater' );
	}

	/**
	 * All fields of a post/term/user (empty when ACF is absent).
	 *
	 * @param int $id Object id.
	 * @return array<string, mixed>
	 */
	public function fields( int $id = 0 ): array {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP/ACF core function.
		if ( ! function_exists( 'get_fields' ) ) {
			return array();
		}

		$fields = get_fields( 0 === $id ? null : $id );

		return is_array( $fields ) ? $fields : array();
	}

	/**
	 * A single field value.
	 *
	 * @param string $key  Field key or name.
	 * @param int    $id   Object id.
	 * @return mixed
	 */
	public function field( string $key, int $id = 0 ): mixed {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP/ACF core function.
		if ( ! function_exists( 'get_field' ) ) {
			return null;
		}

		return get_field( $key, 0 === $id ? null : $id );
	}

	/**
	 * Sub-fields of a repeater/group field.
	 *
	 * @param string $selector Repeater field selector.
	 * @param int    $id       Object id.
	 * @return list<array<string, mixed>>
	 */
	public function sub_fields( string $selector, int $id = 0 ): array {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP/ACF core function.
		if ( ! function_exists( 'get_field' ) ) {
			return array();
		}

		$value = get_field( $selector, 0 === $id ? null : $id );

		return is_array( $value ) ? array_values( $value ) : array();
	}
}
