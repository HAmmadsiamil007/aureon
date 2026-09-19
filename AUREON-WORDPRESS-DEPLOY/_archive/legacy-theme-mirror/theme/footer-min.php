<?php
/**
 * The template for displaying the footer.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * aureon_after_footer hook.
 *
 * @since 2.1
 */
do_action( 'aureon_minimal_footer' );

wp_footer();
?>

</body>
</html>
