jQuery( function( $ ) {
	$( '.post-type-aureon_elements .page-title-action:not(.legacy-button)' ).on( 'click', function( e ) {
		e.preventDefault();

		$( '.choose-element-type-parent' ).show();
		$( '.choose-element-type .select-type' ).focus();
	} );

	$( '.close-choose-element-type' ).on( 'click', function( e ) {
		e.preventDefault();

		$( '.choose-element-type-parent' ).hide();
		$( '.page-title-action' ).focus();
	} );

	$( '.post-type-aureon_elements' ).on( 'keyup', function( e ) {
		const $element = $( '.choose-element-type-parent' );

		if ( e.key === 'Escape' && $element.is( ':visible' ) ) {
			$element.hide();
			$( '.page-title-action' ).focus();
		}
	} );

	// Don't allow Elements to quick edit parents.
	$( '.inline-edit-aureon_elements select#post_parent, .inline-edit-aureon_elements .inline-edit-menu-order-input, .bulk-edit-aureon_elements select#post_parent' ).each( function() {
		$( this ).closest( 'label' ).remove();
	} );
} );
