<?php
/**
 * AssetLoader — public asset pipeline API.
 *
 * Phase 7 (Asset Pipeline): resolves source paths to hashed production URLs
 * (via the Vite manifest) or dev-server URLs (when `LUMINA_VITE_ACTIVE=1`),
 * and exposes the WordPress enqueue surface (`css()`, `js()`, `font_face()`)
 * — every WP call capability-guarded so the loader is safe WP-free.
 *
 * Public API (plan §Phase 7): css($handle), js($handle, $deps, $inFooter),
 * assetUrl($src), fontFace($font). `resolve()` is the WP-free test seam.
 *
 * @package Lumina\Core\Assets
 * @since 0.7.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Assets;

/**
 * Asset URL resolution + guarded enqueue helpers.
 */
class AssetLoader {

	/**
	 * Manifest reader.
	 *
	 * @var ManifestReader
	 */
	private ManifestReader $manifest;

	/**
	 * Dev server.
	 *
	 * @var DevServer
	 */
	private DevServer $dev_server;

	/**
	 * Base URL for hashed assets (e.g. theme/assets/dist).
	 *
	 * @var string
	 */
	private string $base_url;

	/**
	 * Build fingerprint (used as the enqueue version for cache busting).
	 *
	 * @var BuildFingerprint
	 */
	private BuildFingerprint $fingerprint;

	/**
	 * Build the loader.
	 *
	 * @param ManifestReader $manifest   Manifest reader.
	 * @param DevServer      $dev_server Dev-server config.
	 * @param string         $base_url   Base URL for dist assets.
	 */
	public function __construct(
		ManifestReader $manifest,
		DevServer $dev_server,
		string $base_url = ''
	) {
		$this->manifest    = $manifest;
		$this->dev_server  = $dev_server;
		$this->base_url    = rtrim( $base_url, '/' );
		$this->fingerprint = new BuildFingerprint( $manifest );
	}

	/**
	 * Resolve a source path to a production or dev URL.
	 *
	 * Priority: dev server (active) → manifest hashed file → raw source.
	 *
	 * @param string $src Source path (e.g. 'assets-src/ts/main.ts').
	 * @return string
	 */
	public function resolve( string $src ): string {
		if ( $this->dev_server->is_active() ) {
			return $this->dev_server->url( $src );
		}

		$file = $this->manifest->file( $src );

		if ( null !== $file ) {
			return $this->base_url . '/' . ltrim( $file, '/' );
		}

		return $this->base_url . '/' . ltrim( $src, '/' );
	}

	/**
	 * {@inheritDoc} alias of resolve() (plan API name).
	 *
	 * @param string $src Source path.
	 * @return string
	 */
	public function asset_url( string $src ): string {
		return $this->resolve( $src );
	}

	/**
	 * Enqueue a CSS entry by source (WordPress-guarded).
	 *
	 * @param string $src CSS entry source (or handle).
	 * @return void
	 */
	public function css( string $src ): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'wp_enqueue_style' ) ) {
			return;
		}

		$url = $this->resolve( $src );

		// The handle stays stable across builds; the hashed URL + build token
		// version bust the browser cache together.
		wp_enqueue_style( 'lumina-' . sanitize_key( $src ), $url, array(), $this->fingerprint->token() );
	}

	/**
	 * Enqueue a JS entry by source (WordPress-guarded).
	 *
	 * @param string $src       JS entry source.
	 * @param array  $deps      WP script dependency handles.
	 * @param bool   $in_footer Load in footer (default true).
	 * @return void
	 */
	public function js( string $src, array $deps = array(), bool $in_footer = true ): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'wp_enqueue_script' ) ) {
			return;
		}

		$url = $this->resolve( $src );

		wp_enqueue_script( 'lumina-' . sanitize_key( $src ), $url, $deps, $this->fingerprint->token(), $in_footer );
	}

	/**
	 * Emit an @font-face block through wp_add_inline_style (WordPress-guarded).
	 *
	 * @param array<string, mixed> $font Font face definition (family, src, …).
	 * @return void
	 */
	public function font_face( array $font ): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core functions.
		if ( ! function_exists( 'wp_add_inline_style' ) || ! function_exists( 'wp_style_is' ) ) {
			return;
		}

		$family = isset( $font['family'] ) && is_string( $font['family'] ) ? $font['family'] : '';
		$src    = isset( $font['src'] ) && is_string( $font['src'] ) ? $font['src'] : '';

		if ( '' === $family || '' === $src ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS is built from sanitized font tokens.
		$css = sprintf(
			"@font-face{font-family:'%s';src:url('%s') format('woff2');font-display:swap;}",
			str_replace( "'", '', $family ),
			$src
		);

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		wp_add_inline_style( 'lumina-assets', $css );
	}
}
