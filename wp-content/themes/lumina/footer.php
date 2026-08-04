<?php
/**
 * Footer — theme shell (bottom of document).
 *
 * Phase 16 (Safe Rebranding): the standalone theme shell. Original markup —
 * not derived from any parent theme. Renders the site footer region through
 * the framework's composition layer (View::compose('footer')) and closes the
 * document through the public wp_footer() API.
 *
 * @package Lumina
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( class_exists( \Lumina\Core\Templates\View::class ) ) {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- composition HTML is escaped at the leaf via ViewContext helpers.
	echo \Lumina\Core\Templates\View::compose( 'footer', apply_filters( 'lumina_template_data', array(), 'footer' ) );
}

// Region hooks — public Lumina API surface (Companion plugin fills them).
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
do_action( 'lumina_before_footer' );
?>
</div><!-- #page -->
<?php
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
do_action( 'lumina_after_footer' );
wp_footer();
?>
</body>
</html>
