<?php
/**
 * TokenRepository — public facade for the Design Token Engine.
 *
 * Phase 3 (Design Token Engine): single entry point for consuming tokens.
 *   - tokens()   — resolved token map (optionally filtered by group)
 *   - token()    — resolved value for one token (throws UnknownToken)
 *   - resolve()  — inheritance-aware lookup returning null for unknown names
 *   - css()      — rendered CSS custom-property blocks (:root + presets)
 *
 * Precedence: canonical defaults → preset → (future env overrides). The base
 * layer is the 'default' preset merged with canonical defaults; every other
 * preset is rendered as a [data-phantom-theme="<slug>"] block.
 *
 * @package Phantom\Core\Tokens
 * @since 0.3.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Tokens;

use Phantom\Core\Tokens\Loader\DataProvider;
use Phantom\Core\Tokens\Renderer\CssRenderer;

/**
 * Consumes design tokens.
 */
final class TokenRepository {

	/**
	 * Token data source.
	 *
	 * @var DataProvider
	 */
	private DataProvider $provider;

	/**
	 * Token name/definition parser.
	 *
	 * @var TokenSource
	 */
	private TokenSource $source;

	/**
	 * Precedence collector.
	 *
	 * @var Preced
	 */
	private Preced $preced;

	/**
	 * Inheritance resolver.
	 *
	 * @var Resolver
	 */
	private Resolver $resolver;

	/**
	 * CSS renderer.
	 *
	 * @var CssRenderer
	 */
	private CssRenderer $renderer;

	/**
	 * Validator + contrast engine.
	 *
	 * @var Invariant
	 */
	private Invariant $invariant;

	/**
	 * Resolved default token map (memoized).
	 *
	 * @var array<string, mixed>|null
	 */
	private ?array $default = null;

	/**
	 * Resolved preset maps (memoized).
	 *
	 * @var array<string, mixed>|null
	 */
	private ?array $presets = null;

	/**
	 * Constructor.
	 *
	 * @param DataProvider $provider  Token data source.
	 * @param TokenSource  $source    Token parser/validator.
	 * @param Preced       $preced    Precedence collector.
	 * @param Resolver     $resolver  Inheritance resolver.
	 * @param CssRenderer  $renderer  CSS renderer.
	 * @param Invariant    $invariant Validator + contrast.
	 */
	public function __construct(
		DataProvider $provider,
		TokenSource $source,
		Preced $preced,
		Resolver $resolver,
		CssRenderer $renderer,
		Invariant $invariant
	) {
		$this->provider  = $provider;
		$this->source    = $source;
		$this->preced    = $preced;
		$this->resolver  = $resolver;
		$this->renderer  = $renderer;
		$this->invariant = $invariant;
	}

	/**
	 * The resolved default token map (all tokens, after inheritance).
	 *
	 * @param string $context Group prefix filter (e.g. "color"), or "all".
	 * @return array<string, mixed>
	 */
	public function tokens( string $context = 'all' ): array {
		$map = $this->default_map();

		if ( 'all' === $context || '' === $context ) {
			return $map;
		}

		$prefix   = $context . '.';
		$filtered = array();

		foreach ( $map as $name => $value ) {
			if ( str_starts_with( $name, $prefix ) ) {
				$filtered[ $name ] = $value;
			}
		}

		return $filtered;
	}

	/**
	 * Resolved value for a single token.
	 *
	 * @param string $name Token name, e.g. "color.accent".
	 * @return mixed
	 * @throws UnknownToken When the token does not exist.
	 */
	public function token( string $name ): mixed {
		$map = $this->default_map();

		if ( ! array_key_exists( $name, $map ) ) {
			throw new UnknownToken(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- exception messages are developer-facing, not HTML.
				'Unknown design token "' . $name . '".'
			);
		}

		return $map[ $name ];
	}

	/**
	 * Inheritance-aware lookup; null for unknown/unresolvable names.
	 *
	 * @param string $name Token name.
	 * @return mixed
	 */
	public function resolve( string $name ): mixed {
		$map = $this->default_map();

		return $map[ $name ] ?? null;
	}

	/**
	 * Rendered CSS custom-property blocks (:root + preset variants).
	 *
	 * @param string $scope CSS variable scope prefix.
	 * @return string
	 */
	public function css( string $scope = 'phantom' ): string {
		return $this->renderer->render( $this->default_map(), $this->preset_maps(), $scope );
	}

	/**
	 * Invariant violations for the default token set.
	 *
	 * @return string[] Empty when valid.
	 */
	public function validate(): array {
		return $this->invariant->validate( $this->flat_default() );
	}

	/**
	 * Whether the default color pair passes WCAG AA contrast.
	 *
	 * @return bool
	 */
	public function contrast_passes(): bool {
		return $this->invariant->contrast_passes( $this->default_map() );
	}

	/**
	 * Raw (unresolved) default flat token map.
	 *
	 * @return array<string, mixed>
	 */
	private function flat_default(): array {
		return $this->source->parse( $this->provider->tokens() );
	}

	/**
	 * Resolved default token map (memoized).
	 *
	 * @return array<string, mixed>
	 */
	private function default_map(): array {
		if ( null === $this->default ) {
			$default_preset = $this->source->parse(
				(array) ( $this->provider->presets()['default'] ?? array() )
			);

			$this->default = $this->resolver->resolve_all(
				$this->preced->collect( $this->flat_default(), $default_preset )
			);
		}

		return $this->default;
	}

	/**
	 * Resolved preset maps (all non-default presets, memoized).
	 *
	 * @return array<string, mixed>
	 */
	private function preset_maps(): array {
		if ( null === $this->presets ) {
			$maps = array();

			foreach ( (array) $this->provider->presets() as $slug => $override ) {
				if ( 'default' === $slug ) {
					continue;
				}

				$overrides     = $this->source->parse( (array) $override );
				$maps[ $slug ] = $this->resolver->resolve_all(
					$this->preced->collect( $this->flat_default(), $overrides )
				);
			}

			$this->presets = $maps;
		}

		return $this->presets;
	}
}
