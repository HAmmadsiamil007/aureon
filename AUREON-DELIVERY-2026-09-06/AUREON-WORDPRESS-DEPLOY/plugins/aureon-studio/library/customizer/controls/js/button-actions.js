jQuery( function( $ ) {
	$( '[data-type="overlay_design"]' ).on( 'click', function( e ) {
		e.preventDefault();

		// eslint-disable-next-line no-alert
		if ( ! confirm( aureonButtonActions.warning ) ) {
			return;
		}

		( function( api ) {
			'use strict';

			api.instance( 'aureon_settings[slideout_background_color]' ).set( aureonButtonActions.styling.backgroundColor );
			api.instance( 'aureon_settings[slideout_text_color]' ).set( aureonButtonActions.styling.textColor );
			api.instance( 'aureon_settings[slideout_background_hover_color]' ).set( aureonButtonActions.styling.backgroundHoverColor );
			api.instance( 'aureon_settings[slideout_background_current_color]' ).set( aureonButtonActions.styling.backgroundCurrentColor );

			api.instance( 'aureon_settings[slideout_submenu_background_color]' ).set( aureonButtonActions.styling.subMenuBackgroundColor );
			api.instance( 'aureon_settings[slideout_submenu_text_color]' ).set( aureonButtonActions.styling.subMenuTextColor );
			api.instance( 'aureon_settings[slideout_submenu_background_hover_color]' ).set( aureonButtonActions.styling.subMenuBackgroundHoverColor );
			api.instance( 'aureon_settings[slideout_submenu_background_current_color]' ).set( aureonButtonActions.styling.subMenuBackgroundCurrentColor );

			api.instance( 'aureon_settings[slideout_font_weight]' ).set( aureonButtonActions.styling.fontWeight );
			api.instance( 'aureon_settings[slideout_font_size]' ).set( aureonButtonActions.styling.fontSize );

			$( '.wp-color-picker' ).wpColorPicker().change();
		}( wp.customize ) );
	} );

	$( '[data-type="regenerate_external_css"]' ).on( 'click', function( e ) {
		var $thisButton = $( this ); // eslint-disable-line no-var
		e.preventDefault();

		$thisButton.removeClass( 'success' ).addClass( 'loading' );

		$.post( ajaxurl, {
			action: 'aureon_regenerate_css_file',
			_nonce: $thisButton.data( 'nonce' ),
		} ).done( function() {
			$thisButton.removeClass( 'loading' ).addClass( 'success' );
		} );
	} );
} );
