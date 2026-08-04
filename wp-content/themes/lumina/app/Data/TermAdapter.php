<?php
/**
 * TermAdapter — normalizes a WP_Term (id, array, or stdClass) into a ViewModel.
 *
 * Phase 4 (Render Engine): the canonical taxonomy-term DTO used by category
 * chips, filter bars and meta rows. WP-loaded contexts resolve links via
 * get_term_link(); WP-free contexts fall back to raw fields.
 *
 * @package Lumina\Core\Data
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Data;

use Lumina\Core\Render\ViewModel;

/**
 * Term data adapter.
 */
class TermAdapter implements DataAdapterInterface {

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
		return class_exists( 'WP_Term' ) && $source instanceof \WP_Term;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed                $source  Source value.
	 * @param array<string, mixed> $options Adapter options.
	 */
	public function adapt( mixed $source, array $options = array() ): ViewModel {
		$raw = $this->raw_fields( $source );
		$id  = (int) ( $raw['term_id'] ?? $raw['id'] ?? 0 );

		return new ViewModel(
			array(
				'id'          => $id,
				'name'        => (string) ( $raw['name'] ?? '' ),
				'slug'        => (string) ( $raw['slug'] ?? '' ),
				'taxonomy'    => (string) ( $raw['taxonomy'] ?? $options['taxonomy'] ?? 'category' ),
				'description' => (string) ( $raw['description'] ?? '' ),
				'count'       => (int) ( $raw['count'] ?? 0 ),
				'parent'      => (int) ( $raw['parent'] ?? 0 ),
				'link'        => $this->term_link( $id, (string) ( $raw['taxonomy'] ?? '' ) ),
			)
		);
	}

	/**
	 * Extract raw term fields from any supported source shape.
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
		if ( class_exists( 'WP_Term' ) && $source instanceof \WP_Term ) {
			return get_object_vars( $source );
		}

		return array( 'term_id' => (int) $source );
	}

	/**
	 * Resolve the term link ('' in WP-free contexts).
	 *
	 * @param int    $id       Term id.
	 * @param string $taxonomy Taxonomy name.
	 * @return string
	 */
	private function term_link( int $id, string $taxonomy ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( function_exists( 'get_term_link' ) && $id > 0 ) {
			$url = get_term_link( $id, $taxonomy );

			return is_string( $url ) ? $url : '';
		}

		return '';
	}
}
