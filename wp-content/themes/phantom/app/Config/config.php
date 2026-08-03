<?php
/**
 * Phantom Core — default configuration.
 *
 * Phase 1 (Bootstrap): safe, documented defaults for every subsystem. Values
 * here may be overridden per environment via phantom.env.json (ADR-011) —
 * never edit this file for local/one-off changes, and never place secrets here.
 *
 * @package Phantom\Core\Config
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
		// Phantom Core runtime — always on once booted.
		'phantom_core'   => true,
		// Asset pipeline CSS/JS — Phase 7.
		'asset_pipeline' => false,
		// Animation engine (GSAP/Lenis/Three.js) — Phase 10.
		'animation'      => false,
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
);
