<?php
/**
 * Loggers — the concrete log writer.
 *
 * Phase 1 (Bootstrap): a small PSR-3-style logger with level thresholding and
 * built-in secret redaction. Messages are formatted with a timestamp and level,
 * then dispatched via error_log() so they land in the WordPress debug.log under
 * WP_DEBUG. Redaction is applied BEFORE output: any context key listed in
 * config `log.redact` (e.g. ph_pass, sku_key) has its value replaced by
 * [REDACTED] in both the message and the context line (ADR-013).
 *
 * @package Phantom\Core\Support\Debug
 * @since 0.1.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Support\Debug;

/**
 * Concrete logger with level filtering and secret redaction.
 */
final class Loggers {

	/**
	 * PSR-3 severity order (higher = more severe).
	 *
	 * @var array<string, int>
	 */
	private const LEVELS = array(
		'debug'     => 100,
		'info'      => 200,
		'notice'    => 250,
		'warning'   => 300,
		'error'     => 400,
		'critical'  => 500,
		'alert'     => 550,
		'emergency' => 600,
	);

	/**
	 * Minimum level that gets dispatched.
	 *
	 * @var int
	 */
	private int $threshold;

	/**
	 * Context keys whose values must be redacted.
	 *
	 * @var string[]
	 */
	private array $redact;

	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $options Config `log` map.
	 */
	public function __construct( array $options = array() ) {
		$this->threshold = self::LEVELS['warning'];

		if ( isset( $options['level'] ) && isset( self::LEVELS[ (string) $options['level'] ] ) ) {
			$this->threshold = self::LEVELS[ (string) $options['level'] ];
		}

		$this->redact = isset( $options['redact'] ) && is_array( $options['redact'] )
			? array_values( array_filter( $options['redact'], 'is_string' ) )
			: array();
	}

	/**
	 * Dispatch a log record when its level passes the threshold.
	 *
	 * @param string               $level   PSR-3 level name.
	 * @param string               $message Message with optional {placeholders}.
	 * @param array<string, mixed> $context Context values (auto-redacted).
	 * @return void
	 */
	public function dispatch( string $level, string $message, array $context = array() ): void {
		$severity = self::LEVELS[ $level ] ?? self::LEVELS['warning'];

		if ( $severity < $this->threshold ) {
			return;
		}

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions -- error_log() is the sanctioned sink for this logger.
		error_log( $this->format( $level, $message, $context ) );
	}

	/**
	 * Format a log line: [ISO8601] LEVEL message {key=value,...}.
	 *
	 * Applies redaction to both the message and the context render.
	 *
	 * @param string               $level   PSR-3 level name.
	 * @param string               $message Message.
	 * @param array<string, mixed> $context Context values.
	 * @return string
	 */
	public function format( string $level, string $message, array $context = array() ): string {
		$message = $this->redact_message( $message, $context );
		$context = $this->redact_context( $context );

		$line = sprintf(
			'[%s] %s %s',
			gmdate( 'c' ),
			strtoupper( $level ),
			$message
		);

		if ( array() !== $context ) {
			$parts = array();

			foreach ( $context as $key => $value ) {
				$parts[] = $key . '=' . ( is_scalar( $value ) ? (string) $value : gettype( $value ) );
			}

			$line .= ' {' . implode( ', ', $parts ) . '}';
		}

		return $line;
	}

	/**
	 * Replace redacted values inside the message text.
	 *
	 * @param string               $message Message.
	 * @param array<string, mixed> $context Context values.
	 * @return string
	 */
	private function redact_message( string $message, array $context ): string {
		foreach ( $this->redact as $key ) {
			if ( isset( $context[ $key ] ) && is_scalar( $context[ $key ] ) ) {
				$message = str_replace( (string) $context[ $key ], '[REDACTED]', $message );
			}
		}

		return $message;
	}

	/**
	 * Redact listed context keys.
	 *
	 * @param array<string, mixed> $context Context values.
	 * @return array<string, mixed>
	 */
	private function redact_context( array $context ): array {
		foreach ( $this->redact as $key ) {
			if ( array_key_exists( $key, $context ) ) {
				$context[ $key ] = '[REDACTED]';
			}
		}

		return $context;
	}
}
