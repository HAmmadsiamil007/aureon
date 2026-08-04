<?php
/**
 * The template for displaying Archive pages.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

	<div <?php aureon_do_attr( 'content' ); ?>>
		<main <?php aureon_do_attr( 'main' ); ?>>
			<?php
			/**
			 * aureon_before_main_content hook.
			 *
			 * @since 0.1
			 */
			do_action( 'aureon_before_main_content' );

			if ( aureon_has_default_loop() ) {
				if ( have_posts() ) :

					/**
					 * aureon_archive_title hook.
					 *
					 * @since 0.1
					 *
					 * @hooked aureon_archive_title - 10
					 */
					do_action( 'aureon_archive_title' );

					/**
					 * aureon_before_loop hook.
					 *
					 * @since 3.1.0
					 */
					do_action( 'aureon_before_loop', 'archive' );

					while ( have_posts() ) :

						the_post();

						aureon_do_template_part( 'archive' );

					endwhile;

					/**
					 * aureon_after_loop hook.
					 *
					 * @since 2.3
					 */
					do_action( 'aureon_after_loop', 'archive' );

				else :

					aureon_do_template_part( 'none' );

				endif;
			}

			/**
			 * aureon_after_main_content hook.
			 *
			 * @since 0.1
			 */
			do_action( 'aureon_after_main_content' );
			?>
		</main>
	</div>

	<?php
	/**
	 * aureon_after_primary_content_area hook.
	 *
	 * @since 2.0
	 */
	do_action( 'aureon_after_primary_content_area' );

	aureon_construct_sidebars();

	get_footer();
