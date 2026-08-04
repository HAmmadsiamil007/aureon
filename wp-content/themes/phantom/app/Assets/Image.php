<?php
/**
 * Image — responsive image helpers.
 *
 * Phase 7 (Asset Pipeline): `srcset()` resolves attachment data through the
 * public WordPress API when present and returns an empty map in WP-free
 * contexts (CLI/smoke). `build_srcset()` is the pure, testable core that
 * turns a source list into a standards `srcset` attribute string.
 *
 * @package Phantom\Core\Assets
 * @since 0.7.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Assets;

/**
 * Responsive image data.
 */
class Image {

	/**
	 * Resolve srcset data for an attachment id.
	 *
	 * WordPress present: returns array{src, srcset, sizes}. WP-free: array().
	 *
	 * @param int             $id   Attachment id.
	 * @param array<int, int> $size Size (width, height).
	 * @return array<string, mixed>
	 */
	public static function srcset( int $id, array $size = array() ): array {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core functions.
		if (
			! function_exists( 'wp_get_attachment_image_src' )
			|| ! function_exists( 'wp_get_attachment_image_srcset' )
		) {
			return array();
		}

		$src = wp_get_attachment_image_src( $id, $size );

		if ( false === $src || ! is_array( $src ) ) {
			return array();
		}

		$srcset = wp_get_attachment_image_srcset( $id, $size );

		return array(
			// The WP stub types the src tuple {string, int, int, bool}; the casts
			// absorb defensive runtime variance without breaking the contract.
			'src'    => (string) $src[0],
			'srcset' => (string) $srcset,
			'sizes'  => '(max-width: ' . (string) (int) $src[1] . 'px) 100vw, ' . (string) (int) $src[1] . 'px',
		);
	}

	/**
	 * Build a `srcset` attribute value from a source list.
	 *
	 * Pure helper (WP-free): each source is array{url: string, width: int}.
	 *
	 * @param array<int, array<string, mixed>> $sources Source list.
	 * @return string
	 */
	public static function build_srcset( array $sources ): string {
		$parts = array();

		foreach ( $sources as $source ) {
			$url   = isset( $source['url'] ) ? (string) $source['url'] : '';
			$width = isset( $source['width'] ) ? (int) $source['width'] : 0;

			if ( '' !== $url && $width > 0 ) {
				$parts[] = $url . ' ' . (string) $width . 'w';
			}
		}

		return implode( ', ', $parts );
	}
}
