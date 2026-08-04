<?php
/**
 * ErrorHandler — WordPress-safe error handling.
 *
 * Phase 1 (Bootstrap): converts Throwables into WP_Error and logs them without
 * ever crashing the WordPress request surface. Registering is idempotent and
 * the boot-error event is emitted at most once per process.
 *
 * @package Lumina\Core\Support
 * @since 0.1.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Support;

use Lumina\Core\Support\Debug\Log;

/**
 * Error handler that wraps failures for the WP surface.
 */
final class ErrorHandler {

	/**
	 * Whether register() already ran.
	 *
	 * @var bool
	 */
	private bool $registered = false;

	/**
	 * Whether the boot-error event was already raised.
	 *
	 * @var bool
	 */
	private bool $reported = false;

	/**
	 * Install the WP-surface handler (idempotent).
	 *
	 * Registers a shutdown handler that surfaces uncaught Throwables as a
	 * single, logged `lumina_core:boot_error` event. Never rethrows.
	 *
	 * @return void
	 */
	public function register(): void {
		if ( $this->registered ) {
			return;
		}

		$this->registered = true;

		if ( function_exists( 'register_shutdown_function' ) ) {
			register_shutdown_function( array( $this, 'on_shutdown' ) );
		}
	}

	/**
	 * Shutdown hook: convert the last error into a reported failure.
	 *
	 * @return void
	 */
	public function on_shutdown(): void {
		$error = error_get_last();

		if ( is_array( $error ) ) {
			$this->report( new \RuntimeException( (string) $error['message'], (int) $error['type'] ) );
		}
	}

	/**
	 * Wrap any Throwable as a WP_Error.
	 *
	 * @param \Throwable $e The exception.
	 * @return \WP_Error
	 */
	public function wrap( \Throwable $e ): \WP_Error {
		return new \WP_Error( 'lumina_error', $e->getMessage() );
	}

	/**
	 * Run a callable inside the error surface; never lets it crash the request.
	 *
	 * @param callable $callback Callable to run inside the error surface.
	 * @return mixed The callable result, or the wrapped WP_Error on failure.
	 */
	public function run( callable $callback ): mixed {
		try {
			return $callback();
		} catch ( \Throwable $e ) {
			$this->report( $e );

			return $this->wrap( $e );
		}
	}

	/**
	 * Log the failure and raise lumina_core:boot_error exactly once.
	 *
	 * @param \Throwable $e The exception.
	 * @return void
	 */
	private function report( \Throwable $e ): void {
		if ( $this->reported ) {
			return;
		}

		$this->reported = true;

		Log::error(
			'Lumina Core failure: {class}: {message}',
			array(
				'class'   => $e::class,
				'message' => $e->getMessage(),
			)
		);

		if ( function_exists( 'do_action' ) ) {
			do_action( 'lumina_core:boot_error', $e );
		}
	}
}
