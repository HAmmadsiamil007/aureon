<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
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
					 * aureon_before_loop hook.
					 *
					 * @since 3.1.0
					 */
					do_action( 'aureon_before_loop', 'index' );

					while ( have_posts() ) :

						the_post();

						aureon_do_template_part( 'index' );

					endwhile;

					/**
					 * aureon_after_loop hook.
					 *
					 * @since 2.3
					 */
					do_action( 'aureon_after_loop', 'index' );

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
