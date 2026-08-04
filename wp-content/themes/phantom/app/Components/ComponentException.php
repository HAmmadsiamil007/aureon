<?php
/**
 * ComponentException — base failure type for the Component Registry.
 *
 * Phase 5 (Component Registry): raised for definition, registration, and
 * dependency errors that are programming mistakes (invalid schema, missing
 * dependency, unknown component). It is never raised for absent data at the
 * WordPress surface; consumers gate with has()/get() first.
 *
 * @package Phantom\Core\Components
 * @since 0.5.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Components;

/**
 * Component subsystem failure.
 */
class ComponentException extends \RuntimeException {}
