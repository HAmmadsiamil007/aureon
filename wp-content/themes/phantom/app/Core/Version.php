<?php
/**
 * Version metadata for the Phantom Core framework.
 *
 * Phase 0 (Project Foundation) defines the versioning foundation only.
 * Every later phase reads these constants for cache busting, option
 * namespacing, asset handles, and runtime feature gating (ADR-002, ADR-010).
 *
 * @package Phantom\Core\Core
 * @since 0.1.0 (0.2.0: Framework Infrastructure)
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
	public const VERSION = '0.2.0';

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
