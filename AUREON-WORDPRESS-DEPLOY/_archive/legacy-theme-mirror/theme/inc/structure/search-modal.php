<?php
/**
 * Post meta elements.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'wp_footer', 'aureon_do_search_modal' );
/**
 * Create the search modal HTML.
 */
function aureon_do_search_modal() {
	if ( ! aureon_get_option( 'nav_search_modal' ) ) {
		return;
	}
	?>
	<div class="aureon-modal aureon-search-modal" id="aureon-search" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search', 'aureon' ); ?>">
		<div class="aureon-modal__overlay" tabindex="-1" data-aureonmodal-close>
			<div class="aureon-modal__container">
				<?php do_action( 'aureon_inside_search_modal' ); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Create the search modal trigger.
 */
function aureon_do_search_modal_trigger() {
	if ( ! aureon_get_option( 'nav_search_modal' ) ) {
		return;
	}
	?>
	<span class="menu-bar-item">
		<a href="#" role="button" aria-label="<?php _e( 'Open search', 'aureon' ); ?>" aria-haspopup="dialog" aria-controls="aureon-search" data-gpmodal-trigger="aureon-search"><?php echo aureon_get_svg_icon( 'search', true ); // phpcs:ignore -- Escaped in function. ?></a>
	</span>
	<?php
}

add_filter( 'aureon_enable_modal_script', 'aureon_enable_search_modal' );
/**
 * Enable the search modal.
 */
function aureon_enable_search_modal() {
	return aureon_get_option( 'nav_search_modal' );
}

add_action( 'aureon_base_css', 'aureon_do_search_modal_css' );
/**
 * Do the modal CSS.
 *
 * @param Object $css The existing CSS object.
 */
function aureon_do_search_modal_css( $css ) {
	if ( ! aureon_get_option( 'nav_search_modal' ) ) {
		return;
	}

	$css->set_selector( '.search-modal-fields' );
	$css->add_property( 'display', 'flex' );

	$css->set_selector( '.aureon-search-modal .aureon-modal__overlay' );
	$css->add_property( 'align-items', 'flex-start' );
	$css->add_property( 'padding-top', '25vh' );
	$css->add_property( 'background', 'var(--aureon-search-modal-overlay-bg-color)' );

	$css->set_selector( '.search-modal-form' );
	$css->add_property( 'width', '500px' );
	$css->add_property( 'max-width', '100%' );
	$css->add_property( 'background-color', 'var(--aureon-search-modal-bg-color)' );
	$css->add_property( 'color', 'var(--aureon-search-modal-text-color)' );

	$css->set_selector( '.search-modal-form .search-field, .search-modal-form .search-field:focus' );
	$css->add_property( 'width', '100%' );
	$css->add_property( 'height', '60px' );
	$css->add_property( 'background-color', 'transparent' );
	$css->add_property( 'border', 0 );
	$css->add_property( 'appearance', 'none' );
	$css->add_property( 'color', 'currentColor' );

	$css->set_selector( '.search-modal-fields button, .search-modal-fields button:active, .search-modal-fields button:focus, .search-modal-fields button:hover' );
	$css->add_property( 'background-color', 'transparent' );
	$css->add_property( 'border', 0 );
	$css->add_property( 'color', 'currentColor' );
	$css->add_property( 'width', '60px' );

	return $css;
}

add_action( 'aureon_inside_search_modal', 'aureon_do_search_fields' );
/**
 * Add our search fields to the modal.
 */
function aureon_do_search_fields() {
	?>
	<form role="search" method="get" class="search-modal-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label for="search-modal-input" class="screen-reader-text"><?php echo apply_filters( 'aureon_search_label', _x( 'Search for:', 'label', 'aureon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
		<div class="search-modal-fields">
			<input id="search-modal-input" type="search" class="search-field" placeholder="<?php echo esc_attr( apply_filters( 'aureon_search_placeholder', _x( 'Search &hellip;', 'placeholder', 'aureon' ) ) ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
			<button aria-label="<?php echo esc_attr( apply_filters( 'aureon_search_button', _x( 'Search', 'submit button', 'aureon' ) ) ); ?>"><?php echo aureon_get_svg_icon( 'search' ); // phpcs:ignore -- Escaped in function. ?></button>
		</div>
		<?php do_action( 'aureon_inside_search_modal_form' ); ?>
	</form>
	<?php
}
