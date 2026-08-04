<?php
/**
 * Lumina Core — default configuration.
 *
 * Phase 1 (Bootstrap): safe, documented defaults for every subsystem. Values
 * here may be overridden per environment via lumina.env.json (ADR-011) —
 * never edit this file for local/one-off changes, and never place secrets here.
 *
 * @package Lumina\Core\Config
 * @since 0.1.0
 */

declare( strict_types=1 );

return array(
	// Environment override base; usually derived from wp_get_environment_type().
	'environment'   => array(
		'override' => null,
	),
	'debug'         => false,
	'features'      => array(
		// Lumina Core runtime — always on once booted.
		'lumina_core'        => true,
		// Render Engine — Phase 4.
		'render_engine'      => true,
		// Component Registry — Phase 5.
		'component_registry' => true,
		// Template System — Phase 6.
		'template_system'    => true,
		// Asset pipeline CSS/JS — Phase 7.
		'asset_pipeline'     => true,
		// Plugin Bridges — Phase 8 (passive capability adapters, no UI).
		'plugin_bridges'     => true,
		// WooCommerce Bridge — Phase 9 (passive; legacy overrides opt-in).
		'woo_bridge'         => true,
		// Animation engine (GSAP/Lenis/Three.js) — Phase 10.
		'animation'          => true,
		// Frontend Component Library — Phase 11 (token-driven, a11y-ready).
		'component_library'  => true,
		// Frontend Template Library — Phase 12 (Composer + maps).
		'template_library'   => true,
		// Performance Engineering — Phase 13 (budgets, query guard, lazy, purge).
		'performance'        => true,
		// Accessibility Engineering — Phase 14 (checker, skip link, dialogs).
		'accessibility'      => true,
	),
	'log'           => array(
		// Minimum level dispatched to the log (PSR-3 names).
		'level'  => 'warning',
		// Keys whose values are redacted from messages/context.
		'redact' => array(
			'ph_pass',
			'sku_key',
		),
	),
	'error_handler' => array(
		// Whether ErrorHandler::register() installs the WP-surface handler.
		'register' => true,
	),
	'render'        => array(
		// Template engine: 'php' (native, zero runtime deps — ADR-009).
		// A Twig engine can be added later behind TemplateEngineInterface.
		'engine'    => 'php',
		// Render result cache (view + data-hash), disabled for logged-in users.
		'cache'     => true,
		// Cache entry lifetime in seconds (1 hour).
		'cache_ttl' => 3600,
	),
	'components'    => array(
		// Extra per-instance components.json discovery paths. When empty the
		// canonical app/Components/config/components.json is used (Phase 5).
		'json_paths' => array(),
	),
	'performance'   => array(
		// Core Web Vitals + payload budgets (Phase 13, plan §Phase 13).
		'budgets'     => array(
			'lcp'       => 2.0,
			'cls'       => 0.05,
			'inp'       => 150.0,
			'js_kb'     => 120,
			'css_kb'    => 50,
			'server_ms' => 300,
			'queries'   => 8,
		),
		// Debug-only query introspection (warns when the query budget breaks).
		'query_guard' => false,
	),
	'assets'        => array(
		// Sources enqueued on wp_enqueue_scripts (Phase 7): css/js lists of
		// asset-src paths resolved through the Vite manifest. Empty = none.
		'enqueue' => array(),
	),
	'providers'     => array(
		// Service providers registered during boot (Phase 2). Subsystems add
		// their provider class here as they ship in later phases.
		\Lumina\Core\Tokens\TokenServiceProvider::class,
		\Lumina\Core\Render\RenderServiceProvider::class,
		\Lumina\Core\Components\ComponentsServiceProvider::class,
		\Lumina\Core\Templates\TemplatesServiceProvider::class,
		\Lumina\Core\Assets\AssetsServiceProvider::class,
		\Lumina\Core\Bridges\BridgesServiceProvider::class,
		\Lumina\Core\Woo\WooServiceProvider::class,
		\Lumina\Core\Animation\AnimationServiceProvider::class,
		\Lumina\Core\Performance\PerformanceServiceProvider::class,
		\Lumina\Core\A11y\A11yServiceProvider::class,
	),
);
