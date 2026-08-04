<?php
/**
 * Index — catch-all WP hierarchy file (standalone theme shell).
 *
 * Phase 16 (Safe Rebranding): original markup, never derived from a parent
 * theme. Standard WordPress loop composed through the framework's partial
 * layer; all escaping happens at the leaf via ViewContext helpers.
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
		while ( have_posts() ) :
			the_post();

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- partial HTML is escaped at the leaf via ViewContext helpers.
			echo \Lumina\Core\Templates\View::partial( 'content', array() );
		endwhile;
	else :
		?>
		<section class="lumina-empty">
			<h1 class="lumina-empty__title"><?php esc_html_e( 'Nothing found', 'lumina' ); ?></h1>
			<p class="lumina-empty__text"><?php esc_html_e( 'It looks like nothing was found here. Maybe try a search?', 'lumina' ); ?></p>
		</section>
		<?php
	endif;
	?>
</main>
<?php
get_footer();
