jQuery( function( $ ) {
	$( '#aureon-select-all' ).on( 'click', function() {
		if ( this.checked ) {
			$( '.addon-checkbox:not(:disabled)' ).each( function() {
				this.checked = true;
			} );
		} else {
			$( '.addon-checkbox' ).each( function() {
				this.checked = false;
			} );
		}
	} );

	$( '#aureon_license_key_aureon_studio' ).on( 'input', function() {
		if ( '' !== $.trim( this.value ) ) {
			$( '.beta-testing-container' ).show();
		} else {
			$( '.beta-testing-container' ).hide();
		}
	} );

	$( 'input[name="aureon_package_hooks_deactivate_package"]' ).on( 'click', function() {
		// eslint-disable-next-line no-alert
		var check = confirm( dashboard.deprecated_module );

		if ( ! check ) {
			return false;
		}
	} );

	$( 'input[name="aureon_package_page_header_deactivate_package"]' ).on( 'click', function() {
		// eslint-disable-next-line no-alert
		var check = confirm( dashboard.deprecated_module );

		if ( ! check ) {
			return false;
		}
	} );

	$( 'input[name="aureon_package_sections_deactivate_package"]' ).on( 'click', function() {
		// eslint-disable-next-line no-alert
		var check = confirm( dashboard.deprecated_module );

		if ( ! check ) {
			return false;
		}
	} );
} );
