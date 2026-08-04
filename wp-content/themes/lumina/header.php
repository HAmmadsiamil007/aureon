<?php
/**
 * Header — theme shell (top of document).
 *
 * Phase 16 (Safe Rebranding): the standalone theme shell. Original markup —
 * not derived from any parent theme. Renders the document head through public
 * WordPress APIs (wp_head, body_class, wp_body_open) and the site header
 * region through the framework's composition layer (View::compose('header')).
 * Data for the composition arrives via the `lumina_template_data` filter.
 *
 * @package Lumina
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'lumina-body' ); ?>>
<?php wp_body_open(); ?>
<a class="skip-link lumina-skip-link" href="#primary"><?php esc_html_e( 'Skip to content', 'lumina' ); ?></a>
<div id="page" class="lumina-site">
	<?php
	// Region hooks — public Lumina API surface (Companion plugin fills them).
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
	do_action( 'lumina_before_header' );

	if ( class_exists( \Lumina\Core\Templates\View::class ) ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- composition HTML is escaped at the leaf via ViewContext helpers.
		echo \Lumina\Core\Templates\View::compose( 'header', apply_filters( 'lumina_template_data', array(), 'header' ) );
	}

	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
	do_action( 'lumina_after_header' );
	?>
