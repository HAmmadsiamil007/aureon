<?php
/**
 * NotFoundException — thrown when the container cannot resolve a service.
 *
 * Phase 2 (Framework Infrastructure): mirrors the PSR-11 `get()` contract —
 * resolving an unregistered id is an error, not a silent null.
 *
 * @package Phantom\Core\Container
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Container;

/**
 * Service id not found in the container.
 */
final class NotFoundException extends \InvalidArgumentException {
}
