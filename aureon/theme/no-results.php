<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="no-results not-found">
	<div class="inside-article">
		<?php
		/**
		 * aureon_before_content hook.
		 *
		 * @since 0.1
		 *
		 * @hooked aureon_featured_page_header_inside_single - 10
		 */
		do_action( 'aureon_before_content' );
		?>

		<header <?php aureon_do_attr( 'entry-header' ); ?>>
			<h1 class="entry-title"><?php _e( 'Nothing Found', 'aureon' ); ?></h1>
		</header>

		<?php
		/**
		 * aureon_after_entry_header hook.
		 *
		 * @since 0.1
		 *
		 * @hooked aureon_post_image - 10
		 */
		do_action( 'aureon_after_entry_header' );
		?>

		<div class="entry-content">

				<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

					<p>
						<?php
						printf(
							/* translators: 1: Admin URL */
							__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'aureon' ),
							esc_url( admin_url( 'post-new.php' ) )
						);
						?>
					</p>

				<?php elseif ( is_search() ) : ?>

					<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'aureon' ); ?></p>
					<?php get_search_form(); ?>

				<?php else : ?>

					<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'aureon' ); ?></p>
					<?php get_search_form(); ?>

				<?php endif; ?>

		</div>

		<?php
		/**
		 * aureon_after_content hook.
		 *
		 * @since 0.1
		 */
		do_action( 'aureon_after_content' );
		?>
	</div>
</div>
