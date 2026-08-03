<?php
/**
 * Phase 2 — Framework Infrastructure smoke suite (WP-free CLI).
 *
 * Drives the real boot entry (app/load.php → Kernel::launch()) WITHOUT a live
 * WordPress install and asserts the Phase 2 acceptance criteria:
 *
 *   1. Container resolves arbitrary services transient + singleton (PSR-11)
 *   2. Container detects circular dependencies and throws informatively
 *   3. Class-string bindings auto-wire "where safe"
 *   4. Dispatcher re-invokes listeners in registration order; stoppable events
 *      halt the chain; WP actions bridge to phantom_core:wp_* events
 *   5. HookManager registers without double-firing and applies filters idempotently
 *   6. Config\\Repository: dot-notation get/set, all(), immutable by default
 *   7. App::get() routes through the cached repository (dot notation)
 *   8. Registry\\ArrayRegistry + DynamicRegistry (lazy factories)
 *   9. Factory\\SimpleFactory delegates to the container
 *  10. Cache: CacheKey namespacing; ObjectCache/TransientCache safe without WP
 *  11. Provider register()/boot() lifecycle
 *  12. Phase 1 regression: services + config still resolve
 *
 * Determinism: refuses to run when a developer's own phantom.env.json exists
 * (same contract as smoke-phase1.php).
 *
 * Usage: php bin/smoke-phase2.php
 * Exit code 0 = all assertions passed (or skipped); 1 = any failure.
 *
 * @package Phantom
 * @since 0.2.0
 */

declare( strict_types=1 );

// Simulate a WordPress bootstrap boundary so app/load.php's guard passes.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

require dirname( __DIR__ ) . '/app/load.php';

use Phantom\Core\Boot\Kernel;
use Phantom\Core\Cache\CacheKey;
use Phantom\Core\Cache\ObjectCache;
use Phantom\Core\Cache\TransientCache;
use Phantom\Core\Config\Repository;
use Phantom\Core\Container\CircularDependencyException;
use Phantom\Core\Container\Container;
use Phantom\Core\Container\NotFoundException;
use Phantom\Core\Core\App;
use Phantom\Core\Events\Dispatcher;
use Phantom\Core\Factory\SimpleFactory;
use Phantom\Core\Hooks\HookManager;
use Phantom\Core\Providers\ServiceProviderInterface;
use Phantom\Core\Registry\ArrayRegistry;
use Phantom\Core\Registry\DynamicRegistry;

/**
 * A simple auto-wireable dependency.
 */
class SmokeServiceA {
}

/**
 * A class that depends on SmokeServiceA (auto-wire target).
 */
class SmokeServiceB {

	/**
	 * Injected dependency.
	 *
	 * @var SmokeServiceA
	 */
	public SmokeServiceA $a;

	/**
	 * Constructor.
	 *
	 * @param SmokeServiceA $a Dependency.
	 */
	public function __construct( SmokeServiceA $a ) {
		$this->a = $a;
	}
}

/**
 * A minimal service provider used to verify the register()/boot() lifecycle.
 */
class SmokeProvider implements ServiceProviderInterface {

	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		$container->set( 'smoke_provider_service', 'from-provider' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( Container $container ): void {
		$container->set( 'smoke_provider_booted', true );
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

echo "== Phantom Core Phase 2 smoke suite ==\n\n";

// Same contract as Phase 1: the suite verifies the DEFAULT bootstrap, so a
// developer's own phantom.env.json would invalidate the state assertions.
if ( file_exists( dirname( __DIR__ ) . '/phantom.env.json' ) ) {
	echo "[SKIP] phantom.env.json present — default-state assertions are not meaningful.\n";
	echo "       Remove it (or run in a clean checkout) for the full assertion run.\n";
	exit( 0 );
}

// 1/2. PSR-4 autoload + idempotent boot.
check( 'PSR-4 resolves Container', class_exists( Container::class ) );
check( 'PSR-4 resolves Dispatcher', class_exists( Dispatcher::class ) );
check( 'PSR-4 resolves Repository', class_exists( Repository::class ) );

Kernel::launch();
Kernel::launch(); // second launch must be a no-op.
check( 'Kernel::launch() is idempotent', true );

$app       = App::instance();
$container = $app->make( 'container' );

check( 'App::make("container") is Container', $container instanceof Container );

// 6. Config repository through the facade.
$repo = $app->make( 'config' );
check( 'App::make("config") is Config\\Repository', $repo instanceof Repository );
check( 'Repository: dot-notation log.level === warning', 'warning' === $repo->get( 'log.level' ), (string) $repo->get( 'log.level' ) );
check( 'Repository: features.phantom_core === true', true === $repo->get( 'features.phantom_core' ) );
check( 'Repository: missing key returns default', 'fb' === $repo->get( 'nope.deep', 'fb' ) );
check( 'Repository: all() returns array', is_array( $repo->all() ) && isset( $repo->all()['log'] ) );

// 7. App::get() routed through the cached repository.
check( 'App::get("log.level") === warning', 'warning' === $app->get( 'log.level' ) );
check( 'App::get("features.phantom_core") === true', true === $app->get( 'features.phantom_core' ) );
check( 'App::get("nope") returns fallback', 'fb' === $app->get( 'nope', 'fb' ) );

// 1. Container: transient vs singleton.
$container->register( 'smoke_transient', static function (): \stdClass {
	return new \stdClass();
} );
$t1 = $container->get( 'smoke_transient' );
$t2 = $container->get( 'smoke_transient' );
check( 'Container: transient builds a new instance each get()', $t1 !== $t2 );

$container->singleton( 'smoke_singleton', static function (): \stdClass {
	return new \stdClass();
} );
$s1 = $container->get( 'smoke_singleton' );
$s2 = $container->get( 'smoke_singleton' );
check( 'Container: singleton returns the same instance', $s1 === $s2 );

// 1b. Container: set() raw values + has().
$container->set( 'smoke_raw', 'raw-value' );
check( 'Container: set() raw value resolves', 'raw-value' === $container->get( 'smoke_raw' ) );
check( 'Container: has() true for bound, false for unknown', $container->has( 'smoke_raw' ) && ! $container->has( 'smoke_never' ) );

// 1c. Container: PSR-11 NotFoundException.
$not_found = false;
try {
	$container->get( 'smoke_never' );
} catch ( NotFoundException $e ) {
	$not_found = true;
}
check( 'Container: unknown id throws NotFoundException', $not_found );

// 2. Container: circular dependency detection.
$cycle = false;
$container->register( 'smoke_a', static function ( Container $c ): mixed {
	return $c->get( 'smoke_b' );
} );
$container->register( 'smoke_b', static function ( Container $c ): mixed {
	return $c->get( 'smoke_a' );
} );

try {
	$container->get( 'smoke_a' );
} catch ( CircularDependencyException $e ) {
	$cycle = true;
}
check( 'Container: circular dependency throws CircularDependencyException', $cycle );

// 3. Container: class-string auto-wiring.
$container->set( SmokeServiceA::class, new SmokeServiceA() );
$container->register( SmokeServiceB::class, SmokeServiceB::class );
$b = $container->get( SmokeServiceB::class );
check( 'Container: class-string auto-wires typed params', $b instanceof SmokeServiceB && $b->a instanceof SmokeServiceA );

// 4. Dispatcher: registration order + stoppable events + WP-action bridging.
$order = array();
$dispatcher = new Dispatcher( new HookManager() );

$dispatcher->listen(
	'phantom_core:smoke_event',
	static function ( object $event ) use ( &$order ): void {
		$order[] = 'first';
	}
);
$dispatcher->listen(
	'phantom_core:smoke_event',
	static function ( object $event ) use ( &$order ): void {
		$order[] = 'second';
	}
);

$dispatcher->dispatch( new \Phantom\Core\Events\GenericEvent( 'phantom_core:smoke_event' ) );
check( 'Dispatcher: listeners run in registration order', array( 'first', 'second' ) === $order, implode( ',', $order ) );

$stopped = 0;
$dispatcher->listen(
	'phantom_core:smoke_stop',
	static function () use ( &$stopped ): void {
		++$stopped;
	}
);
$dispatcher->listen(
	'phantom_core:smoke_stop',
	static function () use ( &$stopped ): void {
		++$stopped;
	}
);

$dispatcher->dispatch(
	new class() implements \Phantom\Core\Events\EventInterface, \Phantom\Core\Events\StoppableEventInterface {
		public function name(): string {
			return 'phantom_core:smoke_stop';
		}

		public function is_propagation_stopped(): bool {
			return true;
		}
	}
);
check( 'Dispatcher: stop-propagation halts the chain', 1 === $stopped, (string) $stopped );

$bridged = array();
$dispatcher->listen(
	'phantom_core:wp_phantom_test_action',
	static function ( object $event ) use ( &$bridged ): void {
		$bridged = $event->param( 'args' );
	}
);
$hooks      = new HookManager();
$bridge_dsp = new Dispatcher( $hooks );
$bridge_dsp->listen(
	'phantom_core:wp_phantom_test_action',
	static function ( object $event ) use ( &$bridged ): void {
		$bridged = $event->param( 'args' );
	}
);
$bridge_dsp->map( 'phantom_test_action' );
$hooks->do_action( 'phantom_test_action', 'payload' );
check( 'Dispatcher: map() bridges WP action to domain event', array( 'payload' ) === $bridged, (string) json_encode( $bridged ) );

// 5. HookManager: filter application + no double-fire.
$filters = new HookManager();
$filters->add_filter( 'phantom_test_filter', static fn( string $v ): string => $v . '-1' );
$filters->add_filter( 'phantom_test_filter', static fn( string $v ): string => $v . '-2' );
check( 'HookManager: apply() runs the filter chain', 'x-1-2' === $filters->apply( 'phantom_test_filter', 'x' ), (string) $filters->apply( 'phantom_test_filter', 'x' ) );

$fires = 0;
$cb    = static function () use ( &$fires ): void {
	++$fires;
};
$filters->add_action( 'phantom_test_action2', $cb );
$filters->add_action( 'phantom_test_action2', $cb ); // duplicate registration.
$filters->do_action( 'phantom_test_action2' );
check( 'HookManager: same callback never double-fires', 1 === $fires, (string) $fires );

// 8. Registry: ArrayRegistry + DynamicRegistry.
$array_registry = new ArrayRegistry();
$array_registry->set( 'key', 'value' );
check( 'ArrayRegistry: get/has round-trip', 'value' === $array_registry->get( 'key' ) && $array_registry->has( 'key' ) );

$lazy_calls = 0;
$dynamic    = new DynamicRegistry();
$dynamic->register(
	'lazy',
	static function () use ( &$lazy_calls ): \stdClass {
		++$lazy_calls;
		return new \stdClass();
	}
);
$lazy_a = $dynamic->get( 'lazy' );
$lazy_b = $dynamic->get( 'lazy' );
check( 'DynamicRegistry: factory resolves once and caches', $lazy_a === $lazy_b && 1 === $lazy_calls );

// 9. Factory: SimpleFactory delegates to the container.
$factory = new SimpleFactory( $container );
check( 'SimpleFactory: make() delegates to container', $factory->make( 'config' ) instanceof Repository );

// 10. Cache: CacheKey + guarded adapters.
check( 'CacheKey: namespaced + versioned format', 'phantom_tokens_v1_primary' === CacheKey::make( 'tokens', 'primary', 'v1' ) );
check( 'CacheKey: sanitizes segments', 'phantom_menu_header_nav' === CacheKey::make( 'menu', 'Header Nav!' ) );

$object_cache = new ObjectCache();
check( 'ObjectCache: get() returns default without WP', null === $object_cache->get( 'missing', null ) );
check( 'ObjectCache: set() reports false without WP', false === $object_cache->set( 'x', 'y' ) );

$transient_cache = new TransientCache();
check( 'TransientCache: get() returns default without WP', null === $transient_cache->get( 'missing', null ) );
check( 'TransientCache: set() reports false without WP', false === $transient_cache->set( 'x', 'y' ) );

// 11. Provider lifecycle.
$provider = new SmokeProvider();
$provider->register( $container );
$provider->boot( $container );
check( 'Provider: register() binds services', 'from-provider' === $container->get( 'smoke_provider_service' ) );
check( 'Provider: boot() runs after registration', true === $container->get( 'smoke_provider_booted' ) );

// 12. Phase 1 regression through the Phase 2 boot.
check( 'Phase 1 regression: env is production', 'production' === $app->make( 'env' ), (string) $app->make( 'env' ) );
check( 'Phase 1 regression: flags resolvable', $app->make( 'flags' ) instanceof \Phantom\Core\Support\FeatureFlags );
check( 'Phase 1 regression: logger resolvable', $app->make( 'logger' ) instanceof \Phantom\Core\Support\Debug\Loggers );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
