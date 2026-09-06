( function( api ) {
	api.controlConstructor[ 'aureon-background-images' ] = api.Control.extend( {
		ready() {
			var control = this;

			control.container.on( 'change', '.aureon-backgrounds-repeat select',
				function() {
					control.settings.repeat.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change', '.aureon-backgrounds-size select',
				function() {
					control.settings.size.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change', '.aureon-backgrounds-attachment select',
				function() {
					control.settings.attachment.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'input', '.aureon-backgrounds-position input',
				function() {
					control.settings.position.set( jQuery( this ).val() );
				}
			);
		},
	} );
}( wp.customize ) );
