<?php
/**
 * Resolver — variant and slot resolution for component rendering.
 *
 * Phase 5 (Component Registry): turns a request's props into the exact prop
 * map a template sees. Variant presets are merged as defaults (explicit props
 * win); declared slots are materialized from child-component lists into
 * trusted HTML strings before the renderer runs.
 *
 * @package Lumina\Core\Components
 * @since 0.5.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Components;

/**
 * Resolves variants and slot content for a definition.
 */
final class Resolver {

	/**
	 * Merge variant presets under explicit props.
	 *
	 * A request with `variant => 'primary'` gets the variant's prop overrides
	 * as defaults; explicit props always win. Unknown variants are ignored
	 * (the component renders with its base props) rather than failing.
	 *
	 * @param ComponentDefinition  $definition Component definition.
	 * @param array<string, mixed> $props      Requested props.
	 * @return array<string, mixed>
	 */
	public function variant( ComponentDefinition $definition, array $props ): array {
		$variant = isset( $props['variant'] ) && is_string( $props['variant'] )
			? $props['variant']
			: '';

		if ( '' === $variant || ! $definition->has_variant( $variant ) ) {
			return $props;
		}

		$preset = $definition->variants()[ $variant ];

		if ( ! is_array( $preset ) ) {
			return $props;
		}

		return array_merge( $preset, $props );
	}

	/**
	 * Whether a prop value carries child-component slot content.
	 *
	 * @param mixed $value Prop value.
	 * @return bool
	 */
	public function has_slot_content( mixed $value ): bool {
		return is_array( $value );
	}
}
