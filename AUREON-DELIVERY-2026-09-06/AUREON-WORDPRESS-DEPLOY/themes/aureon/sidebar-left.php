<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div <?php aureon_do_attr( 'left-sidebar' ); ?>>
	<div class="inside-left-sidebar">
		<?php
		/**
		 * aureon_before_left_sidebar_content hook.
		 *
		 * @since 0.1
		 */
		do_action( 'aureon_before_left_sidebar_content' );

		if ( ! dynamic_sidebar( 'sidebar-2' ) ) {
			aureon_do_default_sidebar_widgets( 'left-sidebar' );
		}

		/**
		 * aureon_after_left_sidebar_content hook.
		 *
		 * @since 0.1
		 */
		do_action( 'aureon_after_left_sidebar_content' );
		?>
	</div>
</div>
