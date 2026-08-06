/**
 * AETHER Customizer Live Preview.
 *
 * Binds Customizer settings with postMessage transport to live DOM updates,
 * enabling instant preview without full page refreshes.
 *
 * @package Aureon
 */
( function( $ ) {
	'use strict';

	// Helper: update text content of elements matching a selector.
	function bindText( setting, selector ) {
		wp.customize( setting, function( value ) {
			value.bind( function( val ) {
				$( selector ).text( val );
			} );
		} );
	}

	// Helper: update a CSS custom property on :root.
	function bindCSSVar( setting, cssVar ) {
		wp.customize( setting, function( value ) {
			value.bind( function( val ) {
				document.documentElement.style.setProperty( cssVar, val );
			} );
		} );
	}

	// ─── Hero Slides ──────────────────────────────────────────
	// Note: Hero slides use Swiper with data-phantom attributes.
	// The headline/subline text is server-rendered; live preview requires
	// matching the slide index. We bind all three and update whichever
	// elements have the corresponding data-phantom value.

	// Announcement bar.
	wp.customize( 'aether_announcement_text', function( value ) {
		value.bind( function( val ) {
			$( '.announcement-bar .announcement-content span:first-child' ).html(
				'<i class="fas fa-truck"></i> ' + val
			);
		} );
	} );

	// ─── Section Labels ───────────────────────────────────────
	bindText( 'aether_section_label_categories', '.categories .section-label' );
	bindText( 'aether_section_title_categories', '.categories .section-title' );
	bindText( 'aether_section_label_bestsellers', '.bestsellers .section-label' );
	bindText( 'aether_section_title_bestsellers', '.bestsellers .section-title' );
	bindText( 'aether_section_subtitle_bestsellers', '.bestsellers .section-subtitle' );
	bindText( 'aether_section_label_reviews', '.reviews .section-label' );
	bindText( 'aether_section_title_reviews', '.reviews .section-title' );
	bindText( 'aether_section_label_faq', '.faq-section .section-label' );
	bindText( 'aether_section_title_faq', '.faq-section .section-title' );
	bindText( 'aether_section_subtitle_faq', '.faq-section .section-subtitle' );

	// ─── Design Tokens (CSS Custom Properties) ───────────────
	bindCSSVar( 'aether_color_void', '--void' );
	bindCSSVar( 'aether_color_surface', '--surface' );
	bindCSSVar( 'aether_color_accent', '--gold' );
	bindCSSVar( 'aether_color_text', '--chrome' );

	// Typography (refresh required for font changes to load).
	wp.customize( 'aether_font_heading', function( value ) {
		value.bind( function( val ) {
			document.documentElement.style.setProperty( '--font-heading', val );
		} );
	} );

	wp.customize( 'aether_font_body', function( value ) {
		value.bind( function( val ) {
			document.documentElement.style.setProperty( '--font-body', val );
		} );
	} );

	// Container width.
	wp.customize( 'aether_container_width', function( value ) {
		value.bind( function( val ) {
			document.documentElement.style.setProperty( '--container-max', val + 'px' );
		} );
	} );

} )( jQuery );
