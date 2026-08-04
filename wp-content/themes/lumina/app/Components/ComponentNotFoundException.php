<?php
/**
 * ComponentNotFoundException — thrown when rendering an unregistered component.
 *
 * Phase 5 (Component Registry): a deterministic failure for `render()` calls
 * against names that were never registered (or failed discovery). Callers at
 * the WordPress surface should prefer has()/get() and fall back to a default
 * block instead of letting this escape (plan §Phase 4 error-handling rule).
 *
 * @package Lumina\Core\Components
 * @since 0.5.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Components;

/**
 * Unknown component name.
 */
class ComponentNotFoundException extends ComponentException {}
