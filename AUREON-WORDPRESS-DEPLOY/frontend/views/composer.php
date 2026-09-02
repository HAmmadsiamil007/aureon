<?php
/**
 * Composer — assembles page structure from components + adapter data.
 *
 * The composer is the only theme-facing entry point. It gathers data via
 * adapters (the only layer allowed to touch WP/WC) and delegates markup
 * to components via aether_render_component().
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Compose the site shell header (preloader → fog → skip-link → page-content →
 * mobile chrome → announcement → header → main wrapper).
 *
 * Called from the theme's header.php. All data flows in through adapters;
 * no WP calls live in this function beyond adapter invocations.
 */
function aether_compose_header() {
	$shell = array(
		'preloader'    => (bool) aureon_get_option( 'aether_preloader_enabled' ),
		'fog'          => (bool) aureon_get_option( 'aether_fog_enabled' ),
		'announcement' => (bool) aureon_get_option( 'aether_announcement_enabled' ),
	);

	// Preloader — first paint.
	if ( $shell['preloader'] ) {
		aether_render_component( 'shell/preloader', aether_adapter_site() );
	}

	// Global cinematic fog.
	if ( $shell['fog'] ) {
		aether_render_component( 'shell/fog' );
	}

	// Accessibility skip link (targets #main opened below).
	aether_render_component( 'shell/skip-link' );

	echo '<div class="page-content">', "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- structural wrapper.

	// Mobile chrome (header + slide-out menu).
	aether_render_component( 'shell/mobile-chrome', aether_adapter_mobile() );

	// Announcement bar.
	if ( $shell['announcement'] ) {
		aether_render_component( 'shell/announcement', aether_adapter_announcement() );
	}

	// Desktop header.
	aether_render_component( 'shell/header', aether_adapter_header() );

	// Skip-link target + main landmark (the theme's content templates render inside).
	echo '<div id="main" tabindex="-1" class="visually-hidden-focusable" style="position:absolute;top:0"></div>', "\n";
	echo '<main id="swup">', "\n";
}

/**
 * Compose the site shell footer (close main → footer → close page-content).
 *
 * Called from the theme's footer.php.
 */
function aether_compose_footer() {
	echo '</main>', "\n";

	aether_render_component( 'shell/footer', aether_adapter_footer() );

	echo '</div><!-- .page-content -->', "\n";
}
