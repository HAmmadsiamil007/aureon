( function( $, api ) {
	api.controlConstructor[ 'aureon-copyright' ] = api.Control.extend( {
		ready() {
			var control = this;
			$( '.aureon-copyright-area', control.container ).on( 'change keyup',
				function() {
					control.setting.set( $( this ).val() );
				}
			);
		},
	} );
}( jQuery, wp.customize ) );
