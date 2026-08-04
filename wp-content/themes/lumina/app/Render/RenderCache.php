<?php
/**
 * RenderCache — view-keyed render result cache.
 *
 * Phase 4 (Render Engine): caches rendered HTML by (view, data-hash) so
 * identical renders skip template execution. Two safety rails (plan §Phase 4):
 *
 *   - Never caches for logged-in users (stale personalized content).
 *   - Cache failures are swallowed — a broken cache must never break a page.
 *
 * The backing store is a CacheInterface (Phase 2). In WP-free CLI/smoke
 * contexts the Renderer works without a store; tests inject an in-memory
 * store implementing CacheInterface to exercise the caching path.
 *
 * @package Lumina\Core\Render
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Render;

use Lumina\Core\Cache\CacheInterface;

/**
 * Render result cache.
 */
class RenderCache {

	/**
	 * Backing store (null = caching disabled).
	 *
	 * @var CacheInterface|null
	 */
	private ?CacheInterface $store;

	/**
	 * Cache entry TTL in seconds.
	 *
	 * @var int
	 */
	private int $ttl;

	/**
	 * Build the cache.
	 *
	 * @param CacheInterface|null $store Backing store.
	 * @param int                 $ttl   Entry lifetime in seconds.
	 */
	public function __construct( ?CacheInterface $store = null, int $ttl = 3600 ) {
		$this->store = $store;
		$this->ttl   = max( 1, $ttl );
	}

	/**
	 * Whether caching is active for the current request.
	 *
	 * Disabled when no store is attached or when a logged-in user is present.
	 *
	 * @return bool
	 */
	public function enabled(): bool {
		if ( null === $this->store ) {
			return false;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function, not ours.
		if ( function_exists( 'is_user_logged_in' ) && is_user_logged_in() ) {
			return false;
		}

		return true;
	}

	/**
	 * Read a cached render, if present.
	 *
	 * @param string               $view View slug.
	 * @param array<string, mixed> $data Render data.
	 * @return string|null Cached HTML, or null on miss/failure.
	 */
	public function get( string $view, array $data ): ?string {
		if ( ! $this->enabled() ) {
			return null;
		}

		try {
			$value = $this->store->get( $this->key( $view, $data ), null );

			return is_string( $value ) ? $value : null;
		} catch ( \Throwable $throwable ) {
			return null;
		}
	}

	/**
	 * Store a rendered result.
	 *
	 * @param string               $view View slug.
	 * @param array<string, mixed> $data Render data.
	 * @param string               $html Rendered HTML.
	 * @return void
	 */
	public function put( string $view, array $data, string $html ): void {
		if ( ! $this->enabled() ) {
			return;
		}

		try {
			$this->store->set( $this->key( $view, $data ), $html, $this->ttl );
		} catch ( \Throwable $throwable ) {
			// Cache writes must never break the render path (intentional no-op).
			unset( $throwable );
		}
	}

	/**
	 * Deterministic cache key for (view, data).
	 *
	 * @param string               $view View slug.
	 * @param array<string, mixed> $data Render data.
	 * @return string
	 */
	private function key( string $view, array $data ): string {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- wp_json_encode() is unavailable in WP-free CLI contexts.
		$payload = json_encode( $data );

		return 'lumina_render:' . md5( $view . ':' . ( is_string( $payload ) ? $payload : '' ) );
	}
}
