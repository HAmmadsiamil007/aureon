<?php
/**
 * The template for displaying search forms in Aureon
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php echo apply_filters( 'aureon_search_label', _x( 'Search for:', 'label', 'aureon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr( apply_filters( 'aureon_search_placeholder', _x( 'Search &hellip;', 'placeholder', 'aureon' ) ) ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php echo esc_attr( apply_filters( 'aureon_search_label', _x( 'Search for:', 'label', 'aureon' ) ) ); ?>">
	</label>
	<?php
	if ( aureon_is_using_flexbox() ) {
		printf(
			'<button class="search-submit" aria-label="%1$s">%2$s</button>',
			esc_attr( apply_filters( 'aureon_search_button', _x( 'Search', 'submit button', 'aureon' ) ) ),
			aureon_get_svg_icon( 'search' ) // phpcs:ignore -- Escaping not necessary here.
		);
	} else {
		printf(
			'<input type="submit" class="search-submit" value="%s">',
			apply_filters( 'aureon_search_button', _x( 'Search', 'submit button', 'aureon' ) ) // phpcs:ignore -- Escaping not necessary here.
		);
	}
	?>
</form>
