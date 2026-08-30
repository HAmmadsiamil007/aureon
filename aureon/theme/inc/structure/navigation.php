<?php
/**
 * Navigation elements.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'aureon_navigation_position' ) ) {
	/**
	 * Build the navigation.
	 *
	 * @since 0.1
	 */
	function aureon_navigation_position() {
		/**
		 * aureon_before_navigation hook.
		 *
		 * @since 3.0.0
		 */
		do_action( 'aureon_before_navigation' );
		?>
		<nav <?php aureon_do_attr( 'navigation' ); ?>>
			<div <?php aureon_do_attr( 'inside-navigation' ); ?>>
				<?php
				/**
				 * aureon_inside_navigation hook.
				 *
				 * @since 0.1
				 *
				 * @hooked aureon_navigation_search - 10
				 * @hooked aureon_mobile_menu_search_icon - 10
				 */
				do_action( 'aureon_inside_navigation' );
				?>
				<button <?php aureon_do_attr( 'menu-toggle' ); ?>>
					<?php
					/**
					 * aureon_inside_mobile_menu hook.
					 *
					 * @since 0.1
					 */
					do_action( 'aureon_inside_mobile_menu' );

					aureon_do_svg_icon( 'menu-bars', true );

					$mobile_menu_label = apply_filters( 'aureon_mobile_menu_label', __( 'Menu', 'aureon' ) );

					if ( $mobile_menu_label ) {
						printf(
							'<span class="mobile-menu">%s</span>',
							$mobile_menu_label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML allowed in filter.
						);
					} else {
						printf(
							'<span class="screen-reader-text">%s</span>',
							esc_html__( 'Menu', 'aureon' )
						);
					}
					?>
				</button>
				<?php
				/**
				 * aureon_after_mobile_menu_button hook
				 *
				 * @since 3.0.0
				 */
				do_action( 'aureon_after_mobile_menu_button' );

				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container' => 'div',
						'container_class' => 'main-nav',
						'container_id' => 'primary-menu',
						'menu_class' => '',
						'fallback_cb' => 'aureon_menu_fallback',
						'items_wrap' => '<ul id="%1$s" class="%2$s ' . join( ' ', aureon_get_element_classes( 'menu' ) ) . '">%3$s</ul>',
					)
				);

				/**
				 * aureon_after_primary_menu hook.
				 *
				 * @since 2.3
				 */
				do_action( 'aureon_after_primary_menu' );
				?>
			</div>
		</nav>
		<?php
		/**
		 * aureon_after_navigation hook.
		 *
		 * @since 3.0.0
		 */
		do_action( 'aureon_after_navigation' );
	}
}

add_action( 'aureon_before_navigation', 'aureon_do_header_mobile_menu_toggle' );
/**
 * Build the mobile menu toggle in the header.
 *
 * @since 3.0.0
 */
function aureon_do_header_mobile_menu_toggle() {
	if ( ! aureon_is_using_flexbox() ) {
		return;
	}

	if ( ! aureon_has_inline_mobile_toggle() ) {
		return;
	}
	?>
	<nav <?php aureon_do_attr( 'mobile-menu-control-wrapper' ); ?>>
		<?php
		/**
		 * aureon_inside_mobile_menu_control_wrapper hook.
		 *
		 * @since 3.0.0
		 */
		do_action( 'aureon_inside_mobile_menu_control_wrapper' );
		?>
		<button <?php aureon_do_attr( 'menu-toggle', array( 'data-nav' => 'site-navigation' ) ); ?>>
			<?php
			/**
			 * aureon_inside_mobile_menu hook.
			 *
			 * @since 0.1
			 */
			do_action( 'aureon_inside_mobile_menu' );

			aureon_do_svg_icon( 'menu-bars', true );

			$mobile_menu_label = __( 'Menu', 'aureon' );

			if ( 'nav-float-right' === aureon_get_navigation_location() || 'nav-float-left' === aureon_get_navigation_location() ) {
				$mobile_menu_label = '';
			}

			$mobile_menu_label = apply_filters( 'aureon_mobile_menu_label', $mobile_menu_label );

			if ( $mobile_menu_label ) {
				printf(
					'<span class="mobile-menu">%s</span>',
					$mobile_menu_label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML allowed in filter.
				);
			} else {
				printf(
					'<span class="screen-reader-text">%s</span>',
					esc_html__( 'Menu', 'aureon' )
				);
			}
			?>
		</button>
	</nav>
	<?php
}

if ( ! function_exists( 'aureon_menu_fallback' ) ) {
	/**
	 * Menu fallback.
	 *
	 * @since 1.1.4
	 *
	 * @param array $args Existing menu args.
	 */
	function aureon_menu_fallback( $args ) {
		$aureon_settings = wp_parse_args(
			get_option( 'aureon_settings', array() ),
			aureon_get_defaults()
		);
		?>
		<div id="primary-menu" class="main-nav">
			<ul <?php aureon_do_element_classes( 'menu' ); ?>>
				<?php
				$args = array(
					'sort_column' => 'menu_order',
					'title_li' => '',
					'walker' => new Aureon_Page_Walker(),
				);

				wp_list_pages( $args );

				if ( ! aureon_is_using_flexbox() && 'enable' === $aureon_settings['nav_search'] ) {
					$search_item = apply_filters(
						'aureon_navigation_search_menu_item_output',
						sprintf(
							'<li class="search-item menu-item-align-right"><a aria-label="%1$s" href="#">%2$s</a></li>',
							esc_attr__( 'Open Search Bar', 'aureon' ),
							aureon_get_svg_icon( 'search', true ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in function.
						)
					);

					echo $search_item; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe output.
				}
				?>
			</ul>
		</div>
		<?php
	}
}

if ( ! function_exists( 'aureon_add_navigation_after_header' ) ) {
	add_action( 'aureon_after_header', 'aureon_add_navigation_after_header', 5 );
	/**
	 * Generate the navigation based on settings
	 *
	 * It would be better to have all of these inside one action, but these
	 * are kept this way to maintain backward compatibility for people
	 * un-hooking and moving the navigation/changing the priority.
	 *
	 * @since 0.1
	 */
	function aureon_add_navigation_after_header() {
		if ( 'nav-below-header' === aureon_get_navigation_location() ) {
			aureon_navigation_position();
		}
	}
}

if ( ! function_exists( 'aureon_add_navigation_before_header' ) ) {
	add_action( 'aureon_before_header', 'aureon_add_navigation_before_header', 5 );
	/**
	 * Generate the navigation based on settings
	 *
	 * It would be better to have all of these inside one action, but these
	 * are kept this way to maintain backward compatibility for people
	 * un-hooking and moving the navigation/changing the priority.
	 *
	 * @since 0.1
	 */
	function aureon_add_navigation_before_header() {
		if ( 'nav-above-header' === aureon_get_navigation_location() ) {
			aureon_navigation_position();
		}
	}
}

if ( ! function_exists( 'aureon_add_navigation_float_right' ) ) {
	add_action( 'aureon_after_header_content', 'aureon_add_navigation_float_right', 5 );
	/**
	 * Generate the navigation based on settings
	 *
	 * It would be better to have all of these inside one action, but these
	 * are kept this way to maintain backward compatibility for people
	 * un-hooking and moving the navigation/changing the priority.
	 *
	 * @since 0.1
	 */
	function aureon_add_navigation_float_right() {
		if ( 'nav-float-right' === aureon_get_navigation_location() || 'nav-float-left' === aureon_get_navigation_location() ) {
			aureon_navigation_position();
		}
	}
}

if ( ! function_exists( 'aureon_add_navigation_before_right_sidebar' ) ) {
	add_action( 'aureon_before_right_sidebar_content', 'aureon_add_navigation_before_right_sidebar', 5 );
	/**
	 * Generate the navigation based on settings
	 *
	 * It would be better to have all of these inside one action, but these
	 * are kept this way to maintain backward compatibility for people
	 * un-hooking and moving the navigation/changing the priority.
	 *
	 * @since 0.1
	 */
	function aureon_add_navigation_before_right_sidebar() {
		if ( 'nav-right-sidebar' === aureon_get_navigation_location() ) {
			echo '<div class="gen-sidebar-nav">';
				aureon_navigation_position();
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'aureon_add_navigation_before_left_sidebar' ) ) {
	add_action( 'aureon_before_left_sidebar_content', 'aureon_add_navigation_before_left_sidebar', 5 );
	/**
	 * Generate the navigation based on settings
	 *
	 * It would be better to have all of these inside one action, but these
	 * are kept this way to maintain backward compatibility for people
	 * un-hooking and moving the navigation/changing the priority.
	 *
	 * @since 0.1
	 */
	function aureon_add_navigation_before_left_sidebar() {
		if ( 'nav-left-sidebar' === aureon_get_navigation_location() ) {
			echo '<div class="gen-sidebar-nav">';
				aureon_navigation_position();
			echo '</div>';
		}
	}
}

if ( ! class_exists( 'Aureon_Page_Walker' ) && class_exists( 'Walker_Page' ) ) {
	/**
	 * Add current-menu-item to the current item if no theme location is set
	 * This means we don't have to duplicate CSS properties for current_page_item and current-menu-item
	 *
	 * @since 1.3.21
	 */
	class Aureon_Page_Walker extends Walker_Page {
		function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) { // phpcs:ignore
			$css_class = array( 'page_item', 'page-item-' . $page->ID );
			$button = '';

			if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
				$css_class[] = 'menu-item-has-children';
				$icon = aureon_get_svg_icon( 'arrow' );
				$button = '<span role="presentation" class="dropdown-menu-toggle">' . $icon . '</span>';
			}

			if ( ! empty( $current_page ) ) {
				$_current_page = get_post( $current_page );
				if ( $_current_page && in_array( $page->ID, $_current_page->ancestors ) ) {
					$css_class[] = 'current-menu-ancestor';
				}

				if ( $page->ID == $current_page ) { // phpcs:ignore
					$css_class[] = 'current-menu-item';
				} elseif ( $_current_page && $page->ID == $_current_page->post_parent ) { // phpcs:ignore
					$css_class[] = 'current-menu-parent';
				}
			} elseif ( $page->ID == get_option( 'page_for_posts' ) ) { // phpcs:ignore
				$css_class[] = 'current-menu-parent';
			}

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter name.
			$css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

			$args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
			$args['link_after'] = empty( $args['link_after'] ) ? '' : $args['link_after'];

			$output .= sprintf(
				'<li class="%s"><a href="%s">%s%s%s%s</a>',
				$css_classes,
				get_permalink( $page->ID ),
				$args['link_before'],
				apply_filters( 'the_title', $page->post_title, $page->ID ), // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter name.
				$args['link_after'],
				$button
			);
		}
	}
}

if ( ! function_exists( 'aureon_dropdown_icon_to_menu_link' ) ) {
	add_filter( 'nav_menu_item_title', 'aureon_dropdown_icon_to_menu_link', 10, 4 );
	/**
	 * Add dropdown icon if menu item has children.
	 *
	 * @since 1.3.42
	 *
	 * @param string   $title The menu item title.
	 * @param WP_Post  $item All of our menu item data.
	 * @param stdClass $args All of our menu item args.
	 * @param int      $depth Depth of menu item.
	 * @return string The menu item.
	 */
	function aureon_dropdown_icon_to_menu_link( $title, $item, $args, $depth ) {
		$role        = 'presentation';
		$tabindex    = '';
		$aria_label  = '';

		if ( 'click-arrow' === aureon_get_option( 'nav_dropdown_type' ) ) {
			$role = 'button';
			$tabindex = ' tabindex="0"';
			$aria_label = sprintf(
				' aria-label="%s"',
				esc_attr__( 'Open Sub-Menu', 'aureon' )
			);
		}

		if ( isset( $args->container_class ) && 'main-nav' === $args->container_class ) {
			foreach ( $item->classes as $value ) {
				if ( 'menu-item-has-children' === $value ) {
					$arrow_direction = 'down';

					if ( 'primary' === $args->theme_location ) {
						if ( 0 !== $depth ) {
							$arrow_direction = 'right';

							if ( 'left' === aureon_get_option( 'nav_dropdown_direction' ) ) {
								$arrow_direction = 'left';
							}
						}

						if ( 'nav-left-sidebar' === aureon_get_navigation_location() ) {
							$arrow_direction = 'right';

							if ( 'both-right' === aureon_get_layout() ) {
								$arrow_direction = 'left';
							}
						}

						if ( 'nav-right-sidebar' === aureon_get_navigation_location() ) {
							$arrow_direction = 'left';

							if ( 'both-left' === aureon_get_layout() ) {
								$arrow_direction = 'right';
							}
						}

						if ( 'hover' !== aureon_get_option( 'nav_dropdown_type' ) ) {
							$arrow_direction = 'down';
						}
					}

					$arrow_direction = apply_filters( 'aureon_menu_item_dropdown_arrow_direction', $arrow_direction, $args, $depth );

					if ( 'down' === $arrow_direction ) {
						$arrow_direction = '';
					} else {
						$arrow_direction = '-' . $arrow_direction;
					}

					$icon = aureon_get_svg_icon( 'arrow' . $arrow_direction );
					$title = $title . '<span role="' . $role . '" class="dropdown-menu-toggle"' . $tabindex . $aria_label . '>' . $icon . '</span>';
				}
			}
		}

		return $title;
	}
}

add_filter( 'nav_menu_link_attributes', 'aureon_set_menu_item_link_attributes', 10, 4 );
/**
 * Add attributes to the menu item link when using the Click - Menu Item option.
 *
 * @since 3.5.0
 *
 * @param array    $atts The menu item attributes.
 * @param WP_Post  $item The current menu item.
 * @param stdClass $args The menu item args.
 * @param int      $depth The depth of the menu item.
 * @return array The menu item attributes.
 */
function aureon_set_menu_item_link_attributes( $atts, $item, $args, $depth ) {
	if ( ! isset( $args->container_class ) || 'main-nav' !== $args->container_class ) {
		return $atts;
	}

	if ( 'click' !== aureon_get_option( 'nav_dropdown_type' ) ) {
		return $atts;
	}

	if ( in_array( 'menu-item-has-children', $item->classes, true ) ) {
		$atts['role'] = 'button';
		$atts['aria-expanded'] = 'false';
		$atts['aria-haspopup'] = 'true';
		$atts['aria-label'] = esc_attr__( 'Open Sub-Menu', 'aureon' );
	}

	return $atts;
}

if ( ! function_exists( 'aureon_navigation_search' ) ) {
	add_action( 'aureon_inside_navigation', 'aureon_navigation_search' );
	/**
	 * Add the search bar to the navigation.
	 *
	 * @since 1.1.4
	 */
	function aureon_navigation_search() {
		$aureon_settings = wp_parse_args(
			get_option( 'aureon_settings', array() ),
			aureon_get_defaults()
		);

		if ( 'enable' !== $aureon_settings['nav_search'] ) {
			return;
		}

		echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'aureon_navigation_search_output',
			sprintf(
				'<form method="get" class="search-form navigation-search" action="%1$s">
					<input type="search" class="search-field" value="%2$s" name="s" title="%3$s" />
				</form>',
				esc_url( home_url( '/' ) ),
				esc_attr( get_search_query() ),
				esc_attr_x( 'Search', 'label', 'aureon' )
			)
		);
	}
}

add_action( 'aureon_after_primary_menu', 'aureon_do_menu_bar_item_container' );
add_action( 'aureon_inside_mobile_menu_control_wrapper', 'aureon_do_menu_bar_item_container' );
/**
 * Add a container for menu bar items.
 *
 * @since 3.0.0
 */
function aureon_do_menu_bar_item_container() {
	if ( ! aureon_is_using_flexbox() ) {
		return;
	}

	if ( aureon_has_menu_bar_items() ) {
		echo '<div class="menu-bar-items">';
			do_action( 'aureon_menu_bar_items' );
		echo '</div>';
	}
}

add_action( 'wp', 'aureon_add_menu_bar_items' );
/**
 * Add menu bar items to the primary navigation.
 *
 * @since 3.0.0
 */
function aureon_add_menu_bar_items() {
	if ( ! aureon_is_using_flexbox() ) {
		return;
	}

	if ( 'enable' === aureon_get_option( 'nav_search' ) ) {
		add_action( 'aureon_menu_bar_items', 'aureon_do_navigation_search_button' );
	}

	if ( aureon_get_option( 'nav_search_modal' ) && 'flexbox' === aureon_get_option( 'structure' ) && 'svg' === aureon_get_option( 'icons' ) ) {
		add_action( 'aureon_menu_bar_items', 'aureon_do_search_modal_trigger' );
	}
}

/**
 * Add the navigation search button.
 *
 * @since 3.0.0
 */
function aureon_do_navigation_search_button() {
	if ( ! aureon_is_using_flexbox() ) {
		return;
	}

	if ( 'enable' !== aureon_get_option( 'nav_search' ) ) {
		return;
	}

	$search_item = apply_filters(
		'aureon_navigation_search_menu_item_output',
		sprintf(
			'<span class="menu-bar-item search-item"><a aria-label="%1$s" href="#">%2$s</a></span>',
			esc_attr__( 'Open Search Bar', 'aureon' ),
			aureon_get_svg_icon( 'search', true ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in function.
		)
	);

	echo $search_item; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- No escaping needed.
}

if ( ! function_exists( 'aureon_menu_search_icon' ) ) {
	add_filter( 'wp_nav_menu_items', 'aureon_menu_search_icon', 10, 2 );
	/**
	 * Add search icon to primary menu if set.
	 * Only used if using old float system.
	 *
	 * @since 1.2.9.7
	 *
	 * @param string   $nav The HTML list content for the menu items.
	 * @param stdClass $args An object containing wp_nav_menu() arguments.
	 * @return string The search icon menu item.
	 */
	function aureon_menu_search_icon( $nav, $args ) {
		$aureon_settings = wp_parse_args(
			get_option( 'aureon_settings', array() ),
			aureon_get_defaults()
		);

		if ( aureon_is_using_flexbox() ) {
			return $nav;
		}

		// If the search icon isn't enabled, return the regular nav.
		if ( 'enable' !== $aureon_settings['nav_search'] ) {
			return $nav;
		}

		// If our primary menu is set, add the search icon.
		if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
			$search_item = apply_filters(
				'aureon_navigation_search_menu_item_output',
				sprintf(
					'<li class="search-item menu-item-align-right"><a aria-label="%1$s" href="#">%2$s</a></li>',
					esc_attr__( 'Open Search Bar', 'aureon' ),
					aureon_get_svg_icon( 'search', true ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in function.
				)
			);

			return $nav . $search_item;
		}

		// Our primary menu isn't set, return the regular nav.
		// In this case, the search icon is added to the aureon_menu_fallback() function in navigation.php.
		return $nav;
	}
}

if ( ! function_exists( 'aureon_mobile_menu_search_icon' ) ) {
	add_action( 'aureon_inside_navigation', 'aureon_mobile_menu_search_icon' );
	/**
	 * Add search icon to mobile menu bar.
	 * Only used if using old float system.
	 *
	 * @since 1.3.12
	 */
	function aureon_mobile_menu_search_icon() {
		$aureon_settings = wp_parse_args(
			get_option( 'aureon_settings', array() ),
			aureon_get_defaults()
		);

		// If the search icon isn't enabled, return the regular nav.
		if ( 'enable' !== $aureon_settings['nav_search'] ) {
			return;
		}

		if ( aureon_is_using_flexbox() ) {
			return;
		}

		?>
		<div class="mobile-bar-items">
			<?php do_action( 'aureon_inside_mobile_menu_bar' ); ?>
			<span class="search-item">
				<a aria-label="<?php esc_attr_e( 'Open Search Bar', 'aureon' ); ?>" href="#">
					<?php aureon_do_svg_icon( 'search', true ); ?>
				</a>
			</span>
		</div>
		<?php
	}
}

add_action( 'wp_footer', 'aureon_clone_sidebar_navigation' );
/**
 * Clone our sidebar navigation and place it below the header.
 * This places our mobile menu in a more user-friendly location.
 *
 * We're not using wp_add_inline_script() as this needs to happens
 * before menu.js is enqueued.
 *
 * @since 2.0
 */
function aureon_clone_sidebar_navigation() {
	if ( 'nav-left-sidebar' !== aureon_get_navigation_location() && 'nav-right-sidebar' !== aureon_get_navigation_location() ) {
		return;
	}
	?>
	<script>
		var target, nav, clone;
		nav = document.getElementById( 'site-navigation' );
		if ( nav ) {
			clone = nav.cloneNode( true );
			clone.className += ' sidebar-nav-mobile';
			clone.setAttribute( 'aria-label', '<?php esc_attr_e( 'Mobile Menu', 'aureon' ); ?>' );
			target = document.getElementById( 'masthead' );
			if ( target ) {
				target.insertAdjacentHTML( 'afterend', clone.outerHTML );
			} else {
				document.body.insertAdjacentHTML( 'afterbegin', clone.outerHTML )
			}
		}
	</script>
	<?php
}
