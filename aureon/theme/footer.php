<?php
/**
 * The template for displaying the footer.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

	</div>
</div>

<?php
/**
 * aureon_before_footer hook.
 *
 * @since 0.1
 */
do_action( 'aureon_before_footer' );
?>

<div <?php aureon_do_attr( 'footer' ); ?>>
	<?php
	/**
	 * aureon_before_footer_content hook.
	 *
	 * @since 0.1
	 */
	do_action( 'aureon_before_footer_content' );

	/**
	 * aureon_footer hook.
	 *
	 * @since 1.3.42
	 *
	 * @hooked aureon_construct_footer_widgets - 5
	 * @hooked aureon_construct_footer - 10
	 */
	do_action( 'aureon_footer' );

	/**
	 * aureon_after_footer_content hook.
	 *
	 * @since 0.1
	 */
	do_action( 'aureon_after_footer_content' );
	?>
</div>

<?php
/**
 * aureon_after_footer hook.
 *
 * @since 2.1
 */
do_action( 'aureon_after_footer' );

wp_footer();
?>

</body>
</html>
