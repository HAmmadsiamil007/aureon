<?php
/**
 * The template for displaying 404 pages.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
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
		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is allowed in filter here. ?>
		<h1 class="entry-title" itemprop="headline"><?php echo apply_filters( 'aureon_404_title', __( 'Oops! That page can&rsquo;t be found.', 'aureon' ) ); ?></h1>
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

	$itemprop = '';

	if ( 'microdata' === aureon_get_schema_type() ) {
		$itemprop = ' itemprop="text"';
	}
	?>

	<div class="entry-content"<?php echo $itemprop; // phpcs:ignore -- No escaping needed. ?>>
		<?php
		printf(
			'<p>%s</p>',
			apply_filters( 'aureon_404_text', __( 'It looks like nothing was found at this location. Maybe try searching?', 'aureon' ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is allowed in filter here.
		);

		get_search_form();
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
