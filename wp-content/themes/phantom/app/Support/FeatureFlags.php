<?php
/**
 * FeatureFlags — feature-flag accessor.
 *
 * Phase 1 (Bootstrap): every phase ships behind a feature flag
 * (`phantom_feature_*`, ADR-002). Flags default OFF for unshipped subsystems
 * and ON for complete ones; per-environment toggling happens via
 * phantom.env.json — never edited in code.
 *
 * @package Phantom\Core\Support
 * @since 0.1.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Support;

/**
 * Reads feature-flag state from the config `features` map.
 */
final class FeatureFlags {

	/**
	 * Flag map (name → bool).
	 *
	 * @var array<string, bool>
	 */
	private array $flags;

	/**
	 * Constructor.
	 *
	 * @param array<string, bool> $flags Flag map from config.
	 */
	public function __construct( array $flags = array() ) {
		$this->flags = $flags;
	}

	/**
	 * Whether a feature flag is enabled.
	 *
	 * Unknown or unset flags are disabled (fail closed).
	 *
	 * @param string $name Flag name without the phantom_feature_ prefix.
	 * @return bool
	 */
	public function enabled( string $name ): bool {
		return isset( $this->flags[ $name ] ) && true === $this->flags[ $name ];
	}

	/**
	 * All known flags.
	 *
	 * @return array<string, bool>
	 */
	public function all(): array {
		return $this->flags;
	}
}
