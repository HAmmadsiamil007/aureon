<?php
/**
 * Lumina Core — theme presets (design token layers).
 *
 * Phase 3 (Design Token Engine): each preset is a partial token map layered on
 * top of the canonical defaults via Tokens\Preced (default → preset →
 * override). "default" is the empty base; "dark" swaps the color/shadow
 * semantic tokens so a [data-lumina-theme="dark"] block can be rendered.
 *
 * @package Lumina\Core\Tokens\Config
 * @since 0.3.0
 */

declare( strict_types=1 );

return array(
	// Base preset — inherits everything from the canonical defaults.
	'default' => array(),
	// Dark preset — alternate semantic palette (contrast-gated by Invariant).
	'dark'    => array(
		'color'  => array(
			'bg'     => '#111827',
			'fg'     => '#f9fafb',
			'accent' => '#f43f5e',
			'border' => '#374151',
			'muted'  => '#9ca3af',
		),
		'shadow' => array(
			'card' => '0 1px 3px rgba(0,0,0,0.5), 0 1px 2px rgba(0,0,0,0.4)',
			'pop'  => '0 10px 25px rgba(0,0,0,0.6)',
		),
	),
);
