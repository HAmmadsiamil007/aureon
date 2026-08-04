<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'aureon_dashboard_inside_container', 'aureon_do_dashboard_tabs', 5 );
add_action( 'aureon_inside_site_library_container', 'aureon_do_dashboard_tabs', 5 );
add_action( 'aureon_before_site_library', 'aureon_do_dashboard_tabs', 5 );
/**
 * Adds our tabs to the Aureon dashboard.
 *
 * @since 1.6
 */
function aureon_do_dashboard_tabs() {
	if ( ! defined( 'AUREON_VERSION' ) ) {
		return;
	}

	$screen = get_current_screen();

	$tabs = apply_filters( 'aureon_dashboard_tabs', array(
		'Modules' => array(
			'name' => __( 'Modules', 'aureon-studio' ),
			'url' => admin_url( 'themes.php?page=aureon-options' ),
			'class' => 'appearance_page_aureon-options' === $screen->id ? 'active' : '',
		),
	) );

	// Don't print any markup if we only have one tab.
	if ( count( $tabs ) === 1 ) {
		return;
	}
	?>
	<div class="aureon-dashboard-tabs">
		<?php
		foreach ( $tabs as $tab ) {
			printf( '<a href="%1$s" class="%2$s">%3$s</a>',
				esc_url( $tab['url'] ),
				esc_attr( $tab['class'] ),
				esc_html( $tab['name'] )
			);
		}
		?>
	</div>
	<?php
}
