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
<div <?php aureon_do_attr( 'right-sidebar' ); ?>>
	<div class="inside-right-sidebar">
		<?php
		/**
		 * aureon_before_right_sidebar_content hook.
		 *
		 * @since 0.1
		 */
		do_action( 'aureon_before_right_sidebar_content' );

		if ( ! dynamic_sidebar( 'sidebar-1' ) ) {
			aureon_do_default_sidebar_widgets( 'right-sidebar' );
		}

		/**
		 * aureon_after_right_sidebar_content hook.
		 *
		 * @since 0.1
		 */
		do_action( 'aureon_after_right_sidebar_content' );
		?>
	</div>
</div>
