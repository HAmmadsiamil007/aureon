<?php
/**
 * CycleDetector — dependency-graph cycle detection for components.
 *
 * Phase 5 (Component Registry): a depth-first traversal over the component
 * dependency graph that returns every cycle found. The Registry throws
 * ComponentCycleException on the first cycle during resolveDependencies() so
 * misconfigured graphs fail loudly instead of recursing at render time.
 *
 * @package Lumina\Core\Components
 * @since 0.5.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Components;

/**
 * Detects cycles in a name → deps graph.
 */
final class CycleDetector {

	/**
	 * Node state: unvisited.
	 *
	 * @var int
	 */
	private const UNVISITED = 0;

	/**
	 * Node state: on the current DFS stack.
	 *
	 * @var int
	 */
	private const IN_STACK = 1;

	/**
	 * Node state: fully explored (no cycle through it).
	 *
	 * @var int
	 */
	private const DONE = 2;

	/**
	 * Find all cycles in the graph.
	 *
	 * @param array<string, list<string>> $graph Node → dependency names.
	 * @return list<list<string>> Cycles, each a list of node names (start = end).
	 */
	public function find( array $graph ): array {
		$state   = array();
		$stack   = array();
		$cycles  = array();
		$visited = array();

		foreach ( array_keys( $graph ) as $node ) {
			if ( self::DONE === ( $state[ $node ] ?? self::UNVISITED ) ) {
				continue;
			}

			$this->walk( $node, $graph, $state, $stack, $cycles, $visited );
		}

		return $cycles;
	}

	/**
	 * Depth-first walk from a node, recording any cycle closed by it.
	 *
	 * @param string                         $node    Current node.
	 * @param array<string, list<string>>    $graph   Dependency graph.
	 * @param array<string, int>             $state   Node states (by ref).
	 * @param array<int, string>             $stack   Current DFS stack (by ref).
	 * @param array<int, array<int, string>> $cycles  Collected cycles (by ref).
	 * @param array<string, bool>            $visited Emitted-cycle guard (by ref).
	 * @return void
	 */
	private function walk(
		string $node,
		array $graph,
		array &$state,
		array &$stack,
		array &$cycles,
		array &$visited
	): void {
		$state[ $node ] = self::IN_STACK;
		$stack[]        = $node;

		foreach ( $graph[ $node ] ?? array() as $dep ) {
			$dep_state = $state[ $dep ] ?? self::UNVISITED;

			if ( self::IN_STACK === $dep_state ) {
				// A back-edge to a node on the current stack closes a cycle.
				$position = array_search( $dep, $stack, true );

				if ( false !== $position ) {
					$cycle   = array_slice( $stack, (int) $position );
					$cycle[] = $dep;
					$key     = implode( '>', $cycle );

					if ( ! isset( $visited[ $key ] ) ) {
						$visited[ $key ] = true;
						$cycles[]        = $cycle;
					}
				}
			} elseif ( self::UNVISITED === $dep_state ) {
				$this->walk( $dep, $graph, $state, $stack, $cycles, $visited );
			}
		}

		array_pop( $stack );
		$state[ $node ] = self::DONE;
	}
}
