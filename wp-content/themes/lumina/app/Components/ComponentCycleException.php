<?php
/**
 * ComponentCycleException — thrown when the component dependency graph cycles.
 *
 * Phase 5 (Component Registry): dependency validation rejects graphs where a
 * component (transitively) depends on itself. The message carries the first
 * cycle found so the misconfiguration is diagnosable at a glance.
 *
 * @package Lumina\Core\Components
 * @since 0.5.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Components;

/**
 * Dependency cycle detected.
 */
class ComponentCycleException extends ComponentException {}
