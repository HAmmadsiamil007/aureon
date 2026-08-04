<?php
/**
 * The template for displaying the header.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php aureon_do_microdata( 'body' ); ?>>
	<?php
	/**
	 * wp_body_open hook.
	 *
	 * @since 2.3
	 */
	do_action( 'wp_body_open' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core WP hook.

	/**
	 * aureon_before_header hook.
	 *
	 * @since 0.1
	 *
	 * @hooked aureon_do_skip_to_content_link - 2
	 * @hooked aureon_top_bar - 5
	 * @hooked aureon_add_navigation_before_header - 5
	 */
	do_action( 'aureon_before_header' );

	/**
	 * aureon_header hook.
	 *
	 * @since 1.3.42
	 *
	 * @hooked aureon_construct_header - 10
	 */
	do_action( 'aureon_header' );

	/**
	 * aureon_after_header hook.
	 *
	 * @since 0.1
	 *
	 * @hooked aureon_featured_page_header - 10
	 */
	do_action( 'aureon_after_header' );
	?>

	<div <?php aureon_do_attr( 'page' ); ?>>
		<?php
		/**
		 * aureon_inside_site_container hook.
		 *
		 * @since 2.4
		 */
		do_action( 'aureon_inside_site_container' );
		?>
		<div <?php aureon_do_attr( 'site-content' ); ?>>
			<?php
			/**
			 * aureon_inside_container hook.
			 *
			 * @since 0.1
			 */
			do_action( 'aureon_inside_container' );
