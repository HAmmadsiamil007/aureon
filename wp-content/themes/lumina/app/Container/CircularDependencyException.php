<?php
/**
 * CircularDependencyException — thrown when a service resolution cycles.
 *
 * Phase 2 (Framework Infrastructure): the container tracks the in-flight
 * resolution stack and fails fast with an informative message instead of
 * recursing into a stack overflow (plan §Phase 2 risk mitigation).
 *
 * @package Lumina\Core\Container
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Container;

/**
 * Circular service resolution detected.
 */
final class CircularDependencyException extends \LogicException {
}
