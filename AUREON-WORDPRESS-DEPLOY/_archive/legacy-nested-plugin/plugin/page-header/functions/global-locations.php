<?php
defined( 'WPINC' ) or die;

class Aureon_Page_Header_Locations {
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		add_submenu_page(
			function_exists( 'aureon_premium_do_elements' ) ? 'themes.php' : 'edit.php?post_type=aureon_page_header',
			__( 'Global Locations', 'aureon-studio' ),
			__( 'Global Locations', 'aureon-studio' ),
			'manage_options',
			'page-header-global-locations',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( 'aureon_page_header_global_locations' );
		?>
		<div class="wrap">
			<h1><?php _e( 'Global Locations', 'aureon-studio' ); ?></h1>
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'page_header_global_locations' );
				do_settings_sections( 'page-header-global-locations' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			'page_header_global_locations',
			'aureon_page_header_global_locations',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'page_header_global_location_section',
			'',
			'',
			'page-header-global-locations'
		);

		add_settings_field(
			'aureon_page_header_location_blog',
			__( 'Posts Page (blog)', 'aureon-studio' ),
			array( $this, 'post_type_select' ),
			'page-header-global-locations',
			'page_header_global_location_section',
			'blog'
		);

		add_settings_field(
			'aureon_page_header_location_search_results',
			__( 'Search Results', 'aureon-studio' ),
			array( $this, 'post_type_select' ),
			'page-header-global-locations',
			'page_header_global_location_section',
			'search_results'
		);

		add_settings_field(
			'aureon_page_header_location_404',
			__( '404 Template', 'aureon-studio' ),
			array( $this, 'post_type_select' ),
			'page-header-global-locations',
			'page_header_global_location_section',
			'404'
		);

		add_settings_section(
			'page_header_cpt_single_section',
			__( 'Post Types - Single', 'aureon-studio' ),
			'',
			'page-header-global-locations'
		);

		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		foreach( $post_types as $type ) {
			add_settings_field(
				'aureon_page_header_location_' . $type->name,
				$type->label,
				array( $this, 'post_type_select' ),
				'page-header-global-locations',
				'page_header_cpt_single_section',
				$type->name
			);
		}

		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $post_types['attachment'] );
		unset( $post_types['page'] );
		unset( $post_types['post'] );

		if ( count( $post_types ) > 0 ) {
			add_settings_section(
				'page_header_cpt_archives_section',
				__( 'Post Types - Archives', 'aureon-studio' ),
				'',
				'page-header-global-locations'
			);
		}

		foreach( $post_types as $type ) {
			add_settings_field(
				'aureon_page_header_location_' . $type->name . '_archives',
				$type->label,
				array( $this, 'post_type_select' ),
				'page-header-global-locations',
				'page_header_cpt_archives_section',
				$type->name . '_archives'
			);
		}

		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

		if ( count( $taxonomies ) > 0 ) {
			add_settings_section(
				'page_header_taxonomies_section',
				__( 'Taxonomies - Archives', 'aureon-studio' ),
				'',
				'page-header-global-locations'
			);
		}

		foreach( $taxonomies as $type ) {
			add_settings_field(
				'aureon_page_header_location_' . $type->name,
				$type->label,
				array( $this, 'post_type_select' ),
				'page-header-global-locations',
				'page_header_taxonomies_section',
				$type->name
			);
		}
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		// Initialize the new array that will hold the sanitize values
		$new_input = array();

		// Loop through the input and sanitize each of the values
		if ( is_array( $input ) || is_object( $input ) ) {
			foreach ( $input as $key => $val ) {
				$new_input[ $key ] = absint( $val );
			}
		}

		return $new_input;
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function post_type_select( $type ) {
		$options = wp_parse_args(
			get_option( 'aureon_page_header_global_locations', array() ),
			''
		);
		?>
		<select id="<?php echo $type;?>" name="aureon_page_header_global_locations[<?php echo $type;?>]">
			<option value=""></option>
			<?php
			$page_headers = get_posts(array(
				'posts_per_page' => -1,
				'orderby' => 'title',
				'post_type' => 'aureon_page_header',
				'suppress_filters' => false,
			));

			$options[ $type ] = ! isset( $options[ $type ] ) ? '' : $options[ $type ];

			foreach( $page_headers as $header ) {
				printf( '<option value="%1$s" %2$s>%3$s</option>',
					$header->ID,
					selected( $options[ $type ], $header->ID ),
					$header->post_title
				);
			}
			?>
		</select>
		<?php
	}
}

if ( is_admin() ) {
	$aureon_page_header_locations = new Aureon_Page_Header_Locations();
}
