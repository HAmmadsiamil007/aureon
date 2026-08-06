<?php
/**
 * Vendor Library Enqueue.
 *
 * Enqueues Vanta.js, GSAP, and Lenis from local vendor directory.
 * Only loads on front-end pages.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'aureon_enqueue_vendor_libraries', 5 );
/**
 * Enqueue vendor libraries.
 *
 * Priority 5 ensures these load before AETHER assets (priority 99).
 */
function aureon_enqueue_vendor_libraries() {
	if ( is_customize_preview() ) {
		return;
	}

	$vendor_uri = get_template_directory_uri() . '/assets/vendor';
	$version    = '1.0.0';

	// ─── Three.js (required by Vanta.js) ──
	wp_enqueue_script(
		'aureon-threejs',
		'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js',
		array(),
		'0.128.0',
		true
	);

	// ─── Vanta.js ──
	$vanta_effects = array(
		'net',
		'globe',
		'halo',
		'waves',
		'birds',
		'cells',
		'dots',
		'fog',
		'rings',
		'ripple',
		'topology',
		'trunk',
		'clouds',
		'clouds2',
	);

	foreach ( $vanta_effects as $effect ) {
		$handle = 'aureon-vanta-' . $effect;
		wp_enqueue_script(
			$handle,
			$vendor_uri . '/vanta/vanta.' . $effect . '.min.js',
			array( 'aureon-threejs' ),
			$version,
			true
		);
	}

	// ─── GSAP Core ──
	wp_enqueue_script(
		'aureon-gsap',
		$vendor_uri . '/gsap/gsap.min.js',
		array(),
		$version,
		true
	);

	// ─── GSAP Plugins ──
	$gsap_plugins = array(
		'ScrollTrigger'     => 'ScrollTrigger',
		'ScrollToPlugin'    => 'ScrollToPlugin',
		'Observer'          => 'Observer',
		'Draggable'         => 'Draggable',
		'Flip'              => 'Flip',
		'TextPlugin'        => 'TextPlugin',
		'EasePack'          => 'EasePack',
		'MotionPathPlugin'  => 'MotionPathPlugin',
	);

	foreach ( $gsap_plugins as $name => $file ) {
		wp_enqueue_script(
			'aureon-gsap-' . $name,
			$vendor_uri . '/gsap/' . $file . '.min.js',
			array( 'aureon-gsap' ),
			$version,
			true
		);
	}

	// ─── Lenis Smooth Scroll ──
	wp_enqueue_style(
		'aureon-lenis-css',
		$vendor_uri . '/lenis/lenis.css',
		array(),
		$version,
		'all'
	);

	wp_enqueue_script(
		'aureon-lenis',
		$vendor_uri . '/lenis/lenis.min.js',
		array(),
		$version,
		true
	);
}
