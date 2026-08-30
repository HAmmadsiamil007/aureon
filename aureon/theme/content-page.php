<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php aureon_do_microdata( 'article' ); ?>>
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

		if ( aureon_show_entry_header() ) :
			?>

			<header <?php aureon_do_attr( 'entry-header' ); ?>>
				<?php
				/**
				 * aureon_before_page_title hook.
				 *
				 * @since 2.4
				 */
				do_action( 'aureon_before_page_title' );

				if ( aureon_show_title() ) {
					$params = aureon_get_the_title_parameters();

					the_title( $params['before'], $params['after'] );
				}

				/**
				 * aureon_after_page_title hook.
				 *
				 * @since 2.4
				 */
				do_action( 'aureon_after_page_title' );
				?>
			</header>

			<?php
		endif;

		/**
		 * aureon_after_entry_header hook.
		 *
		 * @since 0.1
		 *
		 * @hooked aureon_post_image - 10
		 */
		do_action( 'aureon_after_entry_header' );

		$itemprop = '';

		if ( 'microdata' === aureon_get_schema_type() ) {
			$itemprop = ' itemprop="text"';
		}
		?>

		<div class="entry-content"<?php echo $itemprop; // phpcs:ignore -- No escaping needed. ?>>
			<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'aureon' ),
					'after'  => '</div>',
				)
			);
			?>
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
</article>
