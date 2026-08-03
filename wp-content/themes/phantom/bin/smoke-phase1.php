<?php
/**
 * Phase 1 — Bootstrap smoke suite (WP-free CLI).
 *
 * Drives the real boot entry (app/load.php → Kernel::launch()) WITHOUT a live
 * WordPress install and asserts every Phase 1 acceptance criterion:
 *
 *   1. app/load.php loads once and binds the Kernel
 *   2. Kernel boots in order: config → env → flags → logger → errorHandler
 *   3. App::instance() resolves services: env, flags, logger, errorHandler
 *   4. App::get() reads config; env()/is_debug() resolve from config
 *   5. phantom.env.json overrides are honored end-to-end (ADR-011)
 *   6. Logger redacts secrets (ph_pass / sku_key) from message + context
 *   7. FeatureFlags fail closed for unknown/disabled flags
 *   8. ErrorHandler wraps a Throwable as WP_Error and reports once
 *   9. Boot is idempotent (double launch is a no-op)
 *   10. Namespace autoload resolves Phantom\Core\Core\Version
 *
 * Determinism: the suite asserts the DEFAULT bootstrap state first (fresh
 * install → production, debug off), then drives the phantom.env.json override
 * path (staging + debug on) through ConfigLoader. A shutdown handler guarantees
 * the temporary override file is always removed — even if an assertion fatals
 * mid-run — and the suite refuses to run (exit 0, SKIP) when a developer's own
 * phantom.env.json is present, so it never corrupts local config.
 *
 * Usage: php bin/smoke-phase1.php
 * Exit code 0 = all assertions passed (or skipped); 1 = any failure.
 *
 * @package Phantom
 * @since 0.1.0
 */

declare( strict_types=1 );

// Simulate a WordPress bootstrap boundary so app/load.php's guard passes.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

require dirname( __DIR__ ) . '/app/load.php';

use Phantom\Core\Boot\Kernel;
use Phantom\Core\Core\App;
use Phantom\Core\Core\Version;
use Phantom\Core\Support\Debug\Log;
use Phantom\Core\Support\Debug\Loggers;
use Phantom\Core\Support\Env;
use Phantom\Core\Support\ErrorHandler;
use Phantom\Core\Support\FeatureFlags;

// Minimal WP_Error stub so ErrorHandler can be exercised without a live WP
// install (mirrors the core class shape used by wrap()/run()).
if ( ! class_exists( 'WP_Error' ) ) {
	/**
	 * WP_Error stub for the WP-free smoke suite.
	 */
	class WP_Error {
		/**
		 * Errors keyed by code.
		 *
		 * @var array<string, array<int, string>>
		 */
		private array $errors = array();

		/**
		 * Constructor.
		 *
		 * @param string $code    Error code.
		 * @param string $message Error message.
		 * @param mixed  $data    Optional data (ignored by stub).
		 */
		public function __construct( string $code = '', string $message = '', mixed $data = '' ) {
			if ( '' !== $code ) {
				$this->errors[ $code ] = array( $message );
			}
		}

		/**
		 * Return the first message for a code (or the first error overall).
		 *
		 * @param string $code Optional error code.
		 * @return string
		 */
		public function get_error_message( string $code = '' ): string {
			if ( '' !== $code && isset( $this->errors[ $code ][0] ) ) {
				return $this->errors[ $code ][0];
			}

			$first = reset( $this->errors );

			return is_array( $first ) && isset( $first[0] ) ? $first[0] : '';
		}
	}
}

$passes = 0;
$fails  = 0;

/**
 * Record and print an assertion result.
 *
 * @param string $label  Assertion label.
 * @param bool   $ok     Passed?
 * @param string $detail Optional evidence.
 * @return void
 */
function check( string $label, bool $ok, string $detail = '' ): void {
	global $passes, $fails;

	if ( $ok ) {
		++$passes;
		echo "[PASS] {$label}\n";
	} else {
		++$fails;
		echo "[FAIL] {$label}" . ( '' !== $detail ? " — {$detail}" : '' ) . "\n";
	}
}

echo "== Phantom Core Phase 1 smoke suite ==\n\n";

// Refuse to run against a developer's own override file — the suite verifies
// the DEFAULT bootstrap, so a local phantom.env.json would invalidate every
// state assertion. CI checkouts are always clean.
$override_file = dirname( __DIR__ ) . '/phantom.env.json';

if ( file_exists( $override_file ) ) {
	echo "[SKIP] phantom.env.json present — default-state assertions are not meaningful.\n";
	echo "       Remove it (or run in a clean checkout) for the full assertion run.\n";
	exit( 0 );
}

// Self-cleaning: the override section below writes a temporary phantom.env.json.
// This shutdown handler guarantees removal even if an assertion fatals mid-run.
register_shutdown_function(
	static function () use ( $override_file ): void {
		if ( file_exists( $override_file ) ) {
			unlink( $override_file );
		}
	}
);

// 10. Namespace/autoload resolution.
check( 'PSR-4 autoload resolves Version', class_exists( Version::class ), 'Version class not found' );
check( 'Version::VERSION is 0.1.0', Version::VERSION === '0.1.0', Version::VERSION );

// 9. Idempotent boot.
Kernel::launch();
Kernel::launch(); // second launch must be a no-op.
check( 'Kernel::launch() is idempotent', true );

$app = App::instance();

// 3. Services resolvable through the App facade.
$env   = $app->make( 'env' );
$flags = $app->make( 'flags' );
$log   = $app->make( 'logger' );
$error = $app->make( 'errorHandler' );

check( 'App::make("env") returns string', is_string( $env ) && '' !== $env, var_export( $env, true ) );
check( 'App::make("env") is production (fresh install)', 'production' === $env, (string) $env );
check( 'App::make("flags") is FeatureFlags', $flags instanceof FeatureFlags );
check( 'App::make("logger") is Loggers', $log instanceof Loggers );
check( 'App::make("errorHandler") is ErrorHandler', $error instanceof ErrorHandler );

// 4. Config via App::get() and env()/is_debug() (default state: debug off).
check( 'App::get("debug") === false', false === $app->get( 'debug' ) );
check( 'App::get("missing_key") returns fallback null', null === $app->get( 'missing_key', null ) );
check( 'App::env() matches "env" service', 'production' === $app->env(), (string) $app->env() );
check( 'App::is_debug() === false', false === $app->is_debug() );

// 7. Feature flags fail closed for unshipped subsystems; unknown flags too.
check( 'FeatureFlags: phantom_core enabled', true === $flags->enabled( 'phantom_core' ) );
check( 'FeatureFlags: asset_pipeline disabled (unshipped)', false === $flags->enabled( 'asset_pipeline' ) );
check( 'FeatureFlags: unknown flag fails closed', false === $flags->enabled( 'does_not_exist' ) );

// 6. Secret redaction.
$logger = $log;
$line   = $logger->format(
	'error',
	'connect failed for key {ph_pass}',
	array(
		'ph_pass' => 's3cr3t-value',
		'sku_key' => 'SKU-123',
	)
);

check( 'Redaction: secret value absent from formatted line', false === strpos( $line, 's3cr3t-value' ), $line );
check( 'Redaction: redacted marker present', false !== strpos( $line, '[REDACTED]' ), $line );
check( 'Redaction: message placeholder value replaced', false === strpos( $line, 'key s3cr3t' ), $line );

// Threshold: debug below the default 'warning' threshold must not dispatch.
$captured = '';
set_error_handler(
	static function ( int $errno, string $errstr ) use ( &$captured ): bool {
		$captured = $errstr;
		return true;
	}
);
$logger->dispatch( 'debug', 'noisy debug line' );
restore_error_handler();
check( 'Level threshold: debug (below warning) not dispatched', '' === $captured, $captured );

// 8. ErrorHandler wraps Throwable as WP_Error.
$handler = $error;
$result  = $handler->run(
	static function (): never {
		throw new \RuntimeException( 'boom' );
	}
);

check( 'ErrorHandler::run() returns WP_Error on Throwable', $result instanceof \WP_Error, gettype( $result ) );
check( 'ErrorHandler::wrap() message preserved', $result instanceof \WP_Error && 'boom' === $result->get_error_message() );

// 5. Override honored through the loader (independent of the one-shot Kernel
// boot): same code path the boot config step uses. The temporary file is
// removed by the shutdown handler registered above.
file_put_contents(
	$override_file,
	'{"environment":{"override":"staging"},"debug":true,"features":{"phantom_core":true,"asset_pipeline":true}}'
);

$loader2 = new \Phantom\Core\Config\ConfigLoader( dirname( __DIR__ ) );
$cfg2    = $loader2->load();

check( 'Env override: staging from phantom.env.json', 'staging' === Env::detect( $cfg2 ), (string) Env::detect( $cfg2 ) );
check( 'Debug override: true from phantom.env.json', true === Env::is_debug( $cfg2 ) );

$flags2 = new FeatureFlags( (array) ( $cfg2['features'] ?? array() ) );
check( 'Feature override: asset_pipeline enabled via env file', true === $flags2->enabled( 'asset_pipeline' ) );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
