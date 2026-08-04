<?php
/**
 * PostAdapter — normalizes a WP_Post (id, array, or stdClass) into a ViewModel.
 *
 * Phase 4 (Render Engine): the canonical post DTO consumed by post-card,
 * archive and single templates. WP-loaded contexts use the object fields
 * directly plus get_permalink(); WP-free contexts accept arrays/stdClass
 * (including CLI smoke fixtures) and fall back to raw field names.
 *
 * @package Lumina\Core\Data
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Data;

use Lumina\Core\Render\ViewModel;

/**
 * Post data adapter.
 */
class PostAdapter implements DataAdapterInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed $source Source value.
	 */
	public function supports( mixed $source ): bool {
		if ( is_int( $source ) || ( is_string( $source ) && ctype_digit( $source ) ) ) {
			return true;
		}

		if ( is_array( $source ) || $source instanceof \stdClass ) {
			return true;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core class.
		return class_exists( 'WP_Post' ) && $source instanceof \WP_Post;
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

		return new ViewModel(
			array(
				'id'             => $id,
				'title'          => (string) ( $raw['post_title'] ?? $raw['title'] ?? '' ),
				'excerpt'        => (string) ( $raw['post_excerpt'] ?? $raw['excerpt'] ?? '' ),
				'content'        => (string) ( $raw['post_content'] ?? $raw['content'] ?? '' ),
				'link'           => $this->permalink( $id ),
				'date'           => (string) ( $raw['post_date'] ?? $raw['date'] ?? '' ),
				'modified'       => (string) ( $raw['post_modified'] ?? $raw['modified'] ?? '' ),
				'author_id'      => (int) ( $raw['post_author'] ?? $raw['author_id'] ?? 0 ),
				'thumbnail'      => (int) ( $raw['_thumbnail_id'] ?? $raw['thumbnail'] ?? 0 ),
				'type'           => (string) ( $raw['post_type'] ?? $raw['type'] ?? 'post' ),
				'status'         => (string) ( $raw['post_status'] ?? $raw['status'] ?? 'publish' ),
				'menu_order'     => (int) ( $raw['menu_order'] ?? 0 ),
				'comment_status' => (string) ( $raw['comment_status'] ?? 'open' ),
			)
		);
	}

	/**
	 * Extract raw post fields from any supported source shape.
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
		if ( class_exists( 'WP_Post' ) && $source instanceof \WP_Post ) {
			return get_object_vars( $source );
		}

		if ( is_int( $source ) || ( is_string( $source ) && ctype_digit( $source ) ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			if ( function_exists( 'get_post' ) ) {
				$post = get_post( (int) $source );

				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core class.
				if ( $post instanceof \WP_Post ) {
					return get_object_vars( $post );
				}
			}

			return array( 'ID' => (int) $source );
		}

		return array();
	}

	/**
	 * Resolve the permalink for a post id ('' in WP-free contexts).
	 *
	 * @param int $id Post id.
	 * @return string
	 */
	private function permalink( int $id ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( function_exists( 'get_permalink' ) && $id > 0 ) {
			$url = get_permalink( $id );

			return is_string( $url ) ? $url : '';
		}

		return '';
	}
}
