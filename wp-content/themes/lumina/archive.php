<?php
/**
 * Archive — archive WP hierarchy file (standalone theme shell).
 *
 * Phase 16 (Safe Rebranding): original markup, never derived from a parent
 * theme. Delegates the loop to the framework's partial layer.
 *
 * @package Lumina
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="primary" class="lumina-main site-main">
	<?php
	if ( have_posts() ) :
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WP core function output.
		the_archive_title( '<h1 class="lumina-archive__title">', '</h1>' );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WP core function output.
		the_archive_description( '<div class="lumina-archive__description">', '</div>' );

		while ( have_posts() ) :
			the_post();

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- partial HTML is escaped at the leaf via ViewContext helpers.
			echo \Lumina\Core\Templates\View::partial( 'content', array() );
		endwhile;
	else :
		?>
		<section class="lumina-empty">
			<h1 class="lumina-empty__title"><?php esc_html_e( 'Nothing found', 'lumina' ); ?></h1>
		</section>
		<?php
	endif;
	?>
</main>
<?php
get_footer();
