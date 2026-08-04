<?php
/**
 * Comments — comments WP hierarchy file (standalone theme shell).
 *
 * Phase 16 (Safe Rebranding): original markup, never derived from a parent
 * theme. Delegates to the public comments_template() API when a password or
 * post with closed comments must render WordPress's default output.
 *
 * @package Lumina
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="lumina-comments">
	<?php
	if ( have_comments() ) :
		?>
		<h2 class="lumina-comments__title">
			<?php
			$lumina_comment_count = (int) get_comments_number();

			printf(
				/* translators: %s: comment count. */
				esc_html( _n( '%s comment', '%s comments', $lumina_comment_count, 'lumina' ) ),
				esc_html( number_format_i18n( $lumina_comment_count ) )
			);
			?>
		</h2>
		<ol class="lumina-comments__list">
			<?php
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
				)
			);
			?>
		</ol>
		<?php
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		the_comments_navigation();
	endif;

	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
	comment_form();
	?>
</div>
