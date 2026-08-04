<?php
/**
 * BridgeInterface — the capability-adapter contract (ADR-007).
 *
 * Phase 8 (Plugin Bridges): every third-party plugin is reached exclusively
 * through a `Lumina\Core\Bridges\*` facade. Bridges are thin, isolated
 * adapters: they detect presence/version, expose a capability surface, and
 * never call a vendor symbol unguarded (function_exists/class_exists first).
 * When a plugin is absent, the bridge reports inactive and its capability
 * methods return safe defaults — Lumina never throws.
 *
 * @package Lumina\Core\Bridges
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Bridges;

/**
 * Plugin capability adapter.
 */
interface BridgeInterface {

	/**
	 * Canonical bridge slug (e.g. 'acf').
	 *
	 * @return string
	 */
	public function slug(): string;

	/**
	 * Human-readable plugin name.
	 *
	 * @return string
	 */
	public function name(): string;

	/**
	 * Whether the plugin is installed and active.
	 *
	 * @return bool
	 */
	public function is_active(): bool;

	/**
	 * The detected plugin version ('' when absent/unknown).
	 *
	 * @return string
	 */
	public function version(): string;

	/**
	 * The capability names this bridge exposes.
	 *
	 * @return list<string>
	 */
	public function capabilities(): array;

	/**
	 * Whether a capability is supported.
	 *
	 * @param string $capability Capability name.
	 * @return bool
	 */
	public function supports( string $capability ): bool;
}
