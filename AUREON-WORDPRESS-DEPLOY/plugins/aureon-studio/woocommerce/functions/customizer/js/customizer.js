jQuery( function( $ ) {
	$( '#customize-control-aureon_woocommerce_primary_button_message a' ).on( 'click', function( e ) {
		e.preventDefault();
		wp.customize.control( 'aureon_settings[form_button_background_color]' ).focus();
	} );
} );
