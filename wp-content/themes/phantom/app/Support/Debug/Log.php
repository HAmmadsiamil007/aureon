<?php
/**
 * Log — static facade over Loggers.
 *
 * Phase 1 (Bootstrap): exposes PSR-3-style convenience methods. The underlying
 * Loggers instance is set once by the Kernel during bootstrap; before that the
 * facade is a safe no-op, so logging never fatals outside a booted context.
 *
 * @package Phantom\Core\Support\Debug
 * @since 0.1.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Support\Debug;

/**
 * Log facade.
 */
final class Log {

	/**
	 * Active writer.
	 *
	 * @var Loggers|null
	 */
	private static ?Loggers $writer = null;

	/**
	 * Bind the active writer (called by the Kernel).
	 *
	 * @param Loggers|null $writer Logger instance.
	 * @return void
	 *
	 * @internal Framework bootstrap only.
	 */
	public static function set_writer( ?Loggers $writer ): void {
		self::$writer = $writer;
	}

	/**
	 * Log a record at any level.
	 *
	 * @param string               $level   PSR-3 level name.
	 * @param string               $message Message.
	 * @param array<string, mixed> $context Context values.
	 * @return void
	 */
	public static function log( string $level, string $message, array $context = array() ): void {
		self::$writer?->dispatch( $level, $message, $context );
	}

	/**
	 * Log at debug level.
	 *
	 * @param string               $message Message.
	 * @param array<string, mixed> $context Context values.
	 * @return void
	 */
	public static function debug( string $message, array $context = array() ): void {
		self::log( 'debug', $message, $context );
	}

	/**
	 * Log at info level.
	 *
	 * @param string               $message Message.
	 * @param array<string, mixed> $context Context values.
	 * @return void
	 */
	public static function info( string $message, array $context = array() ): void {
		self::log( 'info', $message, $context );
	}

	/**
	 * Log at notice level.
	 *
	 * @param string               $message Message.
	 * @param array<string, mixed> $context Context values.
	 * @return void
	 */
	public static function notice( string $message, array $context = array() ): void {
		self::log( 'notice', $message, $context );
	}

	/**
	 * Log at warning level.
	 *
	 * @param string               $message Message.
	 * @param array<string, mixed> $context Context values.
	 * @return void
	 */
	public static function warning( string $message, array $context = array() ): void {
		self::log( 'warning', $message, $context );
	}

	/**
	 * Log at error level.
	 *
	 * @param string               $message Message.
	 * @param array<string, mixed> $context Context values.
	 * @return void
	 */
	public static function error( string $message, array $context = array() ): void {
		self::log( 'error', $message, $context );
	}

	/**
	 * Log at critical level.
	 *
	 * @param string               $message Message.
	 * @param array<string, mixed> $context Context values.
	 * @return void
	 */
	public static function critical( string $message, array $context = array() ): void {
		self::log( 'critical', $message, $context );
	}
}
