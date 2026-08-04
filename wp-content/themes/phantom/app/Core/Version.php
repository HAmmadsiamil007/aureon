<?php
/**
 * Version metadata for the Phantom Core framework.
 *
 * Phase 0 (Project Foundation) defines the versioning foundation only.
 * Every later phase reads these constants for cache busting, option
 * namespacing, asset handles, and runtime feature gating (ADR-002, ADR-010).
 *
 * @package Phantom\Core\Core
 * @since 0.1.0 (0.2.0: Framework Infrastructure; 0.3.0: Design Token Engine;
 * 0.4.0: Render Engine; 0.5.0: Component Registry; 0.6.0: Template System;
 * 0.7.0: Asset Pipeline; 0.8.0: Plugin Bridges; 0.9.0: WooCommerce Bridge;
 * 0.10.0: Animation Engine; 0.11.0: Frontend Component Library;
 * 0.12.0: Frontend Template Library; 0.13.0: Performance
 * Engineering; 0.14.0: Accessibility Engineering)
 */

declare( strict_types=1 );

namespace Phantom\Core\Core;

/**
 * Immutable version constants for Phantom Core.
 *
 * SemVer policy: 0.x (see docs/versions.md) — pre-1.0, any change may be a
 * breaking change; API_LEVEL bumps on incompatible public-API changes.
 */
final class Version {

	/**
	 * Framework version string.
	 *
	 * @var string
	 */
	public const VERSION = '0.14.0';

	/**
	 * Framework API level — bumped whenever a public API breaks compatibility.
	 *
	 * @var int
	 */
	public const API_LEVEL = 1;

	/**
	 * Minimum supported WordPress version.
	 *
	 * @var string
	 */
	public const WP_MIN = '6.5';

	/**
	 * Minimum supported PHP version.
	 *
	 * @var string
	 */
	public const PHP_MIN = '8.2';

	/**
	 * Namespace root for all Phantom Core code (ADR-002).
	 *
	 * @var string
	 */
	public const NAMESPACE_ROOT = 'Phantom\\Core\\';

	/**
	 * Global function / option / hook prefix (ADR-002).
	 *
	 * @var string
	 */
	public const PREFIX = 'phantom_';

	/**
	 * Asset handle prefix (ADR-002).
	 *
	 * @var string
	 */
	public const HANDLE_PREFIX = 'phantom-';
}
