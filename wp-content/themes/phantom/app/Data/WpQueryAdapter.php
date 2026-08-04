<?php
/**
 * WpQueryAdapter — normalizes a WP_Query (or array) into a ViewModel.
 *
 * Phase 4 (Render Engine): the canonical archive/loop DTO — the post list plus
 * pagination metadata (found, page, max pages, has_next/has_prev). WP-loaded
 * contexts consume a WP_Query instance; WP-free contexts accept an array with
 * 'posts' (list of post arrays) plus optional pagination keys. Posts are
 * normalized through the PostAdapter so every consumer sees the same DTO.
 *
 * @package Phantom\Core\Data
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Data;

use Phantom\Core\Render\ViewModel;

/**
 * Query loop data adapter.
 */
class WpQueryAdapter implements DataAdapterInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed $source Source value.
	 */
	public function supports( mixed $source ): bool {
		if ( is_array( $source ) ) {
			return true;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core class.
		return class_exists( 'WP_Query' ) && $source instanceof \WP_Query;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed                $source  Source value.
	 * @param array<string, mixed> $options Adapter options.
	 */
	public function adapt( mixed $source, array $options = array() ): ViewModel {
		$post  = new PostAdapter();
		$data  = $this->raw( $source );
		$posts = array();

		foreach ( (array) ( $data['posts'] ?? array() ) as $raw_post ) {
			$posts[] = $post->adapt( $raw_post )->all();
		}

		return new ViewModel(
			array(
				'posts'         => $posts,
				'found_posts'   => (int) ( $data['found_posts'] ?? count( $posts ) ),
				'post_count'    => (int) ( $data['post_count'] ?? count( $posts ) ),
				'max_num_pages' => (int) ( $data['max_num_pages'] ?? 1 ),
				'current_page'  => (int) ( $data['current_page'] ?? 1 ),
				'has_next'      => (bool) ( $data['has_next'] ?? false ),
				'has_prev'      => (bool) ( $data['has_prev'] ?? false ),
			)
		);
	}

	/**
	 * Extract raw query data from a WP_Query or array.
	 *
	 * @param mixed $source Query source.
	 * @return array<string, mixed>
	 */
	private function raw( mixed $source ): array {
		if ( is_array( $source ) ) {
			return $source;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core class.
		if ( ! class_exists( 'WP_Query' ) || ! $source instanceof \WP_Query ) {
			return array();
		}

		return array(
			'posts'         => $source->posts,
			'found_posts'   => (int) $source->found_posts,
			'post_count'    => (int) $source->post_count,
			'max_num_pages' => (int) $source->max_num_pages,
			'current_page'  => max( 1, (int) $source->get( 'paged' ) ),
			'has_next'      => (int) $source->max_num_pages > max( 1, (int) $source->get( 'paged' ) ),
			'has_prev'      => max( 1, (int) $source->get( 'paged' ) ) > 1,
		);
	}
}
