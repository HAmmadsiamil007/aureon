( function( api ) {
	'use strict';

	// Add callback for when the header_textcolor setting exists.
	api( 'aureon_settings[nav_position_setting]', function( setting ) {
		var isNavFloated, isNavAlignable, setNavDropPointActiveState, setNavAlignmentsActiveState;

		/**
		 * Determine whether the navigation is floating.
		 *
		 * @returns {boolean} Is floating?
		 */
		isNavFloated = function() {
			if ( 'nav-float-right' === setting.get() || 'nav-float-left' === setting.get() ) {
				return true;
			}

			return false;
		};

		/**
		 * Determine whether the navigation is align-able.
		 *
		 * @returns {boolean} Is floating?
		 */
		isNavAlignable = function() {
			if ( 'nav-float-right' === setting.get() || 'nav-float-left' === setting.get() ) {
				var navAsHeader = api.instance( 'aureon_menu_plus_settings[navigation_as_header]' );

				if ( navAsHeader && navAsHeader.get() ) {
					return true;
				}

				return false;
			}

			return true;
		};

		/**
		 * Update a control's active state according to the navigation location setting's value.
		 *
		 * @param {wp.customize.Control} control
		 */
		setNavDropPointActiveState = function( control ) {
			var setActiveState = function() {
				control.active.set( isNavFloated() );
			};

			// FYI: With the following we can eliminate all of our PHP active_callback code.
			control.active.validate = isNavFloated;

			// Set initial active state.
			setActiveState();

			/*
			 * Update activate state whenever the setting is changed.
			 * Even when the setting does have a refresh transport where the
			 * server-side active callback will manage the active state upon
			 * refresh, having this JS management of the active state will
			 * ensure that controls will have their visibility toggled
			 * immediately instead of waiting for the preview to load.
			 * This is especially important if the setting has a postMessage
			 * transport where changing the setting wouldn't normally cause
			 * the preview to refresh and thus the server-side active_callbacks
			 * would not get invoked.
			 */
			setting.bind( setActiveState );
		};

		/**
		 * Update a control's active state according to the navigation location setting's value.
		 *
		 * @param {wp.customize.Control} control
		 */
		setNavAlignmentsActiveState = function( control ) {
			var setActiveState = function() {
				control.active.set( isNavAlignable() );
			};

			// FYI: With the following we can eliminate all of our PHP active_callback code.
			control.active.validate = isNavAlignable;

			// Set initial active state.
			setActiveState();

			/*
			 * Update activate state whenever the setting is changed.
			 * Even when the setting does have a refresh transport where the
			 * server-side active callback will manage the active state upon
			 * refresh, having this JS management of the active state will
			 * ensure that controls will have their visibility toggled
			 * immediately instead of waiting for the preview to load.
			 * This is especially important if the setting has a postMessage
			 * transport where changing the setting wouldn't normally cause
			 * the preview to refresh and thus the server-side active_callbacks
			 * would not get invoked.
			 */
			setting.bind( setActiveState );
		};

		api.control( 'aureon_settings[nav_drop_point]', setNavDropPointActiveState );
		api.control( 'aureon_settings[nav_layout_setting]', setNavAlignmentsActiveState );
		api.control( 'aureon_settings[nav_inner_width]', setNavAlignmentsActiveState );
		api.control( 'aureon_settings[nav_alignment_setting]', setNavAlignmentsActiveState );
	} );

	var setOption = function( options ) {
		if ( options.headerAlignment ) {
			api.instance( 'aureon_settings[header_alignment_setting]' ).set( options.headerAlignment );
		}

		if ( options.navLocation ) {
			api.instance( 'aureon_settings[nav_position_setting]' ).set( options.navLocation );
		}

		if ( options.navAlignment ) {
			api.instance( 'aureon_settings[nav_alignment_setting]' ).set( options.navAlignment );
		}

		if ( options.boxAlignment ) {
			api.instance( 'aureon_settings[container_alignment]' ).set( options.boxAlignment );
		}

		if ( options.siteTitleFontSize ) {
			api.instance( 'aureon_settings[site_title_font_size]' ).set( options.siteTitleFontSize );
		}

		if ( 'undefined' !== typeof options.hideSiteTagline ) {
			api.instance( 'aureon_settings[hide_tagline]' ).set( options.hideSiteTagline );
		}

		if ( options.headerPaddingTop ) {
			api.instance( 'aureon_spacing_settings[header_top]' ).set( options.headerPaddingTop );
		}

		if ( options.headerPaddingBottom ) {
			api.instance( 'aureon_spacing_settings[header_bottom]' ).set( options.headerPaddingBottom );
		}
	};

	api( 'aureon_header_helper', function( value ) {
		var headerAlignment = false,
			navLocation = false,
			navAlignment = false,
			boxAlignment = false,
			siteTitleFontSize = false,
			hideSiteTagline = false,
			headerPaddingTop = false,
			headerPaddingBottom = false;

		value.bind( function( newval ) {
			var headerAlignmentSetting = api.instance( 'aureon_settings[header_alignment_setting]' );
			var navLocationSetting = api.instance( 'aureon_settings[nav_position_setting]' );
			var navAlignmentSetting = api.instance( 'aureon_settings[nav_alignment_setting]' );
			var boxAlignmentSetting = api.instance( 'aureon_settings[container_alignment]' );
			var siteTitleFontSizeSetting = api.instance( 'aureon_settings[site_title_font_size]' );
			var hideSiteTaglineSetting = api.instance( 'aureon_settings[hide_tagline]' );
			var headerPaddingTopSetting = api.instance( 'aureon_spacing_settings[header_top]' );
			var headerPaddingBottomSetting = api.instance( 'aureon_spacing_settings[header_bottom]' );

			if ( ! headerAlignmentSetting._dirty ) {
				headerAlignment = headerAlignmentSetting.get();
			}

			if ( ! navLocationSetting._dirty ) {
				navLocation = navLocationSetting.get();
			}

			if ( ! navAlignmentSetting._dirty ) {
				navAlignment = navAlignmentSetting.get();
			}

			if ( ! boxAlignmentSetting._dirty ) {
				boxAlignment = boxAlignmentSetting.get();
			}

			if ( ! siteTitleFontSizeSetting._dirty ) {
				siteTitleFontSize = siteTitleFontSizeSetting.get();
			}

			if ( ! hideSiteTaglineSetting._dirty ) {
				hideSiteTagline = hideSiteTaglineSetting.get();
			}

			if ( ! headerPaddingTopSetting._dirty ) {
				headerPaddingTop = headerPaddingTopSetting.get();
			}

			if ( ! headerPaddingBottomSetting._dirty ) {
				headerPaddingBottom = headerPaddingBottomSetting.get();
			}

			var options = {
				headerAlignment: aureon_defaults.header_alignment_setting,
				navLocation: aureon_defaults.nav_position_setting,
				navAlignment: aureon_defaults.nav_alignment_setting,
				boxAlignment: aureon_defaults.container_alignment,
				siteTitleFontSize: aureon_typography_defaults.site_title_font_size,
				hideSiteTagline: aureon_defaults.hide_tagline,
				headerPaddingTop: aureon_spacing_defaults.header_top,
				headerPaddingBottom: aureon_spacing_defaults.header_bottom,
			};

			if ( 'current' === newval ) {
				options = {
					headerAlignment: headerAlignment,
					navLocation: navLocation,
					navAlignment: navAlignment,
					boxAlignment: boxAlignment,
					siteTitleFontSize: siteTitleFontSize,
					hideSiteTagline: hideSiteTagline,
					headerPaddingTop: headerPaddingTop,
					headerPaddingBottom: headerPaddingBottom,
				};

				setOption( options );
			}

			if ( 'default' === newval ) {
				setOption( options );
			}

			if ( 'classic' === newval ) {
				var options = {
					headerAlignment: 'left',
					navLocation: 'nav-below-header',
					navAlignment: 'left',
					boxAlignment: 'boxes',
					siteTitleFontSize: '45',
					hideSiteTagline: '',
					headerPaddingTop: '40',
					headerPaddingBottom: '40',
				};

				setOption( options );
			}

			if ( 'nav-before' === newval ) {
				options['headerAlignment'] = 'left';
				options['navLocation'] = 'nav-above-header';
				options['navAlignment'] = 'left';

				setOption( options );
			}

			if ( 'nav-after' === newval ) {
				options['headerAlignment'] = 'left';
				options['navLocation'] = 'nav-below-header';
				options['navAlignment'] = 'left';

				setOption( options );
			}

			if ( 'nav-before-centered' === newval ) {
				options['headerAlignment'] = 'center';
				options['navLocation'] = 'nav-above-header';
				options['navAlignment'] = 'center';

				setOption( options );
			}

			if ( 'nav-after-centered' === newval ) {
				options['headerAlignment'] = 'center';
				options['navLocation'] = 'nav-below-header';
				options['navAlignment'] = 'center';

				setOption( options );
			}

			if ( 'nav-left' === newval ) {
				options['headerAlignment'] = 'left';
				options['navLocation'] = 'nav-float-left';
				options['navAlignment'] = 'right';

				setOption( options );
			}
		} );
	} );

	api( 'aureon_settings[use_dynamic_typography]', function( value ) {
		var fontManager = api.control( 'aureon_settings[font_manager]' );
		var typographyManager = api.control( 'aureon_settings[typography]' );

		value.bind( function( newval ) {
			if ( newval ) {
				if ( fontManager.setting.get().length === 0 ) {
					fontManager.setting.set( aureonCustomizeControls.mappedTypographyData.fonts );
				}

				if ( typographyManager.setting.get().length === 0 ) {
					typographyManager.setting.set( aureonCustomizeControls.mappedTypographyData.typography );
				}
			}
		} );
	} );
}( wp.customize ) );
