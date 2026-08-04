<?php
/**
 * TaxAdapter — normalizes the taxonomy terms of a post into a ViewModel.
 *
 * Phase 4 (Render Engine): the taxonomy DTO used for category/tag chips on
 * cards and single views. WP-loaded contexts read get_the_terms() for the
 * requested taxonomy; WP-free contexts derive terms from a 'terms' key in the
 * source array or resolve to an empty list. Each term is normalized through
 * the TermAdapter so the shape stays identical everywhere.
 *
 * @package Lumina\Core\Data
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Data;

use Lumina\Core\Render\ViewModel;

/**
 * Taxonomy terms data adapter.
 */
class TaxAdapter implements DataAdapterInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed $source Source value.
	 */
	public function supports( mixed $source ): bool {
		return is_array( $source ) || is_int( $source ) || $source instanceof \stdClass;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed                $source  Source value.
	 * @param array<string, mixed> $options Adapter options.
	 */
	public function adapt( mixed $source, array $options = array() ): ViewModel {
		$taxonomy = (string) ( $options['taxonomy'] ?? 'category' );
		$terms    = array();
		$term     = new TermAdapter();

		if ( is_array( $source ) && isset( $source['terms'] ) && is_array( $source['terms'] ) ) {
			foreach ( $source['terms'] as $raw_term ) {
				$terms[] = $term->adapt( $raw_term, array( 'taxonomy' => $taxonomy ) )->all();
			}

			return new ViewModel(
				array(
					'taxonomy' => $taxonomy,
					'terms'    => $terms,
				)
			);
		}

		$post_id = is_int( $source ) ? $source : (int) ( is_object( $source ) ? ( $source->ID ?? 0 ) : 0 );

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( function_exists( 'get_the_terms' ) && $post_id > 0 ) {
			$wp_terms = get_the_terms( $post_id, $taxonomy );

			if ( is_array( $wp_terms ) ) {
				foreach ( $wp_terms as $wp_term ) {
					$terms[] = $term->adapt( $wp_term, array( 'taxonomy' => $taxonomy ) )->all();
				}
			}
		}

		return new ViewModel(
			array(
				'taxonomy' => $taxonomy,
				'terms'    => $terms,
			)
		);
	}
}
