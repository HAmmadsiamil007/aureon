<?php
/**
 * DevServer — Vite dev-server detection + URL building.
 *
 * Phase 7 (Asset Pipeline): during local development the asset loader must
 * point at the Vite dev server (HMR) instead of hashed dist files. Detection
 * is env-driven (ADR-011): `PHANTOM_VITE_ACTIVE=1` opts in, and the port is
 * read from `PHANTOM_VITE_PORT` (default 5173). Never guesses based on port
 * probes — no network I/O in the request path.
 *
 * @package Phantom\Core\Assets
 * @since 0.7.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Assets;

/**
 * Dev-server configuration.
 */
class DevServer {

	/**
	 * Default dev-server port (Vite default).
	 *
	 * @var int
	 */
	private const DEFAULT_PORT = 5173;

	/**
	 * Host.
	 *
	 * @var string
	 */
	private string $host;

	/**
	 * Port.
	 *
	 * @var int
	 */
	private int $port;

	/**
	 * Whether the dev server is explicitly enabled.
	 *
	 * @var bool
	 */
	private bool $active;

	/**
	 * Build the dev-server config.
	 *
	 * @param string $host   Host (default 'localhost').
	 * @param int    $port   Port (default 5173).
	 * @param bool   $active Whether the dev server is enabled.
	 */
	public function __construct( string $host = 'localhost', int $port = 5173, bool $active = false ) {
		$this->host   = $host;
		$this->port   = max( 1, $port );
		$this->active = $active;
	}

	/**
	 * Build from environment (ADR-011 overrides).
	 *
	 * @return DevServer
	 */
	public static function from_env(): DevServer {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- getenv() is PHP core.
		$port_raw = getenv( 'PHANTOM_VITE_PORT' );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- getenv() is PHP core.
		$host_raw = getenv( 'PHANTOM_VITE_HOST' );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- getenv() is PHP core.
		$active_raw = getenv( 'PHANTOM_VITE_ACTIVE' );

		$port   = is_string( $port_raw ) && is_numeric( $port_raw ) ? (int) $port_raw : self::DEFAULT_PORT;
		$host   = is_string( $host_raw ) && '' !== trim( $host_raw ) ? trim( $host_raw ) : 'localhost';
		$active = is_string( $active_raw )
			? in_array( strtolower( trim( $active_raw ) ), array( '1', 'true', 'yes', 'on' ), true )
			: false;

		return new self( $host, $port, $active );
	}

	/**
	 * Whether the dev server is active.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return $this->active;
	}

	/**
	 * The configured port.
	 *
	 * @return int
	 */
	public function port(): int {
		return $this->port;
	}

	/**
	 * The configured host.
	 *
	 * @return string
	 */
	public function host(): string {
		return $this->host;
	}

	/**
	 * Dev-server URL for a path.
	 *
	 * @param string $path URL path (leading slash optional).
	 * @return string
	 */
	public function url( string $path ): string {
		$path = '/' . ltrim( $path, '/' );

		return 'http://' . $this->host . ':' . (string) $this->port . $path;
	}
}
