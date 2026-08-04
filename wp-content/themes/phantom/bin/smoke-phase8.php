<?php
/**
 * Phase 8 smoke suite — Plugin Bridges.
 *
 * WP-free. Boots the container, resolves the bridge registry/manager, and
 * asserts the canonical surface: 12 bridges registered, lazy resolution,
 * capability matrix parity, health checks, and WP-free safety (absent plugins
 * never throw).
 *
 * Usage: php bin/smoke-phase8.php
 *
 * @package Phantom\Core\Smoke
 * @since 0.8.0
 */

declare( strict_types=1 );

use Phantom\Core\Bridges\BridgeInterface;
use Phantom\Core\Bridges\BridgeManager;
use Phantom\Core\Bridges\FeatureMatrix;
use Phantom\Core\Bridges\HealthCheck;
use Phantom\Core\Bridges\Registry;
use Phantom\Core\Boot\Kernel;
use Phantom\Core\Core\App;

require __DIR__ . '/../vendor/autoload.php';

$failures = 0;
$total    = 0;

/**
 * Record a check result.
 *
 * @param string $name    Check name.
 * @param bool   $ok      Whether the check passed.
 * @param string $details Optional details on failure.
 */
function check( string $name, bool $ok, string $details = '' ): void {
	global $failures, $total;

	++$total;

	if ( ! $ok ) {
		++$failures;
		echo 'FAIL  ' . $name . ( '' !== $details ? ' — ' . $details : '' ) . PHP_EOL;
	}
}

Kernel::launch();
$app = App::instance();

// 1. Feature flag present and enabled.
$features = $app->make( 'config' )->get( 'features', array() );
check( 'plugin_bridges feature is enabled', true === ( $features['plugin_bridges'] ?? false ) );

// 2. Registry resolves from the container.
$registry = $app->make( 'bridges.registry' );
check( 'bridges.registry resolves', $registry instanceof Registry );

// 3. Canonical bridge count.
check( '12 bridges registered', 12 === count( $registry->slugs() ), 'got ' . count( $registry->slugs() ) );

// 4. Expected slugs present.
$expected = array( 'acf', 'rankmath', 'yoast', 'wpml', 'polylang', 'fluentforms', 'gravity', 'wpforms', 'buddypress', 'bbpress', 'learndash', 'tec' );
$missing  = array_diff( $expected, $registry->slugs() );
check( 'all canonical slugs registered', array() === $missing, 'missing: ' . implode( ', ', $missing ) );

// 5. Lazy resolution — nothing resolved before first get().
$resolved_before = 0;

foreach ( $expected as $slug ) {
	if ( $registry->is_resolved( $slug ) ) {
		++$resolved_before;
	}
}

check( 'registry is lazy (no bridge resolved pre-request)', 0 === $resolved_before, "$resolved_before resolved" );

// 6. Each bridge resolves to a BridgeInterface with a matching slug.
$slugs_ok = true;

foreach ( $expected as $slug ) {
	$bridge = $registry->get( $slug );

	if ( ! ( $bridge instanceof BridgeInterface ) || $bridge->slug() !== $slug ) {
		$slugs_ok = false;
		break;
	}
}

check( 'every bridge resolves and reports its slug', $slugs_ok );

// 7. WP-free safety — inactive bridges return safe defaults, never throw.
$safe = true;

foreach ( $registry->slugs() as $slug ) {
	if ( $registry->get( $slug )->is_active() ) {
		$safe = false; // Plugin absent in WP-free CLI — must be inactive.
		break;
	}
}

check( 'all bridges inactive in WP-free context', $safe );

// 8. Capabilities are non-empty for every bridge.
$caps_ok = true;

foreach ( $registry->slugs() as $slug ) {
	if ( array() === $registry->get( $slug )->capabilities() ) {
		$caps_ok = false;
		break;
	}
}

check( 'every bridge declares capabilities', $caps_ok );

// 9. supports() reflects capabilities().
$supports_ok = true;

foreach ( $registry->slugs() as $slug ) {
	$bridge = $registry->get( $slug );
	foreach ( $bridge->capabilities() as $capability ) {
		if ( ! $bridge->supports( $capability ) ) {
			$supports_ok = false;
			break 2;
		}
	}

	if ( $bridge->supports( 'no_such_capability' ) ) {
		$supports_ok = false;
		break;
	}
}

check( 'supports() matches declared capabilities', $supports_ok );

// 10. BridgeManager facade.
$manager = $app->make( 'bridges.manager' );
check( 'bridges.manager resolves', $manager instanceof BridgeManager );
check( 'manager->get returns a bridge', $manager->get( 'acf' ) instanceof BridgeInterface );
check( 'manager->get unknown slug returns null', null === $manager->get( 'nope' ) );
check( 'manager->all returns 12 bridges', 12 === count( $manager->all() ) );
check( 'manager->active() empty in WP-free context', array() === $manager->active() );
check( 'manager->is_active false', false === $manager->is_active( 'acf' ) );
check( 'manager->supports true for declared cap', true === $manager->supports( 'acf', 'repeater' ) );
check( 'manager->supports false for unknown cap', false === $manager->supports( 'acf', 'nope' ) );

// 11. FeatureMatrix parity with registry slugs.
$matrix = $app->make( 'bridges.matrix' );
check( 'bridges.matrix resolves', $matrix instanceof FeatureMatrix );
check( 'matrix has 12 entries', 12 === count( $matrix->slugs() ), 'got ' . count( $matrix->slugs() ) );

$matrix_missing = array_diff( $expected, $matrix->slugs() );
check( 'matrix covers all canonical slugs', array() === $matrix_missing );

$parity = true;

foreach ( $expected as $slug ) {
	$bridge  = $registry->get( $slug );
	$defined = $matrix->definition( $slug );

	if ( null === $defined ) {
		$parity = false;
		break;
	}

	$declared = $defined['capabilities'] ?? array();

	if ( ! is_array( $declared ) || array_diff( $bridge->capabilities(), $declared ) !== array() ) {
		$parity = false;
		break;
	}
}

check( 'matrix capabilities match bridge declarations', $parity );

// 12. HealthCheck.
$health = $app->make( 'bridges.health' );
check( 'bridges.health resolves', $health instanceof HealthCheck );
check( 'health->active false in WP-free context', false === $health->active( 'acf/acf.php' ) );
check( 'health->version empty in WP-free context', '' === $health->version( 'acf/acf.php' ) );
check( 'health->passes false when inactive', false === $health->passes( 'acf/acf.php', '6.0' ) );

// 13. Bridge version() is '' in WP-free context (no fatal on missing constant).
$versions_ok = true;

foreach ( $registry->slugs() as $slug ) {
	if ( ! is_string( $registry->get( $slug )->version() ) ) {
		$versions_ok = false;
		break;
	}
}

check( 'bridge version() is string-safe', $versions_ok );

// 14. Adapter capability methods return safe defaults when plugin absent.
$acf = $registry->get( 'acf' );
check( 'acf->fields() returns array', is_array( $acf->fields( 1 ) ) );
check( 'acf->field() returns null', null === $acf->field( 'name', 1 ) );
check( 'acf->sub_fields() returns array', is_array( $acf->sub_fields( 'group', 1 ) ) );

echo 'Results: ' . ( $total - $failures ) . '/' . $total . ' checks passed.' . PHP_EOL;

if ( 0 !== $failures ) {
	echo 'PHASE 8 SMOKE: ' . $failures . ' FAILURE(S).' . PHP_EOL;
	exit( 1 );
}

echo 'PHASE 8 SMOKE: PASS' . PHP_EOL;
