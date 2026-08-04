<?php
/**
 * BridgeManager — public facade over the bridge registry.
 *
 * Phase 8 (Plugin Bridges): the only surface consumers touch
 * (plan §Phase 8):
 *
 *   BridgeManager::get(string $slug): ?BridgeInterface
 *   BridgeManager::all(): list<BridgeInterface>
 *
 * Plus convenience: `active()`, `is_active($slug)`, `supports($slug, $cap)`.
 * Bridge resolution is lazy; absent plugins simply resolve to inactive
 * bridges and Phantom never throws.
 *
 * @package Phantom\Core\Bridges
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Bridges;

/**
 * Public bridge access.
 */
class BridgeManager {

	/**
	 * Bridge registry.
	 *
	 * @var Registry
	 */
	private Registry $registry;

	/**
	 * Build the manager.
	 *
	 * @param Registry $registry Bridge registry.
	 */
	public function __construct( Registry $registry ) {
		$this->registry = $registry;
	}

	/**
	 * Resolve a bridge by slug.
	 *
	 * @param string $slug Bridge slug.
	 * @return BridgeInterface|null
	 */
	public function get( string $slug ): ?BridgeInterface {
		return $this->registry->get( $slug );
	}

	/**
	 * All registered bridges, resolved.
	 *
	 * @return list<BridgeInterface>
	 */
	public function all(): array {
		$bridges = array();

		foreach ( $this->registry->slugs() as $slug ) {
			$bridge = $this->registry->get( $slug );

			if ( null !== $bridge ) {
				$bridges[] = $bridge;
			}
		}

		return $bridges;
	}

	/**
	 * The bridges whose plugins are active.
	 *
	 * @return list<BridgeInterface>
	 */
	public function active(): array {
		$active = array();

		foreach ( $this->all() as $bridge ) {
			if ( $bridge->is_active() ) {
				$active[] = $bridge;
			}
		}

		return $active;
	}

	/**
	 * Whether a bridge's plugin is active.
	 *
	 * @param string $slug Bridge slug.
	 * @return bool
	 */
	public function is_active( string $slug ): bool {
		$bridge = $this->get( $slug );

		return null !== $bridge && $bridge->is_active();
	}

	/**
	 * Whether a bridge supports a capability.
	 *
	 * @param string $slug       Bridge slug.
	 * @param string $capability Capability name.
	 * @return bool
	 */
	public function supports( string $slug, string $capability ): bool {
		$bridge = $this->get( $slug );

		return null !== $bridge && $bridge->supports( $capability );
	}
}
