<?php
/**
 * DefinitionCompiler — validates and normalizes raw component definitions.
 *
 * Phase 5 (Component Registry): converts a raw definition array (from JSON
 * discovery or PHP `register()` meta) into an immutable ComponentDefinition,
 * enforcing the schema: valid name, non-empty renderer, int version ≥ 1,
 * and list-shaped slots/deps. Invalid definitions throw ComponentException so
 * configuration mistakes surface at registration time, not render time.
 *
 * @package Lumina\Core\Components
 * @since 0.5.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Components;

/**
 * Compiles raw definitions into ComponentDefinition objects.
 */
final class DefinitionCompiler {

	/**
	 * Valid component name: lowercase alnum + hyphens, ≤ 64 chars.
	 *
	 * @var string
	 */
	private const NAME_PATTERN = '/^[a-z0-9][a-z0-9\-]{0,63}$/';

	/**
	 * Compile a raw definition into an immutable definition.
	 *
	 * @param string               $name     Component name.
	 * @param string               $renderer Renderer view slug.
	 * @param array<string, mixed> $meta     Definition meta (slug, data,
	 *                                       variants, slots, deps, version).
	 * @return ComponentDefinition
	 * @throws ComponentException When the definition is invalid.
	 */
	public function compile( string $name, string $renderer, array $meta = array() ): ComponentDefinition {
		$this->assert_name( $name );

		if ( '' === trim( $renderer ) ) {
			throw new ComponentException(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- developer-facing message, not HTML.
				sprintf( 'Component %s declares an empty renderer view.', $name )
			);
		}

		$slug    = isset( $meta['slug'] ) && is_string( $meta['slug'] ) ? trim( $meta['slug'] ) : $name;
		$data    = isset( $meta['data'] ) && is_array( $meta['data'] ) ? $meta['data'] : array();
		$slots   = $this->string_list( $meta['slots'] ?? array(), 'slots' );
		$deps    = $this->string_list( $meta['deps'] ?? array(), 'deps' );
		$version = isset( $meta['version'] ) && is_int( $meta['version'] ) ? $meta['version'] : 1;

		$variants = array();
		if ( isset( $meta['variants'] ) && is_array( $meta['variants'] ) ) {
			foreach ( $meta['variants'] as $variant_name => $overrides ) {
				if ( is_string( $variant_name ) && is_array( $overrides ) ) {
					$variants[ $variant_name ] = $overrides;
				}
			}
		}

		return new ComponentDefinition(
			$name,
			$renderer,
			$slug,
			$data,
			$variants,
			$slots,
			$deps,
			$version
		);
	}

	/**
	 * Validate a component name against the canonical pattern.
	 *
	 * @param string $name Component name.
	 * @return void
	 * @throws ComponentException On invalid names.
	 */
	private function assert_name( string $name ): void {
		if ( 1 !== preg_match( self::NAME_PATTERN, $name ) ) {
			throw new ComponentException(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- developer-facing message, not HTML.
				sprintf( 'Invalid component name "%s".', $name )
			);
		}
	}

	/**
	 * Normalize a mixed value into a list of non-empty strings.
	 *
	 * @param mixed  $value Raw value.
	 * @param string $field Field label for the error message.
	 * @return list<string>
	 * @throws ComponentException When the value is not a list.
	 */
	private function string_list( mixed $value, string $field ): array {
		if ( ! is_array( $value ) ) {
			throw new ComponentException(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- developer-facing message, not HTML.
				sprintf( 'Component field "%s" must be a list.', $field )
			);
		}

		$list = array();

		foreach ( $value as $item ) {
			if ( is_string( $item ) && '' !== trim( $item ) ) {
				$list[] = trim( $item );
			}
		}

		return $list;
	}
}
