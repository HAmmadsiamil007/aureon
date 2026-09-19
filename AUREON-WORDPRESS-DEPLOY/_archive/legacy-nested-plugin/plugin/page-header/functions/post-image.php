<?php
defined( 'WPINC' ) or die;

if ( ! function_exists( 'aureon_page_header_post_image' ) ) {
	add_action( 'aureon_after_entry_header', 'aureon_page_header_post_image' );
	/**
	 * Prints the Post Image to post excerpts
	 */
	function aureon_page_header_post_image() {
		// Get our options
		$options = aureon_page_header_get_options( get_the_ID() );

		// Check if we have a featured image
		$featured_image = ( has_post_thumbnail() ) ? apply_filters( 'aureon_post_image_force_featured_image', true ) : apply_filters( 'aureon_post_image_force_featured_image', false );
		// If using the featured image, stop
		if ( $featured_image ) {
			return;
		}

		// If our add to excerpt checkbox isn't set, stop
		if ( '' == get_post_meta( get_the_ID(), '_meta-aureon-page-header-add-to-excerpt', true ) ) {
			return;
		}

		if ( 'post' == get_post_type() && ! is_single() ) {
			// If an image is set and no content is set
			if ( '' == $options[ 'content' ] && aureon_page_header_get_image( 'ALL' ) ) {
				printf(
					'<div class="%1$s">
						%2$s
							%4$s
						%3$s
					</div>',
					'post-image page-header-post-image',
					( ! empty( $options[ 'image_link' ] ) ) ? '<a href="' . esc_url( $options[ 'image_link' ] ) . '"' . apply_filters( 'aureon_page_header_link_target','' ) . '>' : null,
					( ! empty( $options[ 'image_link' ] ) ) ? '</a>' : null,
					aureon_page_header_get_image_output()
				);
			}

			// If content is set, show it
			if ( '' !== $options[ 'content' ] && false !== $options[ 'content' ] ) {
				printf(
					'<div class="%1$s">
						<div class="%2$s">
							%3$s
								%5$s
							%4$s
						</div>
					</div>',
					'post-image aureon-page-header aureon-post-content-header page-header-post-image',
					'inside-page-header-container inside-post-content-header grid-container grid-parent',
					( ! empty( $options[ 'add_padding' ] ) ) ? '<div class="inside-page-header">' : null,
					( ! empty( $options[ 'add_padding' ] ) ) ? '</div>' : null,
					( ! empty( $options[ 'autop' ] ) ) ? do_shortcode( wpautop( $options[ 'content' ] ) ) : do_shortcode( $options[ 'content' ] )
				);
			}
		}
	}
}
