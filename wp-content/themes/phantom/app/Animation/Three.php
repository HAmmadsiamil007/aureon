<?php
/**
 * Three — scoped Three.js canvas facade.
 *
 * Phase 10 (Animation Engine): declares Three.js scenes scoped to hero/canvas
 * sections only (`with_canvas()`). Three.js is loaded as a dynamic import
 * (code-split) — never global — and only when a matching `.phantom-canvas`
 * mount exists in the DOM (plan §Phase 10 acceptance). The PHP side records
 * the mounts + per-mount config; the runtime instantiates lazily.
 *
 * @package Phantom\Core\Animation
 * @since 0.10.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Animation;

/**
 * Three.js scene declarations.
 */
class Three {

	/**
	 * Declared canvas mounts: selector → config.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private array $mounts = array();

	/**
	 * Declare a Three.js scene for a canvas mount.
	 *
	 * @param string               $mount  CSS selector of the canvas mount.
	 * @param array<string, mixed> $config Scene config (e.g. clear color).
	 * @return void
	 */
	public function with_canvas( string $mount, array $config = array() ): void {
		$this->mounts[ $mount ] = $config;
	}

	/**
	 * Declared mounts (selector → config).
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function mounts(): array {
		return $this->mounts;
	}

	/**
	 * Whether any canvas mount is declared.
	 *
	 * @return bool
	 */
	public function is_empty(): bool {
		return array() === $this->mounts;
	}

	/**
	 * Serialized mounts for the JS runtime.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function to_array(): array {
		return $this->mounts;
	}
}
