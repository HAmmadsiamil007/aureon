<?php
/**
 * Lumina Core — canonical design token definitions (default layer).
 *
 * Phase 3 (Design Token Engine): the single source of truth for the default
 * token values consumed by TokenSource/TokenRepository. Keys are dot-separated
 * token names (e.g. "color.bg", "space.4", "component.button.bg"); a value may
 * be a scalar (resolved as-is) or a structured entry with an "extends" key that
 * aliases another token (resolved by Tokens\Resolver). This file is the
 * default layer — presets and env overrides layer on top via Preced.
 *
 * Per plan §Phase 3 canonical groups: color, typography, spacing, radius,
 * shadow, motion, layout, grid, breakpoints, z-index, component.
 *
 * @package Lumina\Core\Tokens\Config
 * @since 0.3.0
 */

declare( strict_types=1 );

return array(
	// Color — semantic base palette (default preset; contrast-gated).
	'color'       => array(
		'bg'     => '#ffffff',
		'fg'     => '#1a1a1a',
		'accent' => '#e11d48',
		'border' => '#e5e7eb',
		'muted'  => '#6b7280',
	),
	// Typography — font stacks + type scale / line heights / weights.
	'typography'  => array(
		'font' => array(
			'sans'  => "'Inter', system-ui, sans-serif",
			'serif' => "'Georgia', serif",
			'mono'  => "'JetBrains Mono', monospace",
		),
		'type' => array(
			'size'   => array(
				'sm'   => '0.875rem',
				'base' => '1rem',
				'lg'   => '1.125rem',
				'xl'   => '1.25rem',
				'2xl'  => '1.5rem',
				'3xl'  => '1.875rem',
				'4xl'  => '2.25rem',
			),
			'line'   => array(
				'none'    => '1',
				'snug'    => '1.375',
				'normal'  => '1.5',
				'relaxed' => '1.625',
			),
			'weight' => array(
				'regular'  => '400',
				'medium'   => '500',
				'semibold' => '600',
				'bold'     => '700',
			),
		),
	),
	// Space — 4px-based scale, token name = px value (space.4 = 0.25rem).
	'space'       => array(
		'4'       => '0.25rem',
		'8'       => '0.5rem',
		'12'      => '0.75rem',
		'16'      => '1rem',
		'24'      => '1.5rem',
		'32'      => '2rem',
		'48'      => '3rem',
		'64'      => '4rem',
		'section' => '5rem',
		'gutter'  => '1.5rem',
	),
	// Radius.
	'radius'      => array(
		'sm'   => '0.25rem',
		'md'   => '0.5rem',
		'lg'   => '0.75rem',
		'xl'   => '1rem',
		'pill' => '9999px',
	),
	// Shadow.
	'shadow'      => array(
		'card'       => '0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06)',
		'pop'        => '0 10px 25px rgba(0,0,0,0.15)',
		'focus-ring' => '0 0 0 3px rgba(225,29,72,0.4)',
	),
	// Motion — durations, easings, reduced-motion flag.
	'motion'      => array(
		'duration' => array(
			'fast' => '150ms',
			'slow' => '300ms',
		),
		'ease'     => array(
			'default' => 'cubic-bezier(0.4, 0, 0.2, 1)',
			'in'      => 'cubic-bezier(0.4, 0, 1, 1)',
			'out'     => 'cubic-bezier(0, 0, 0.2, 1)',
		),
		'reduced'  => true,
	),
	// Layout — max width, gutters, stacking rhythm.
	'layout'      => array(
		'max'    => '1200px',
		'gutter' => '1.5rem',
		'stack'  => '1.5rem',
	),
	// Grid.
	'grid'        => array(
		'cols' => '12',
		'gap'  => '1.5rem',
	),
	// Breakpoints (min-width).
	'breakpoints' => array(
		'sm' => '640px',
		'md' => '768px',
		'lg' => '1024px',
		'xl' => '1280px',
	),
	// Z-index scale.
	'z-index'     => array(
		'header'  => '100',
		'modal'   => '200',
		'tooltip' => '300',
	),
	// Component tokens — alias base tokens via "extends".
	'component'   => array(
		'button' => array(
			'bg'        => array( 'extends' => 'color.accent' ),
			'fg'        => array( 'extends' => 'color.bg' ),
			'radius'    => array( 'extends' => 'radius.md' ),
			'padding-x' => array( 'extends' => 'space.16' ),
			'padding-y' => array( 'extends' => 'space.8' ),
		),
		'card'   => array(
			'bg'     => array( 'extends' => 'color.bg' ),
			'border' => array( 'extends' => 'color.border' ),
			'radius' => array( 'extends' => 'radius.lg' ),
			'shadow' => array( 'extends' => 'shadow.card' ),
		),
	),
);
