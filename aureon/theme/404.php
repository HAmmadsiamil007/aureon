<?php
/**
 * The template for displaying 404 pages (Not Found).
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

			aureon_do_template_part( '404' );

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
