/* global wp, _ */
/**
 * Schema-driven repeater control JS.
 *
 * Renders repeatable rows from a schema (passed via control.params.schema),
 * persists the whole collection as a JSON string on the setting, and keeps
 * stable per-row IDs across reorders. No domain-specific logic here — the
 * hero slides schema is the first consumer.
 *
 * @package Aureon
 * @since 1.2.0
 */
( function( $ ) {
	'use strict';

	wp.customize.controlConstructor[ 'aureon-repeater' ] = wp.customize.Control.extend( {

		ready: function() {
			var control = this;

			control.$root   = control.container.find( '[data-repeater-root]' );
			control.$items  = control.$root.find( '.aureon-repeater__items' );
			control.params  = control.params || {};
			control.schema  = control.params.schema || { fields: [], visible: false };
			control.labels  = control.params.labels || {};
			control.itemKey = control.params.title_key || '';

			control.renderRows();
			control.bindSortable();
			control.bindEvents();

			// The button label is baked into the template at print time from
			// a dummy control (no choices), so re-apply the real label here.
			if ( control.params.add_label ) {
				control.$root.find( '.aureon-repeater__add-label' ).text( control.params.add_label );
			}
		},

		/* ── read / write ─────────────────────────────────────── */

		/**
		 * Normalize the setting value to an array of row objects.
		 */
		getValue: function() {
			var value = this.setting.get();

			if ( _.isString( value ) ) {
				try {
					value = JSON.parse( value );
				} catch ( e ) {
					value = [];
				}
			}

			return _.isArray( value ) ? value : [];
		},

		/**
		 * Push the current DOM state to the setting as JSON.
		 */
		flush: function() {
			var control = this, items = [];

			control.$items.children( '.aureon-repeater__item' ).each( function() {
				var row   = $( this ),
					item  = {},
					rowId = row.attr( 'data-row-id' );

				if ( rowId ) {
					item.id = rowId;
				}

				row.find( '[data-key]' ).each( function() {
					var $wrap = $( this ),
						key   = $wrap.data( 'key' );

					if ( $wrap.hasClass( 'aureon-repeater__cta' ) ) {
						item[ key ] = {};
						$wrap.find( '[data-cta-key]' ).each( function() {
							item[ key ][ $( this ).data( 'cta-key' ) ] = $( this ).val();
						} );
						return;
					}

					if ( $wrap.hasClass( 'aureon-repeater__field--checkbox' ) ) {
						item[ key ] = $wrap.find( 'input[type="checkbox"]' ).is( ':checked' );
						return;
					}

					if ( $wrap.hasClass( 'aureon-repeater__field--image' ) ) {
						item[ key ] = $wrap.find( 'input[type="hidden"]' ).val();
						return;
					}

					item[ key ] = $wrap.find( 'input, textarea' ).first().val();
				} );

				items.push( item );
			} );

			this.setting.set( JSON.stringify( items ) );
		},

		/* ── rendering ───────────────────────────────────────── */

		/**
		 * Build all rows from the current value.
		 */
		renderRows: function() {
			var self = this, value = this.getValue();

			this.$items.empty();
			$.each( value, function( index, item ) {
				self.$items.append( self.buildRow( item, index ) );
			} );
		},

		/**
		 * Build a single row.
		 */
		buildRow: function( item, index ) {
			var self   = this,
				row    = $( '<div class="aureon-repeater__item">' ),
				title  = this.rowTitle( item, index ),
				inline = this.hasVisibleField() ? '' : ' style="display:none"';

			row.append(
				'<div class="aureon-repeater__actionbar">' +
					'<span class="dashicons dashicons-sort aureon-repeater__drag" title="' + ( this.labels.drag || 'Drag' ) + '"></span>' +
					'<span class="aureon-repeater__item-title">' + escapeHtml( title ) + '</span>' +
					'<span class="aureon-repeater__actions">' +
						'<span class="dashicons dashicons-visibility aureon-repeater__visibility"' + inline + ' title="' + ( this.labels.visible || 'Visible' ) + '"></span>' +
						'<span class="dashicons dashicons-arrow-down-alt2 aureon-repeater__toggle" title="' + ( this.labels.collapse || 'Collapse' ) + '"></span>' +
						'<span class="dashicons dashicons-no-alt aureon-repeater__remove" title="' + ( this.labels.remove || 'Remove' ) + '"></span>' +
					'</span>' +
				'</div>'
			);

			var body = $( '<div class="aureon-repeater__body">' );
			$.each( this.schema.fields, function() {
				body.append( self.buildField( this, item ) );
			} );
			row.append( body );

			if ( this.schema.visible && item.visible === false ) {
				row.addClass( 'is-hidden' );
			}

			row.data( 'index', index );
			if ( item && item.id ) {
				row.attr( 'data-row-id', item.id );
			}
			return row;
		},

		/**
		 * Collapsed-row title: preferred key, else first textish value, else
		 * "Item N".
		 */
		rowTitle: function( item, index ) {
			var fields = this.schema.fields || [],
				key    = this.itemKey,
				field, i;

			if ( key ) {
				return this.fieldValue( item, key ) || ( this.params.item_label || 'Item' ) + ' ' + ( index + 1 );
			}

			for ( i = 0; i < fields.length; i += 1 ) {
				field = fields[ i ];
				if ( 'cta' !== field.type && 'image' !== field.type && 'checkbox' !== field.type ) {
					key = field.key;
					return this.fieldValue( item, key ) || ( this.params.item_label || 'Item' ) + ' ' + ( index + 1 );
				}
			}

			return ( this.params.item_label || 'Item' ) + ' ' + ( index + 1 );
		},

		fieldValue: function( item, key ) {
			if ( item && item[ key ] !== undefined ) {
				return item[ key ];
			}
			return '';
		},

		/**
		 * Whether the schema declares a boolean "visible" field (drives the
		 * visibility eye + hide styling).
		 */
		hasVisibleField: function() {
			if ( ! this.schema.visible ) {
				return false;
			}
			var i;
			for ( i = 0; i < ( this.schema.fields || [] ).length; i += 1 ) {
				if ( 'visible' === this.schema.fields[ i ].key && 'checkbox' === this.schema.fields[ i ].type ) {
					return true;
				}
			}
			return false;
		},

		/**
		 * Build one schema field's DOM.
		 */
		buildField: function( field, item ) {
			if ( ! field || ! field.type ) {
				return '';
			}

			var self   = this,
				value  = this.fieldValue( item, field.key ),
				label  = field.label || field.key,
				wrap   = $( '<div class="aureon-repeater__wrap" data-key="' + escapeAttr( field.key ) + '">' );

			switch ( field.type ) {
				case 'cta':
					wrap.addClass( 'aureon-repeater__cta' );
					$.each( field.sub || [ { value: 'label', label: this.labels.cta_label || 'Label' }, { value: 'url', label: this.labels.cta_url || 'URL' } ], function() {
						var subValue = '';
						if ( value && value[ this.value ] !== undefined ) {
							subValue = value[ this.value ];
						}
						wrap.append(
							'<label class="aureon-repeater__sub">' +
								'<span class="aureon-repeater__sub-label">' + escapeHtml( this.label ) + '</span>' +
								'<input type="text" class="aureon-repeater__input" data-cta-key="' + escapeAttr( this.value ) + '" value="' + escapeAttr( subValue ) + '">' +
							'</label>'
						);
					} );
					break;

				case 'image':
					wrap.addClass( 'aureon-repeater__field--image' );
					wrap.append(
						'<label class="aureon-repeater__label">' + escapeHtml( label ) + '</label>' +
						'<div class="aureon-repeater__image">' +
							'<input type="hidden" class="aureon-repeater__input" value="' + escapeAttr( value ) + '">' +
							'<div class="aureon-repeater__thumb' + ( value ? '' : ' is-empty' ) + '">' +
								( value ? '' : '<span class="dashicons dashicons-format-image"></span>' ) +
							'</div>' +
							'<button type="button" class="button aureon-repeater__image-button">' + escapeHtml( this.labels.upload || 'Select image' ) + '</button>' +
							'<button type="button" class="button-link-delete aureon-repeater__image-remove"' + ( value ? ' data-has-image="1"' : '' ) + '>' + escapeHtml( this.labels.removeImage || 'Remove' ) + '</button>' +
						'</div>'
					);
					break;

				case 'textarea':
					wrap.append(
						'<label class="aureon-repeater__label">' + escapeHtml( label ) + '</label>' +
						'<textarea class="aureon-repeater__input" rows="3" placeholder="' + escapeAttr( field.placeholder || '' ) + '">' + escapeHtml( value ) + '</textarea>'
					);
					break;

				case 'checkbox':
					wrap.addClass( 'aureon-repeater__field--checkbox' );
					wrap.append(
						'<label class="aureon-repeater__checkbox">' +
							'<input type="checkbox" class="aureon-repeater__input"' + ( value ? ' checked' : '' ) + '>' +
							'<span>' + escapeHtml( label ) + '</span>' +
						'</label>'
					);
					break;

				case 'color':
					wrap.append(
						'<label class="aureon-repeater__label">' + escapeHtml( label ) + '</label>' +
						'<input type="text" class="aureon-repeater__input aureon-repeater__color" data-alpha="1" value="' + escapeAttr( value ) + '" placeholder="' + escapeAttr( field.placeholder || '' ) + '">'
					);
					break;

				case 'url':
					wrap.append(
						'<label class="aureon-repeater__label">' + escapeHtml( label ) + '</label>' +
						'<input type="text" class="aureon-repeater__input aureon-repeater__url" value="' + escapeAttr( value ) + '" placeholder="' + escapeAttr( field.placeholder || 'https://' ) + '">'
					);
					break;

				case 'text':
				default:
					wrap.append(
						'<label class="aureon-repeater__label">' + escapeHtml( label ) + '</label>' +
						'<input type="text" class="aureon-repeater__input" value="' + escapeAttr( value ) + '" placeholder="' + escapeAttr( field.placeholder || '' ) + '">'
					);
					break;
			}

			return wrap;
		},

		/* ── wire up ─────────────────────────────────────────── */

		/**
		 * Drag-to-reorder when jQuery UI sortable is available.
		 */
		bindSortable: function() {
			if ( ! $.fn.sortable ) {
				return;
			}

			var control = this;

			this.$items.sortable( {
				handle: '.aureon-repeater__drag',
				items:  '.aureon-repeater__item',
				axis:   'y',
				cursor: 'grabbing',
				stop:   function() {
					control.flush();
				}
			} );
		},

		/**
		 * Delegated events; survives repeater-produced rows.
		 */
		bindEvents: function() {
			var control = this, $root = control.$root;

			var debounce = function( fn, wait ) {
				var timer;
				return function() {
					var args = arguments;
					clearTimeout( timer );
					timer = setTimeout( function() {
						fn.apply( null, args );
					}, wait || 250 );
				};
			};

			$root.on( 'input change', '.aureon-repeater__input', debounce( function() {
				control.flush();
			} ) );

			$root.on( 'change', '.aureon-repeater__field--checkbox input', function() {
				var row = $( this ).closest( '.aureon-repeater__item' );
				row.toggleClass( 'is-hidden', ! $( this ).is( ':checked' ) );
			} );

			$root.on( 'click', '.aureon-repeater__add', function() {
				control.addRow();
			} );

			$root.on( 'click', '.aureon-repeater__toggle', function() {
				var row = $( this ).closest( '.aureon-repeater__item' );
				row.toggleClass( 'is-collapsed' );
				$( this ).toggleClass( 'dashicons-arrow-down-alt2 dashicons-arrow-right-alt2' );
			} );

			$root.on( 'click', '.aureon-repeater__remove', function() {
				$( this ).closest( '.aureon-repeater__item' ).remove();
				control.flush();
			} );

			$root.on( 'click', '.aureon-repeater__visibility', function() {
				var row  = $( this ).closest( '.aureon-repeater__item' ),
					box  = row.find( '.aureon-repeater__field--checkbox input' );

				row.toggleClass( 'is-hidden' );
				box.prop( 'checked', ! row.hasClass( 'is-hidden' ) ).trigger( 'change', [ true ] );
			} );

			$root.on( 'click', '.aureon-repeater__image-button', function() {
				control.openMedia( $( this ) );
			} );

			$root.on( 'click', '.aureon-repeater__image-remove', function() {
				var wrap = $( this ).closest( '.aureon-repeater__field--image' );
				wrap.find( 'input[type="hidden"]' ).val( '' ).trigger( 'change' );
				wrap.find( '.aureon-repeater__thumb' ).addClass( 'is-empty' ).empty();
				$( this ).removeAttr( 'data-has-image' );
			} );
		},

		/**
		 * Add a blank row with a fresh stable id and flush.
		 */
		addRow: function() {
			var items   = this.getValue(),
				newItem = { id: 'slide_' + this.makeId() };

			if ( this.hasVisibleField() ) {
				newItem[ this.visibleKey() ] = true;
			}

			items.push( newItem );
			this.setting.set( JSON.stringify( items ) );
			this.$items.append( this.buildRow( newItem, items.length - 1 ) );
			this.flush();
		},

		visibleKey: function() {
			var i;
			for ( i = 0; i < ( this.schema.fields || [] ).length; i += 1 ) {
				if ( 'checkbox' === this.schema.fields[ i ].type ) {
					return this.schema.fields[ i ].key;
				}
			}
			return 'visible';
		},

		/**
		 * Open a wp.media frame for an image field.
		 */
		openMedia: function( button ) {
			var control = this,
				wrap    = button.closest( '.aureon-repeater__field--image' ),
				input   = wrap.find( 'input[type=hidden]' ),
				thumb   = wrap.find( '.aureon-repeater__thumb' ),
				remove  = wrap.find( '.aureon-repeater__image-remove' ),
				frame;

			if ( ! window.wp || ! wp.media ) {
				return;
			}

			frame = wp.media( {
				title: control.labels.upload || 'Select image',
				multiple: false,
				library: { type: 'image' },
				button: { text: control.labels.upload || 'Select' }
			} );

			frame.on( 'select', function() {
				var attachment = frame.state().get( 'selection' ).first().toJSON(),
					url        = attachment.url;

				input.val( url ).trigger( 'change' );
				thumb.removeClass( 'is-empty' ).empty()
					.append( '<img src="' + escapeAttr( url ) + '" alt="">' );
				remove.attr( 'data-has-image', 1 );
			} );

			frame.open();
		},

		/**
		 * Random 8-hex id suffix. Stable per row — never regenerated on edit
		 * or reorder, only issued when a row is first created.
		 */
		makeId: function() {
			var chars = 'abcdef0123456789',
				out   = '',
				bytes, i;

			if ( window.crypto && crypto.getRandomValues ) {
				bytes = new Uint8Array( 4 );
				crypto.getRandomValues( bytes );
			} else {
				bytes = [ 0, 0, 0, 0 ];
				for ( i = 0; i < 4; i += 1 ) {
					bytes[ i ] = Math.floor( Math.random() * 256 );
				}
			}

			for ( i = 0; i < 4; i += 1 ) {
				out += ( '0' + bytes[ i ].toString( 16 ) ).slice( -2 );
			}

			return out;
		}
	} );

	/**
	 * Minimal HTML escape for attribute/HTML contexts.
	 */
	function escapeHtml( text ) {
		if ( ! text ) {
			return '';
		}

		return String( text )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' )
			.replace( /"/g, '&quot;' )
			.replace( /'/g, '&#39;' );
	}

	function escapeAttr( text ) {
		return escapeHtml( text );
	}
}( jQuery ) );