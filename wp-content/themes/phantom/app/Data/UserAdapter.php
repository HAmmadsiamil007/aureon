<?php
/**
 * UserAdapter — normalizes a WP_User (id, array, or stdClass) into a ViewModel.
 *
 * Phase 4 (Render Engine): the canonical author/account DTO. WP-loaded
 * contexts resolve author archives and avatars; WP-free contexts accept
 * arrays/stdClass (CLI smoke fixtures) with raw field names.
 *
 * @package Phantom\Core\Data
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Data;

use Phantom\Core\Render\ViewModel;

/**
 * User data adapter.
 */
class UserAdapter implements DataAdapterInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed $source Source value.
	 */
	public function supports( mixed $source ): bool {
		if ( is_int( $source ) || is_array( $source ) || $source instanceof \stdClass ) {
			return true;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core class.
		return class_exists( 'WP_User' ) && $source instanceof \WP_User;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed                $source  Source value.
	 * @param array<string, mixed> $options Adapter options.
	 */
	public function adapt( mixed $source, array $options = array() ): ViewModel {
		$raw = $this->raw_fields( $source );
		$id  = (int) ( $raw['ID'] ?? $raw['id'] ?? 0 );

		$roles = array();
		if ( ! empty( $raw['roles'] ) && is_array( $raw['roles'] ) ) {
			$roles = array_values( array_map( 'strval', $raw['roles'] ) );
		}

		return new ViewModel(
			array(
				'id'          => $id,
				'name'        => (string) ( $raw['display_name'] ?? $raw['name'] ?? '' ),
				'nicename'    => (string) ( $raw['user_nicename'] ?? $raw['nicename'] ?? '' ),
				'email'       => (string) ( $raw['user_email'] ?? $raw['email'] ?? '' ),
				'description' => (string) ( $raw['description'] ?? '' ),
				'roles'       => $roles,
				'link'        => $this->author_link( $id, (string) ( $raw['user_nicename'] ?? '' ) ),
				'avatar'      => $this->avatar( $id, (string) ( $raw['user_email'] ?? '' ) ),
			)
		);
	}

	/**
	 * Extract raw user fields from any supported source shape.
	 *
	 * @param mixed $source Source value.
	 * @return array<string, mixed>
	 */
	private function raw_fields( mixed $source ): array {
		if ( is_array( $source ) ) {
			return $source;
		}

		if ( $source instanceof \stdClass ) {
			return get_object_vars( $source );
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core class.
		if ( class_exists( 'WP_User' ) && $source instanceof \WP_User ) {
			return get_object_vars( $source );
		}

		return array( 'ID' => (int) $source );
	}

	/**
	 * Resolve the author archive link ('' in WP-free contexts).
	 *
	 * @param int    $id       User id.
	 * @param string $nicename User nicename.
	 * @return string
	 */
	private function author_link( int $id, string $nicename ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( function_exists( 'get_author_posts_url' ) && $id > 0 ) {
			// get_author_posts_url() is stubbed string-returning; the cast also
			// absorbs the WP-free/edge false return without breaking the contract.
			return (string) get_author_posts_url( $id, $nicename );
		}

		return '';
	}

	/**
	 * Resolve an avatar URL ('' in WP-free contexts).
	 *
	 * @param int    $id    User id.
	 * @param string $email User email.
	 * @return string
	 */
	private function avatar( int $id, string $email ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'get_avatar_url' ) ) {
			return '';
		}

		$url = get_avatar_url( $email, array( 'size' => 96 ) );

		if ( $id > 0 ) {
			$url = get_avatar_url( $id, array( 'size' => 96 ) );
		}

		// get_avatar_url() is stubbed string-returning; the cast also absorbs the
		// WP-free/edge false return without violating the string contract.
		return (string) $url;
	}
}
