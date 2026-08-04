<?php
/**
 * Single — minimal structural template for the Phase 6 Template System.
 *
 * Composes the page from public WordPress APIs (get_header,
 * get_footer, comments_template) and the Lumina framework (View::partial for
 * content composition, View::section for third-party regions). No page design,
 * no marketing layout, no demo content — structure only (plan §Phase 6).
 *
 * @package Lumina\Core\Templates
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="primary" class="lumina-main site-main">
	<?php
	while ( have_posts() ) :
		the_post();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- partial HTML is escaped at the leaf via ViewContext helpers.
		echo \Lumina\Core\Templates\View::partial( 'content-single', array() );

		if ( function_exists( 'comments_template' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			comments_template();
		}
	endwhile;

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- section HTML is escaped at the leaf via the render pipeline.
	echo \Lumina\Core\Templates\View::section( 'after-main' );
	?>
</main>
<?php
get_footer();
