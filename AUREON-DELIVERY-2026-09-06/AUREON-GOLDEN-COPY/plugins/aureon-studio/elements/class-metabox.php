<?php
/**
 * This file builds all the options for our Elements.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Start our Elements metabox class.
 */
class Aureon_Elements_Metabox {
	/**
	 * Instance.
	 *
	 * @since 1.7
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 1.7
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Build it.
	 *
	 * @since 1.7
	 */
	public function __construct() {
		add_action( 'admin_body_class', array( $this, 'body_class' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'wp_ajax_aureon_elements_get_location_terms', array( $this, 'get_terms' ) );
		add_action( 'wp_ajax_aureon_elements_get_location_posts', array( $this, 'get_posts' ) );
		add_action( 'wp_ajax_aureon_elements_get_location_objects', array( $this, 'get_all_objects' ) );
	}

	/**
	 * Check the current element type.
	 *
	 * @since 1.11.0
	 */
	public static function get_element_type() {
		$element_type = get_post_meta( get_the_ID(), '_aureon_element_type', true );

		if ( ! $element_type && isset( $_GET['element_type'] ) ) { // phpcs:ignore -- No processing happening.
			$element_type = esc_html( $_GET['element_type'] ); // phpcs:ignore -- No processing happening.
		}

		if ( ! $element_type ) {
			$element_type = 'no-element-type';

			if ( function_exists( 'get_current_screen' ) ) {
				$current_screen = get_current_screen();

				if ( ! empty( $current_screen->is_block_editor ) ) {
					$element_type = 'block';
				}
			}
		}

		return $element_type;
	}

	/**
	 * Add a body class if we're using the Header element type.
	 * We do this so we can hide the Template Tags metabox for other types.
	 *
	 * @since 1.7
	 *
	 * @param string $classes Existing classes.
	 * @return string
	 */
	public function body_class( $classes ) {
		if ( 'aureon_elements' === get_post_type() ) {
			$element_type = self::get_element_type();

			if ( $element_type ) {
				$classes .= ' ' . $element_type . '-element-type';
			} else {
				$classes .= ' no-element-type';
			}

			if ( 'header' === $element_type && get_post_meta( get_the_ID(), '_aureon_element_content', true ) ) {
				$classes .= ' element-has-page-hero';
			}

			$block_type = get_post_meta( get_the_ID(), '_aureon_block_type', true );

			if ( $block_type ) {
				if ( 'content-template' === $block_type && get_post_meta( get_the_ID(), '_aureon_use_theme_post_container', true ) ) {
					$classes .= ' using-theme-post-container';
				}

				if ( ( 'archive-navigation-template' === $block_type || 'post-navigation-template' === $block_type ) && get_post_meta( get_the_ID(), '_aureon_use_archive_navigation_container', true ) ) {
					$classes .= ' using-theme-pagination-container';
				}

				$classes .= ' ' . $block_type . '-block-type';
			}
		}

		return $classes;
	}

	/**
	 * Enqueue any necessary scripts and styles.
	 *
	 * @since 1.7
	 *
	 * @param string $hook The current page hook.
	 */
	public function scripts( $hook ) {
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
			if ( 'aureon_elements' === get_post_type() ) {
				$element_type = get_post_meta( get_the_ID(), '_aureon_element_type', true );

				// Remove autosave when dealing with non-content Elements.
				// phpcs:ignore -- No data processing happening.
				if ( 'block' !== $element_type || ( isset( $_GET['element_type'] ) && 'block' !== $_GET['element_type'] ) ) {
					wp_dequeue_script( 'autosave' );
				}

				$deps = array( 'jquery' );

				if ( function_exists( 'wp_enqueue_code_editor' ) ) {
					$settings = wp_enqueue_code_editor(
						array(
							'type'       => 'application/x-httpd-php',
							'codemirror' => array(
								'indentUnit' => 2,
								'tabSize'    => 2,
							),
						)
					);

					$deps[] = 'code-editor';
				} else {
					$settings = false;
				}

				$element_type = self::get_element_type();

				wp_enqueue_script( 'aureon-elements-metabox', plugin_dir_url( __FILE__ ) . 'assets/admin/metabox.js', $deps, AUREON_STUDIO_VERSION, true );

				wp_localize_script(
					'aureon-elements-metabox',
					'elements',
					array(
						'nonce' => wp_create_nonce( 'aureon-elements-location' ),
						'settings' => $settings ? wp_json_encode( $settings ) : false,
						'type' => $element_type,
						'custom_image' => __( 'Custom Image', 'aureon-studio' ),
						'fallback_image' => __( 'Fallback Image', 'aureon-studio' ),
						'choose' => __( 'Choose...', 'aureon-studio' ),
						'showID' => apply_filters( 'aureon_elements_show_object_ids', false ),
					)
				);

				wp_enqueue_style( 'aureon-elements-metabox', plugin_dir_url( __FILE__ ) . 'assets/admin/metabox.css', array(), AUREON_STUDIO_VERSION );
				wp_enqueue_style( 'aureon-elements-balloon', plugin_dir_url( __FILE__ ) . 'assets/admin/balloon.css', array(), AUREON_STUDIO_VERSION );

				wp_enqueue_media();
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker-alpha', AUREON_LIBRARY_DIRECTORY_URL . 'alpha-color-picker/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), '3.0.0', true );

				wp_add_inline_script(
					'wp-color-picker-alpha',
					'jQuery( function() { jQuery( ".color-picker" ).wpColorPicker(); } );'
				);

				if ( function_exists( 'wp_add_inline_script' ) && function_exists( 'aureon_get_default_color_palettes' ) ) {
					// Grab our palette array and turn it into JS.
					$palettes = wp_json_encode( aureon_get_default_color_palettes() );

					// Add our custom palettes.
					// json_encode takes care of escaping.
					wp_add_inline_script( 'wp-color-picker', 'jQuery.wp.wpColorPicker.prototype.options.palettes = ' . $palettes . ';' );
				}

				wp_enqueue_style( 'select2', AUREON_LIBRARY_DIRECTORY_URL . 'select2/select2.min.css', array(), '4.0.13' );
				wp_enqueue_script( 'select2', AUREON_LIBRARY_DIRECTORY_URL . 'select2/select2.full.min.js', array( 'jquery', 'aureon-elements-metabox' ), '4.0.13', true );

				$css = '';

				if ( function_exists( 'aureon_get_option' ) && 'separate-containers' === aureon_get_option( 'content_layout_setting' ) ) {
					$css .= 'body.left-sidebar-block-type .block-editor-block-list__layout.is-root-container, body.right-sidebar-block-type .block-editor-block-list__layout.is-root-container {background: ' . aureon_get_option( 'background_color' ) . ';}';
					$css .= 'body.content-template-block-type:not(.using-theme-post-container) .block-editor-block-list__layout.is-root-container, body.archive-navigation-template-block-type:not(.using-theme-pagination-container) .block-editor-block-list__layout.is-root-container {background: ' . aureon_get_option( 'background_color' ) . ';}';
				}

				wp_add_inline_style( 'aureon-elements-metabox', $css );
			}
		}
	}

	/**
	 * Register our metabox.
	 *
	 * @since 1.7
	 */
	public function register_metabox() {
		// Title not translated on purpose.
		add_meta_box( 'aureon_premium_elements', __( 'Display Rules', 'aureon-studio' ), array( $this, 'element_fields' ), 'aureon_elements', 'normal', 'high' );
		add_meta_box( 'aureon_page_hero_template_tags', __( 'Template Tags', 'aureon-studio' ), array( $this, 'template_tags' ), 'aureon_elements', 'side', 'low' );
		remove_meta_box( 'slugdiv', 'aureon_elements', 'normal' );
	}

	/**
	 * Output all of our metabox fields.
	 *
	 * @since 1.7
	 *
	 * @param object $post Our post object.
	 */
	public function element_fields( $post ) {
		wp_nonce_field( basename( __FILE__ ), 'aureon_elements_nonce' );

		$type = self::get_element_type();
		$page_hero_class = '';
		$merge = get_post_meta( get_the_ID(), '_aureon_site_header_merge', true );
		$conditions_set = get_post_meta( get_the_ID(), '_aureon_element_display_conditions', true );
		$post_status = get_post_status( get_the_ID() );

		if ( 'header' === $type && get_post_meta( get_the_ID(), '_aureon_element_content', true ) ) {
			$page_hero_class = ' has-page-hero';
		}
		?>
		<div class="aureon-element-type" style="display: none;">
			<select class="select-type" name="_aureon_element_type">
				<option <?php selected( $type, '' ); ?> value=""><?php esc_attr_e( 'Choose...', 'aureon-studio' ); ?></option>
				<option <?php selected( $type, 'block' ); ?> value="block"><?php esc_attr_e( 'Block', 'aureon-studio' ); ?></option>
				<option <?php selected( $type, 'header' ); ?> value="header"><?php esc_attr_e( 'Header', 'aureon-studio' ); ?></option>
				<option <?php selected( $type, 'hook' ); ?> value="hook"><?php esc_attr_e( 'Hook', 'aureon-studio' ); ?></option>
				<option <?php selected( $type, 'layout' ); ?> value="layout"><?php esc_attr_e( 'Layout', 'aureon-studio' ); ?></option>
			</select>
		</div>

		<?php if ( 'publish' === $post_status && ! $conditions_set && 'block' !== $type ) : ?>
			<div class="error notice inline elements-no-location-error" style="margin: 5px 0 10px;">
				<p><?php _e( 'This element needs a location set within the Display Rules tab in order to display.', 'aureon-studio' ); ?></p>
			</div>
		<?php endif; ?>

		<div class="element-settings <?php echo esc_html( $type . $page_hero_class ); ?>">
			<?php if ( 'hook' === $type || 'header' === $type ) : ?>
				<textarea id="aureon-element-content" name="_aureon_element_content"><?php echo esc_textarea( get_post_meta( get_the_ID(), '_aureon_element_content', true ) ); ?></textarea>
			<?php endif; ?>

			<ul class="element-metabox-tabs">
				<?php if ( 'header' === $type ) : ?>
					<li data-type="header" <?php echo 'header' === $type && '' === $page_hero_class ? 'class="is-selected" ' : ''; ?>data-tab="site-header">
						<a href="#">
							<?php _e( 'Site Header', 'aureon-studio' ); ?>
						</a>
					</li>

					<li data-type="header" <?php echo 'header' === $type && '' !== $page_hero_class ? 'class="is-selected" ' : ''; ?>data-tab="hero">
						<a href="#">
							<?php _e( 'Page Hero', 'aureon-studio' ); ?>
						</a>
					</li>
				<?php endif; ?>

				<li data-type="hook" <?php echo ( 'hook' === $type || 'block' === $type ) ? 'class="is-selected" ' : ''; ?>data-tab="hook-settings">
					<a href="#">
						<?php echo 'block' === $type ? esc_attr__( 'Rules', 'aureon-studio' ) : esc_attr__( 'Settings', 'aureon-studio' ); ?>
					</a>
				</li>

				<?php if ( 'layout' === $type ) : ?>
					<li data-type="layout" <?php echo 'layout' === $type ? 'class="is-selected" ' : ''; ?>data-tab="sidebars">
						<a href="#">
							<?php _e( 'Sidebar', 'aureon-studio' ); ?>
						</a>
					</li>

					<li data-type="layout" data-tab="footer-widgets">
						<a href="#">
							<?php _e( 'Footer', 'aureon-studio' ); ?>
						</a>
					</li>

					<?php if ( function_exists( 'aureon_disable_elements' ) ) : ?>
						<li data-type="layout" data-tab="disable-elements">
							<a href="#">
								<?php _e( 'Disable Elements', 'aureon-studio' ); ?>
							</a>
						</li>
					<?php endif; ?>

					<li data-type="layout" data-tab="content">
						<a href="#">
							<?php _e( 'Content', 'aureon-studio' ); ?>
						</a>
					</li>
				<?php endif; ?>

				<?php if ( 'block' !== $type ) : ?>
					<li data-tab="display-rules">
						<a href="#">
							<?php _e( 'Display Rules', 'aureon-studio' ); ?>
						</a>
					</li>
				<?php endif; ?>

				<li data-type="all" data-tab="internal-notes">
					<a href="#">
						<?php _e( 'Internal Notes', 'aureon-studio' ); ?>
					</a>
				</li>
			</ul>

			<table class="aureon-elements-settings" data-type="hook" data-tab="hook-settings">
				<tbody>
					<?php
					if ( 'hook' === $type ) :
						?>
						<tr id="hook-row" class="aureon-element-row hook-row <?php echo '' !== get_post_meta( get_the_ID(), '_aureon_block_type', true ) && 'hook' !== get_post_meta( get_the_ID(), '_aureon_block_type', true ) ? 'hide-hook-row' : ''; ?>">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hook"><?php _e( 'Hook', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<select id="_aureon_hook" name="_aureon_hook">
									<?php
									$hooks = self::get_available_hooks();

									foreach ( (array) $hooks as $group => $data ) {
										?>
										<optgroup label="<?php echo esc_html( $data['group'] ); ?>">
											<?php
											foreach ( $data['hooks'] as $val ) {
												printf(
													'<option value="%1$s" %2$s>%1$s</option>',
													esc_html( $val ),
													selected( get_post_meta( get_the_ID(), '_aureon_hook', true ), $val )
												);
											}
											?>
										</optgroup>
										<?php
									}
									?>
									<optgroup label="<?php esc_attr_e( 'Custom', 'aureon-studio' ); ?>">
										<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hook', true ), 'custom' ); ?> value="custom"><?php esc_attr_e( 'Custom Hook', 'aureon-studio' ); ?></option>
									</optgroup>
								</select>
							</td>
						</tr>

						<tr class="aureon-element-row custom-hook-name hook-row <?php echo '' !== get_post_meta( get_the_ID(), '_aureon_block_type', true ) && 'hook' !== get_post_meta( get_the_ID(), '_aureon_block_type', true ) ? 'hide-hook-row' : ''; ?>" <?php echo 'custom' !== get_post_meta( get_the_ID(), '_aureon_hook', true ) ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_custom_hook"><?php _e( 'Custom Hook Name', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="text" name="_aureon_custom_hook" id="_aureon_custom_hook" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_custom_hook', true ) ); ?>" />
							</td>
						</tr>
						<?php
					endif;

					if ( 'hook' === $type ) :
						?>
						<tr class="aureon-element-row disable-header-hook" <?php echo 'aureon_header' !== get_post_meta( get_the_ID(), '_aureon_hook', true ) ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_hook_disable_site_header"><?php _e( 'Disable Site Header', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" id="_aureon_hook_disable_site_header" name="_aureon_hook_disable_site_header" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_hook_disable_site_header', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row disable-footer-hook" <?php echo 'aureon_footer' !== get_post_meta( get_the_ID(), '_aureon_hook', true ) ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_hook_disable_site_footer"><?php _e( 'Disable Site Footer', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" id="_aureon_hook_disable_site_footer" name="_aureon_hook_disable_site_footer" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_hook_disable_site_footer', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hook_execute_shortcodes"><?php _e( 'Execute Shortcodes', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" id="_aureon_hook_execute_shortcodes" name="_aureon_hook_execute_shortcodes" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_hook_execute_shortcodes', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hook_execute_php"><?php _e( 'Execute PHP', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<?php if ( ! Aureon_Elements_Helper::should_execute_php() ) : ?>
									<?php
									printf(
										/* translators: %s is DISALLOW_FILE_EDIT constant */
										esc_html__( 'Unable to execute PHP as %s is defined.', 'aureon-studio' ),
										'<code>DISALLOW_FILE_EDIT</code>'
									);
									?>
								<?php else : ?>
									<input type="checkbox" id="_aureon_hook_execute_php" name="_aureon_hook_execute_php" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_hook_execute_php', true ), 'true' ); ?> />
								<?php endif; ?>
							</td>
						</tr>
						<?php
					endif;

					if ( 'hook' === $type ) :
						?>
						<tr class="aureon-element-row hook-row <?php echo '' !== get_post_meta( get_the_ID(), '_aureon_block_type', true ) && 'hook' !== get_post_meta( get_the_ID(), '_aureon_block_type', true ) ? 'hide-hook-row' : ''; ?>">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hook_priority"><?php _e( 'Priority', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="number" id="_aureon_hook_priority" name="_aureon_hook_priority" placeholder="10" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hook_priority', true ) ); ?>" />
							</td>
						</tr>
						<?php
					endif;
					?>

				</tbody>
			</table>

			<?php if ( 'header' === $type ) : ?>
				<table class="aureon-elements-settings" data-type="header" data-tab="hero">
					<tbody>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_custom_classes"><?php _e( 'Element Classes', 'aureon-studio' ); ?></label>
								<span class="tip" data-balloon="<?php esc_attr_e( 'Add custom classes to the Page Hero element.', 'aureon-studio' ); ?>" data-balloon-pos="down">?</span>
							</td>
							<td class="aureon-element-row-content">
								<input type="text" name="_aureon_hero_custom_classes" id="_aureon_hero_custom_classes" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hero_custom_classes', true ) ); ?>" />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_container"><?php _e( 'Container', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<select id="_aureon_hero_container" name="_aureon_hero_container">
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_container', true ), '' ); ?> value=""><?php esc_attr_e( 'Full Width', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_container', true ), 'contained' ); ?> value="contained"><?php echo esc_attr_x( 'Contained', 'Width', 'aureon-studio' ); ?></option>
								</select>
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_inner_container"><?php _e( 'Inner Container', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<select id="_aureon_hero_inner_container" name="_aureon_hero_inner_container">
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_inner_container', true ), '' ); ?> value=""><?php echo esc_attr_x( 'Contained', 'Width', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_inner_container', true ), 'full-width' ); ?> value="full-width"><?php esc_attr_e( 'Full Width', 'aureon-studio' ); ?></option>
								</select>
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_horizontal_alignment"><?php _e( 'Horizontal Alignment', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<select id="_aureon_hero_horizontal_alignment" name="_aureon_hero_horizontal_alignment">
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_horizontal_alignment', true ), '' ); ?> value=""><?php esc_attr_e( 'Left', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_horizontal_alignment', true ), 'center' ); ?> value="center"><?php esc_attr_e( 'Center', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_horizontal_alignment', true ), 'right' ); ?> value="right"><?php esc_attr_e( 'Right', 'aureon-studio' ); ?></option>
								</select>
							</td>
						</tr>

						<tr class="aureon-element-row requires-header-merge" <?php echo ! $merge ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_full_screen"><?php _e( 'Full Screen', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" name="_aureon_hero_full_screen" id="_aureon_hero_full_screen" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_hero_full_screen', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row requires-full-screen" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_aureon_hero_full_screen', true ) ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_vertical_alignment"><?php _e( 'Vertical Alignment', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<select id="_aureon_hero_vertical_alignment" name="_aureon_hero_vertical_alignment">
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_vertical_alignment', true ), '' ); ?> value=""><?php esc_attr_e( 'Top', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_vertical_alignment', true ), 'center' ); ?> value="center"><?php esc_attr_e( 'Center', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_vertical_alignment', true ), 'bottom' ); ?> value="bottom"><?php esc_attr_e( 'Bottom', 'aureon-studio' ); ?></option>
								</select>
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_padding"><?php _e( 'Padding', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<div class="responsive-controls">
									<a href="#" class="is-selected" data-control="desktop"><span class="dashicons dashicons-desktop"></span></a>
									<a href="#" data-control="mobile"><span class="dashicons dashicons-smartphone"></span></a>
								</div>

								<div class="padding-container desktop">
									<div class="padding-element">
										<div class="padding-element-options">
											<input type="number" id="_aureon_hero_padding_top" name="_aureon_hero_padding_top" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hero_padding_top', true ) ); ?>" />

											<select id="_aureon_hero_padding_top_unit" name="_aureon_hero_padding_top_unit">
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_top_unit', true ), '' ); ?> value="">px</option>
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_top_unit', true ), '%' ); ?> value="%">%</option>
											</select>
										</div>

										<span><?php esc_html_e( 'Top', 'aureon-studio' ); ?></span>
									</div>

									<div class="padding-element">
										<div class="padding-element-options">
											<input type="number" id="_aureon_hero_padding_right" name="_aureon_hero_padding_right" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hero_padding_right', true ) ); ?>" />

											<select id="_aureon_hero_padding_right_unit" name="_aureon_hero_padding_right_unit">
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_right_unit', true ), '' ); ?> value="">px</option>
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_right_unit', true ), '%' ); ?> value="%">%</option>
											</select>
										</div>

										<span><?php esc_html_e( 'Right', 'aureon-studio' ); ?></span>
									</div>

									<div class="padding-element">
										<div class="padding-element-options">
											<input type="number" id="_aureon_hero_padding_bottom" name="_aureon_hero_padding_bottom" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hero_padding_bottom', true ) ); ?>" />

											<select id="_aureon_hero_padding_bottom_unit" name="_aureon_hero_padding_bottom_unit">
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_bottom_unit', true ), '' ); ?> value="">px</option>
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_bottom_unit', true ), '%' ); ?> value="%">%</option>
											</select>
										</div>

										<span><?php esc_html_e( 'Bottom', 'aureon-studio' ); ?></span>
									</div>

									<div class="padding-element">
										<div class="padding-element-options">
											<input type="number" id="_aureon_hero_padding_left" name="_aureon_hero_padding_left" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hero_padding_left', true ) ); ?>" />

											<select id="_aureon_hero_padding_left_unit" name="_aureon_hero_padding_left_unit">
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_left_unit', true ), '' ); ?> value="">px</option>
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_left_unit', true ), '%' ); ?> value="%">%</option>
											</select>
										</div>

										<span><?php esc_html_e( 'Left', 'aureon-studio' ); ?></span>
									</div>
								</div>

								<div class="padding-container mobile" style="display: none;">
									<div class="padding-element">
										<div class="padding-element-options">
											<input type="number" id="_aureon_hero_padding_top_mobile" name="_aureon_hero_padding_top_mobile" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hero_padding_top_mobile', true ) ); ?>" />

											<select id="_aureon_hero_padding_top_unit_mobile" name="_aureon_hero_padding_top_unit_mobile">
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_top_unit_mobile', true ), '' ); ?> value="">px</option>
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_top_unit_mobile', true ), '%' ); ?> value="%">%</option>
											</select>
										</div>

										<span><?php esc_html_e( 'Top', 'aureon-studio' ); ?></span>
									</div>

									<div class="padding-element">
										<div class="padding-element-options">
											<input type="number" id="_aureon_hero_padding_right_mobile" name="_aureon_hero_padding_right_mobile" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hero_padding_right_mobile', true ) ); ?>" />

											<select id="_aureon_hero_padding_right_unit_mobile" name="_aureon_hero_padding_right_unit_mobile">
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_right_unit_mobile', true ), '' ); ?> value="">px</option>
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_right_unit_mobile', true ), '%' ); ?> value="%">%</option>
											</select>
										</div>

										<span><?php esc_html_e( 'Right', 'aureon-studio' ); ?></span>
									</div>

									<div class="padding-element">
										<div class="padding-element-options">
											<input type="number" id="_aureon_hero_padding_bottom_mobile" name="_aureon_hero_padding_bottom_mobile" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hero_padding_bottom_mobile', true ) ); ?>" />

											<select id="_aureon_hero_padding_bottom_unit_mobile" name="_aureon_hero_padding_bottom_unit_mobile">
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_bottom_unit_mobile', true ), '' ); ?> value="">px</option>
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_bottom_unit_mobile', true ), '%' ); ?> value="%">%</option>
											</select>
										</div>

										<span><?php esc_html_e( 'Bottom', 'aureon-studio' ); ?></span>
									</div>

									<div class="padding-element">
										<div class="padding-element-options">
											<input type="number" id="_aureon_hero_padding_left_mobile" name="_aureon_hero_padding_left_mobile" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hero_padding_left_mobile', true ) ); ?>" />

											<select id="_aureon_hero_padding_left_unit_mobile" name="_aureon_hero_padding_left_unit_mobile">
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_left_unit_mobile', true ), '' ); ?> value="">px</option>
												<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_padding_left_unit_mobile', true ), '%' ); ?> value="%">%</option>
											</select>
										</div>

										<span><?php esc_html_e( 'Left', 'aureon-studio' ); ?></span>
									</div>
								</div>
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_background_image"><?php _e( 'Background Image', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<select id="_aureon_hero_background_image" name="_aureon_hero_background_image">
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_background_image', true ), '' ); ?> value=""><?php esc_attr_e( 'No Background Image', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_background_image', true ), 'featured-image' ); ?> value="featured-image"><?php esc_attr_e( 'Featured Image', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_background_image', true ), 'custom-image' ); ?> value="custom-image"><?php esc_attr_e( 'Custom Image', 'aureon-studio' ); ?></option>
								</select>
							</td>
						</tr>

						<?php
						$background_image = get_post_meta( get_the_ID(), '_aureon_hero_background_image', true );

						if ( 'custom-image' === $background_image ) {
							$image_text = __( 'Custom Image', 'aureon-studio' );
						} else {
							$image_text = __( 'Fallback Image', 'aureon-studio' );
						}
						?>

						<tr class="aureon-element-row requires-background-image" <?php echo '' === $background_image ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_background_link_color_hover"><span class="image-text"><?php echo esc_html( $image_text ); ?></span></label>
							</td>
							<td class="aureon-element-row-content">
								<div class="change-featured-image" <?php echo ! has_post_thumbnail() ? 'style="display: none;"' : ''; ?>>
									<div class="image-preview">
										<?php the_post_thumbnail( 'thumbnail', array( 9999, 50 ) ); ?>
									</div>
									<?php
									printf(
										'<a class="button" href="#">%s</a>',
										esc_html__( 'Change', 'aureon-studio' )
									);

									printf(
										' <a class="button remove-image" href="#">%s</a>',
										esc_html__( 'Remove', 'aureon-studio' )
									);
									?>
								</div>

								<div class="set-featured-image" <?php echo has_post_thumbnail() ? 'style="display: none;"' : ''; ?>>
									<?php
									printf(
										'<a class="button" href="#">%s</a>',
										sprintf(
											/* translators: Upload Custom Image or Fallback image */
											esc_html__( 'Upload %s', 'aureon-studio' ),
											'<span class="image-text">' . esc_html( $image_text ) . '</span>'
										)
									);
									?>
								</div>
							</td>
						</tr>

						<tr class="aureon-element-row requires-background-image" <?php echo ! $background_image ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_background_position"><?php _e( 'Background Position', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<select id="_aureon_hero_background_position" name="_aureon_hero_background_position">
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_background_position', true ), '' ); ?> value="">left top</option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_background_position', true ), 'left center' ); ?> value="left center" <?php echo get_post_meta( get_the_ID(), '_aureon_hero_background_parallax', true ) ? 'disabled' : ''; ?>>left center</option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_background_position', true ), 'left bottom' ); ?> value="left bottom" <?php echo get_post_meta( get_the_ID(), '_aureon_hero_background_parallax', true ) ? 'disabled' : ''; ?>>left bottom</option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_background_position', true ), 'right top' ); ?> value="right top">right top</option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_background_position', true ), 'right center' ); ?> value="right center" <?php echo get_post_meta( get_the_ID(), '_aureon_hero_background_parallax', true ) ? 'disabled' : ''; ?>>right center</option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_background_position', true ), 'right bottom' ); ?> value="right bottom" <?php echo get_post_meta( get_the_ID(), '_aureon_hero_background_parallax', true ) ? 'disabled' : ''; ?>>right bottom</option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_background_position', true ), 'center top' ); ?> value="center top">center top</option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_background_position', true ), 'center center' ); ?> value="center center" <?php echo get_post_meta( get_the_ID(), '_aureon_hero_background_parallax', true ) ? 'disabled' : ''; ?>>center center</option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_hero_background_position', true ), 'center bottom' ); ?> value="center bottom" <?php echo get_post_meta( get_the_ID(), '_aureon_hero_background_parallax', true ) ? 'disabled' : ''; ?>>center bottom</option>
								</select>
							</td>
						</tr>

						<tr class="aureon-element-row requires-background-image" <?php echo ! $background_image ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_background_parallax"><?php _e( 'Parallax', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" name="_aureon_hero_background_parallax" id="_aureon_hero_background_parallax" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_hero_background_parallax', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row requires-background-image" <?php echo ! $background_image ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_disable_featured_image"><?php _e( 'Disable Featured Image', 'aureon-studio' ); ?></label>
								<span class="tip" data-balloon="<?php esc_attr_e( 'Disable the featured image on posts with this hero area.', 'aureon-studio' ); ?>" data-balloon-pos="down">?</span>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" name="_aureon_hero_disable_featured_image" id="_aureon_hero_disable_featured_image" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_hero_disable_featured_image', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row requires-background-image" <?php echo ! $background_image ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_background_overlay"><?php _e( 'Background Overlay', 'aureon-studio' ); ?></label>
								<span class="tip" data-balloon="<?php esc_attr_e( 'Use the background color as a background overlay.', 'aureon-studio' ); ?>" data-balloon-pos="down">?</span>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" name="_aureon_hero_background_overlay" id="_aureon_hero_background_overlay" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_hero_background_overlay', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_background_color"><?php _e( 'Background Color', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input class="color-picker" data-alpha-enabled="true" data-alpha-color-type="hex" type="text" name="_aureon_hero_background_color" id="_aureon_hero_background_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hero_background_color', true ) ); ?>" />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_text_color"><?php _e( 'Text Color', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input class="color-picker" type="text" name="_aureon_hero_text_color" id="_aureon_hero_text_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hero_text_color', true ) ); ?>" />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_link_color"><?php _e( 'Link Color', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input class="color-picker" type="text" name="_aureon_hero_link_color" id="_aureon_hero_link_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hero_link_color', true ) ); ?>" />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_hero_background_link_color_hover"><?php _e( 'Link Color Hover', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input class="color-picker" type="text" name="_aureon_hero_background_link_color_hover" id="_aureon_hero_background_link_color_hover" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_hero_background_link_color_hover', true ) ); ?>" />
							</td>
						</tr>

					</tbody>
				</table>

				<table class="aureon-elements-settings" data-type="header" data-tab="site-header">
					<tbody>
						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_site_header_merge"><?php _e( 'Merge with Content', 'aureon-studio' ); ?></label>
								<span class="tip" data-balloon="<?php esc_attr_e( 'Place your site header on top of the element below it.', 'aureon-studio' ); ?>" data-balloon-pos="down">?</span>
							</td>
							<td class="aureon-element-row-content">
								<select id="_aureon_site_header_merge" name="_aureon_site_header_merge">
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_site_header_merge', true ), '' ); ?> value=""><?php esc_attr_e( 'No Merge', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_site_header_merge', true ), 'merge' ); ?> value="merge"><?php esc_attr_e( 'Merge', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_site_header_merge', true ), 'merge-desktop' ); ?> value="merge-desktop"><?php esc_attr_e( 'Merge on Desktop Only', 'aureon-studio' ); ?></option>
								</select>
							</td>
						</tr>

						<tr class="aureon-element-row requires-header-merge" <?php echo ! $merge ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_site_header_height"><?php _e( 'Offset Site Header Height', 'aureon-studio' ); ?></label>
								<span class="tip" data-balloon="<?php esc_attr_e( 'Add to the top padding of your Page Hero to prevent overlapping.', 'aureon-studio' ); ?>" data-balloon-pos="down">?</span>
							</td>
							<td class="aureon-element-row-content">

								<div class="responsive-controls single-responsive-value">
									<a href="#" class="is-selected" data-control="desktop"><span class="dashicons dashicons-desktop"></span></a>
									<a href="#" data-control="mobile"><span class="dashicons dashicons-smartphone"></span></a>
								</div>

								<div class="padding-container single-value-padding-container desktop">
									<div class="padding-element">
										<input type="number" id="_aureon_site_header_height" name="_aureon_site_header_height" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_site_header_height', true ) ); ?>" />
										<span class="unit">px</span>
									</div>
								</div>

								<div class="padding-container single-value-padding-container mobile" style="display: none;">
									<div class="padding-element">
										<input type="number" id="_aureon_site_header_height_mobile" name="_aureon_site_header_height_mobile" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_site_header_height_mobile', true ) ); ?>" />
										<span class="unit">px</span>
									</div>
								</div>
							</td>
						</tr>

						<tr class="aureon-element-row requires-header-merge" <?php echo ! $merge ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_site_header_background_color"><?php _e( 'Header Background', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input class="color-picker" data-alpha-enabled="true" data-alpha-color-type="hex" type="text" name="_aureon_site_header_background_color" id="_aureon_site_header_background_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_site_header_background_color', true ) ); ?>" />
							</td>
						</tr>

						<?php if ( Aureon_Elements_Helper::does_option_exist( 'site-title' ) ) : ?>
							<tr class="aureon-element-row requires-header-merge" <?php echo ! $merge ? 'style="display: none;"' : ''; ?>>
								<td class="aureon-element-row-heading">
									<label for="_aureon_site_header_title_color"><?php _e( 'Site Title', 'aureon-studio' ); ?></label>
								</td>
								<td class="aureon-element-row-content">
									<input class="color-picker" type="text" name="_aureon_site_header_title_color" id="_aureon_site_header_title_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_site_header_title_color', true ) ); ?>" />
								</td>
							</tr>
						<?php endif; ?>

						<?php if ( Aureon_Elements_Helper::does_option_exist( 'site-tagline' ) ) : ?>
							<tr class="aureon-element-row requires-header-merge" <?php echo ! $merge ? 'style="display: none;"' : ''; ?>>
								<td class="aureon-element-row-heading">
									<label for="_aureon_site_header_tagline_color"><?php _e( 'Site Tagline', 'aureon-studio' ); ?></label>
								</td>
								<td class="aureon-element-row-content">
									<input class="color-picker" type="text" name="_aureon_site_header_tagline_color" id="_aureon_site_header_tagline_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_site_header_tagline_color', true ) ); ?>" />
								</td>
							</tr>
						<?php endif; ?>

						<?php if ( Aureon_Elements_Helper::does_option_exist( 'site-logo' ) ) : ?>
							<tr class="aureon-element-row">
								<td class="aureon-element-row-heading">
									<label for="_aureon_site_header_merge"><?php _e( 'Site Logo', 'aureon-studio' ); ?></label>
								</td>
								<td class="aureon-element-row-content">
									<div class="media-container">
										<div class="aureon-media-preview">
											<?php
											$site_logo_id = get_post_meta( get_the_ID(), '_aureon_site_logo', true );
											$site_logo_url = wp_get_attachment_url( absint( $site_logo_id ) );

											if ( $site_logo_url ) {
												echo '<img src="' . esc_url( $site_logo_url ) . '" alt="" height="30" />';
											}
											?>
										</div>
										<input type="button" data-title="<?php esc_attr_e( 'Site Logo', 'aureon-studio' ); ?>" data-preview="true" class="button aureon-upload-file" value="<?php esc_attr_e( 'Upload', 'aureon-studio' ); ?>" />
										<input type="button" class="button remove-field" value="<?php esc_attr_e( 'Remove', 'aureon-studio' ); ?>" <?php echo ! $site_logo_id ? 'style="display: none;"' : ''; ?> />

										<input type="hidden" class="media-field" name="_aureon_site_logo" value="<?php echo esc_attr( $site_logo_id ); ?>" />
									</div>
								</td>
							</tr>
						<?php endif; ?>

						<?php if ( Aureon_Elements_Helper::does_option_exist( 'retina-logo' ) ) : ?>
							<tr class="aureon-element-row">
								<td class="aureon-element-row-heading">
									<label for="_aureon_retina_logo"><?php _e( 'Retina Logo', 'aureon-studio' ); ?></label>
								</td>
								<td class="aureon-element-row-content">
									<div class="media-container">
										<div class="aureon-media-preview">
											<?php
											$retina_site_logo_id = get_post_meta( get_the_ID(), '_aureon_retina_logo', true );
											$retina_site_logo_url = wp_get_attachment_url( absint( $retina_site_logo_id ) );

											if ( $retina_site_logo_url ) {
												echo '<img src="' . esc_url( $retina_site_logo_url ) . '" alt="" height="30" />';
											}
											?>
										</div>
										<input type="button" data-title="<?php esc_attr_e( 'Retina Logo', 'aureon-studio' ); ?>" data-preview="true" class="button aureon-upload-file" value="<?php esc_attr_e( 'Upload', 'aureon-studio' ); ?>" />
										<input type="button" class="button remove-field" value="<?php esc_attr_e( 'Remove', 'aureon-studio' ); ?>" <?php echo ! $retina_site_logo_id ? 'style="display: none;"' : ''; ?> />

										<input type="hidden" class="media-field" name="_aureon_retina_logo" value="<?php echo esc_attr( $retina_site_logo_id ); ?>" />
									</div>
								</td>
							</tr>
						<?php endif; ?>

						<?php if ( Aureon_Elements_Helper::does_option_exist( 'navigation-logo' ) ) : ?>
							<tr class="aureon-element-row">
								<td class="aureon-element-row-heading">
									<label for="_aureon_navigation_logo"><?php _e( 'Navigation Logo', 'aureon-studio' ); ?></label>
								</td>
								<td class="aureon-element-row-content">
									<div class="media-container">
										<div class="aureon-media-preview">
											<?php
											$navigation_logo_id = get_post_meta( get_the_ID(), '_aureon_navigation_logo', true );
											$navigation_logo_url = wp_get_attachment_url( absint( $navigation_logo_id ) );

											if ( $navigation_logo_url ) {
												echo '<img src="' . esc_url( $navigation_logo_url ) . '" alt="" height="30" />';
											}
											?>
										</div>
										<input type="button" data-title="<?php esc_attr_e( 'Retina Logo', 'aureon-studio' ); ?>" data-preview="true" class="button aureon-upload-file" value="<?php esc_attr_e( 'Upload', 'aureon-studio' ); ?>" />
										<input type="button" class="button remove-field" value="<?php esc_attr_e( 'Remove', 'aureon-studio' ); ?>" <?php echo ! $navigation_logo_id ? 'style="display: none;"' : ''; ?> />

										<input type="hidden" class="media-field" name="_aureon_navigation_logo" value="<?php echo esc_attr( $navigation_logo_id ); ?>" />
									</div>
								</td>
							</tr>
						<?php endif; ?>

						<?php if ( Aureon_Elements_Helper::does_option_exist( 'mobile-logo' ) ) : ?>
							<tr class="aureon-element-row">
								<td class="aureon-element-row-heading">
									<label for="_aureon_mobile_logo"><?php _e( 'Mobile Header Logo', 'aureon-studio' ); ?></label>
								</td>
								<td class="aureon-element-row-content">
									<div class="media-container">
										<div class="aureon-media-preview">
											<?php
											$mobile_logo_id = get_post_meta( get_the_ID(), '_aureon_mobile_logo', true );
											$mobile_logo_url = wp_get_attachment_url( absint( $mobile_logo_id ) );

											if ( $mobile_logo_url ) {
												echo '<img src="' . esc_url( $mobile_logo_url ) . '" alt="" height="30" />';
											}
											?>
										</div>
										<input type="button" data-title="<?php esc_attr_e( 'Retina Logo', 'aureon-studio' ); ?>" data-preview="true" class="button aureon-upload-file" value="<?php esc_attr_e( 'Upload', 'aureon-studio' ); ?>" />
										<input type="button" class="button remove-field" value="<?php esc_attr_e( 'Remove', 'aureon-studio' ); ?>" <?php echo ! $mobile_logo_id ? 'style="display: none;"' : ''; ?> />

										<input type="hidden" class="media-field" name="_aureon_mobile_logo" value="<?php echo esc_attr( $mobile_logo_id ); ?>" />
									</div>
								</td>
							</tr>
						<?php endif; ?>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_navigation_location"><?php _e( 'Navigation Location', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<select id="_aureon_navigation_location" name="_aureon_navigation_location">
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_navigation_location', true ), '' ); ?> value=""><?php esc_attr_e( 'Default', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_navigation_location', true ), 'nav-below-header' ); ?> value="nav-below-header"><?php esc_attr_e( 'Below Header', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_navigation_location', true ), 'nav-above-header' ); ?> value="nav-above-header"><?php esc_attr_e( 'Above Header', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_navigation_location', true ), 'nav-float-right' ); ?> value="nav-float-right"><?php esc_attr_e( 'Float Right', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_navigation_location', true ), 'nav-float-left' ); ?> value="nav-float-left"><?php esc_attr_e( 'Float Left', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_navigation_location', true ), 'nav-left-sidebar' ); ?> value="nav-left-sidebar"><?php esc_attr_e( 'Left Sidebar', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_navigation_location', true ), 'nav-right-sidebar' ); ?> value="nav-right-sidebar"><?php esc_attr_e( 'Right Sidebar', 'aureon-studio' ); ?></option>
									<option <?php selected( get_post_meta( get_the_ID(), '_aureon_navigation_location', true ), 'no-navigation' ); ?> value="no-navigation"><?php esc_attr_e( 'No Navigation', 'aureon-studio' ); ?></option>
								</select>
							</td>
						</tr>

						<tr class="aureon-element-row requires-header-merge" <?php echo ! $merge ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_navigation_colors"><?php _e( 'Navigation Colors', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" name="_aureon_navigation_colors" id="_aureon_navigation_colors" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_navigation_colors', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row requires-navigation-colors" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_aureon_navigation_colors', true ) ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_navigation_background_color"><?php _e( 'Navigation Background', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input class="color-picker" data-alpha-enabled="true" data-alpha-color-type="hex" type="text" name="_aureon_navigation_background_color" id="_aureon_navigation_background_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_navigation_background_color', true ) ); ?>" />
							</td>
						</tr>

						<tr class="aureon-element-row requires-navigation-colors" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_aureon_navigation_colors', true ) ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_navigation_text_color"><?php _e( 'Navigation Text', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input class="color-picker" type="text" name="_aureon_navigation_text_color" id="_aureon_navigation_text_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_navigation_text_color', true ) ); ?>" />
							</td>
						</tr>

						<tr class="aureon-element-row requires-navigation-colors" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_aureon_navigation_colors', true ) ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_navigation_background_color_hover"><?php _e( 'Navigation Background Hover', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input class="color-picker" data-alpha-enabled="true" data-alpha-color-type="hex" type="text" name="_aureon_navigation_background_color_hover" id="_aureon_navigation_background_color_hover" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_navigation_background_color_hover', true ) ); ?>" />
							</td>
						</tr>

						<tr class="aureon-element-row requires-navigation-colors" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_aureon_navigation_colors', true ) ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_navigation_text_color_hover"><?php _e( 'Navigation Text Hover', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input class="color-picker" type="text" name="_aureon_navigation_text_color_hover" id="_aureon_navigation_text_color_hover" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_navigation_text_color_hover', true ) ); ?>" />
							</td>
						</tr>

						<tr class="aureon-element-row requires-navigation-colors" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_aureon_navigation_colors', true ) ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_navigation_background_color_current"><?php _e( 'Navigation Background Current', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input class="color-picker" data-alpha-enabled="true" data-alpha-color-type="hex" type="text" name="_aureon_navigation_background_color_current" id="_aureon_navigation_background_color_current" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_navigation_background_color_current', true ) ); ?>" />
							</td>
						</tr>

						<tr class="aureon-element-row requires-navigation-colors" <?php echo ! $merge || ! get_post_meta( get_the_ID(), '_aureon_navigation_colors', true ) ? 'style="display: none;"' : ''; ?>>
							<td class="aureon-element-row-heading">
								<label for="_aureon_navigation_text_color_current"><?php _e( 'Navigation Text Current', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input class="color-picker" type="text" name="_aureon_navigation_text_color_current" id="_aureon_navigation_text_color_current" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_navigation_text_color_current', true ) ); ?>" />
							</td>
						</tr>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( 'layout' === $type ) : ?>
				<table class="aureon-elements-settings" data-type="layout" data-tab="sidebars">
					<tbody>
						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_sidebar_layout"><?php _e( 'Sidebar Layout', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<div class="layout-radio-item">
									<label for="default-sidebar-layout">
										<input type="radio" name="_aureon_sidebar_layout" id="default-sidebar-layout" <?php checked( get_post_meta( get_the_ID(), '_aureon_sidebar_layout', true ), '' ); ?> value="">
										<?php esc_html_e( 'Default', 'aureon-studio' ); ?>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="right-sidebar-layout" title="<?php esc_attr_e( 'Right Sidebar', 'aureon-studio' ); ?>">
										<input type="radio" name="_aureon_sidebar_layout" id="right-sidebar-layout" <?php checked( get_post_meta( get_the_ID(), '_aureon_sidebar_layout', true ), 'right-sidebar' ); ?> value="right-sidebar">
										<?php esc_html_e( 'Content', 'aureon-studio' ); ?> / <strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'aureon-studio' ); ?></strong>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="left-sidebar-layout" title="<?php esc_attr_e( 'Left Sidebar', 'aureon-studio' ); ?>">
										<input type="radio" name="_aureon_sidebar_layout" id="left-sidebar-layout" <?php checked( get_post_meta( get_the_ID(), '_aureon_sidebar_layout', true ), 'left-sidebar' ); ?> value="left-sidebar">
										<strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'aureon-studio' ); ?></strong> / <?php esc_html_e( 'Content', 'aureon-studio' ); ?>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="no-sidebar-layout" title="<?php esc_attr_e( 'No Sidebars', 'aureon-studio' ); ?>">
										<input type="radio" name="_aureon_sidebar_layout" id="no-sidebar-layout" <?php checked( get_post_meta( get_the_ID(), '_aureon_sidebar_layout', true ), 'no-sidebar' ); ?> value="no-sidebar">
										<?php esc_html_e( 'Content (no sidebars)', 'aureon-studio' ); ?>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="both-sidebars-layout" title="<?php esc_attr_e( 'Both Sidebars', 'aureon-studio' ); ?>">
										<input type="radio" name="_aureon_sidebar_layout" id="both-sidebars-layout" <?php checked( get_post_meta( get_the_ID(), '_aureon_sidebar_layout', true ), 'both-sidebars' ); ?> value="both-sidebars">
										<strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'aureon-studio' ); ?></strong> / <?php esc_html_e( 'Content', 'aureon-studio' ); ?> / <strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'aureon-studio' ); ?></strong>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="both-sidebars-left-layout" title="<?php esc_attr_e( 'Both Sidebars on Left', 'aureon-studio' ); ?>">
										<input type="radio" name="_aureon_sidebar_layout" id="both-sidebars-left-layout" <?php checked( get_post_meta( get_the_ID(), '_aureon_sidebar_layout', true ), 'both-left' ); ?> value="both-left">
										<strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'aureon-studio' ); ?></strong> / <strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'aureon-studio' ); ?></strong> / <?php esc_html_e( 'Content', 'aureon-studio' ); ?>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="both-sidebars-right-layout" title="<?php esc_attr_e( 'Both Sidebars on Right', 'aureon-studio' ); ?>">
										<input type="radio" name="_aureon_sidebar_layout" id="both-sidebars-right-layout" <?php checked( get_post_meta( get_the_ID(), '_aureon_sidebar_layout', true ), 'both-right' ); ?> value="both-right">
										<?php esc_html_e( 'Content', 'aureon-studio' ); ?> / <strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'aureon-studio' ); ?></strong> / <strong><?php echo esc_html_x( 'Sidebar', 'Short name for meta box', 'aureon-studio' ); ?></strong>
									</label>
								</div>
							</td>
						</tr>
					</tbody>
				</table>

				<table class="aureon-elements-settings" data-type="layout" data-tab="footer-widgets">
					<tbody>
						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_footer_widgets"><?php _e( 'Footer Widgets', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<div class="layout-radio-item">
									<label for="default-footer-widgets">
										<input type="radio" name="_aureon_footer_widgets" id="default-footer-widgets" <?php checked( get_post_meta( get_the_ID(), '_aureon_footer_widgets', true ), '' ); ?> value="">
										<?php esc_html_e( 'Default', 'aureon-studio' ); ?>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="footer-widget-0">
										<input type="radio" name="_aureon_footer_widgets" id="footer-widget-0" <?php checked( get_post_meta( get_the_ID(), '_aureon_footer_widgets', true ), 'no-widgets' ); ?> value="no-widgets">
										<?php esc_attr_e( '0 Widgets', 'aureon-studio' ); ?>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="footer-widget-1">
										<input type="radio" name="_aureon_footer_widgets" id="footer-widget-1" <?php checked( get_post_meta( get_the_ID(), '_aureon_footer_widgets', true ), '1' ); ?> value="1">
										<?php esc_attr_e( '1 Widget', 'aureon-studio' ); ?>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="footer-widget-2">
										<input type="radio" name="_aureon_footer_widgets" id="footer-widget-2" <?php checked( get_post_meta( get_the_ID(), '_aureon_footer_widgets', true ), '2' ); ?> value="2">
										<?php esc_attr_e( '2 Widgets', 'aureon-studio' ); ?>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="footer-widget-3">
										<input type="radio" name="_aureon_footer_widgets" id="footer-widget-3" <?php checked( get_post_meta( get_the_ID(), '_aureon_footer_widgets', true ), '3' ); ?> value="3">
										<?php esc_attr_e( '3 Widgets', 'aureon-studio' ); ?>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="footer-widget-4">
										<input type="radio" name="_aureon_footer_widgets" id="footer-widget-4" <?php checked( get_post_meta( get_the_ID(), '_aureon_footer_widgets', true ), '4' ); ?> value="4">
										<?php esc_attr_e( '4 Widgets', 'aureon-studio' ); ?>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="footer-widget-5">
										<input type="radio" name="_aureon_footer_widgets" id="footer-widget-5" <?php checked( get_post_meta( get_the_ID(), '_aureon_footer_widgets', true ), '5' ); ?> value="5">
										<?php esc_attr_e( '5 Widgets', 'aureon-studio' ); ?>
									</label>
								</div>
							</td>
						</tr>
					</tbody>
				</table>

				<table class="aureon-elements-settings" data-tab="disable-elements" data-type="layout">
					<tbody>
						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_disable_site_header"><?php _e( 'Site Header', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" name="_aureon_disable_site_header" id="_aureon_disable_site_header" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_disable_site_header', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_disable_mobile_header"><?php _e( 'Mobile Header', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" name="_aureon_disable_mobile_header" id="_aureon_disable_mobile_header" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_disable_mobile_header', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_disable_top_bar"><?php _e( 'Top Bar', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" name="_aureon_disable_top_bar" id="_aureon_disable_top_bar" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_disable_top_bar', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_disable_primary_navigation"><?php _e( 'Primary Navigation', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" name="_aureon_disable_primary_navigation" id="_aureon_disable_primary_navigation" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_disable_primary_navigation', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_disable_secondary_navigation"><?php _e( 'Secondary Navigation', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" name="_aureon_disable_secondary_navigation" id="_aureon_disable_secondary_navigation" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_disable_secondary_navigation', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_disable_featured_image"><?php _e( 'Featured Image', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" name="_aureon_disable_featured_image" id="_aureon_disable_featured_image" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_disable_featured_image', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_disable_content_title"><?php _e( 'Content Title', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" name="_aureon_disable_content_title" id="_aureon_disable_content_title" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_disable_content_title', true ), 'true' ); ?> />
							</td>
						</tr>

						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_disable_footer"><?php _e( 'Footer', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="checkbox" name="_aureon_disable_footer" id="_aureon_disable_footer" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_disable_footer', true ), 'true' ); ?> />
							</td>
						</tr>
					</tbody>
				</table>

				<table class="aureon-elements-settings" data-type="layout" data-tab="content">
					<tbody>
						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_content_area"><?php _e( 'Content Area', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<div class="layout-radio-item">
									<label for="default-container">
										<input type="radio" name="_aureon_content_area" id="default-container" <?php checked( get_post_meta( get_the_ID(), '_aureon_content_area', true ), '' ); ?> value="">
										<?php esc_html_e( 'Default', 'aureon-studio' ); ?>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="full-width-container">
										<input type="radio" name="_aureon_content_area" id="full-width-container" <?php checked( get_post_meta( get_the_ID(), '_aureon_content_area', true ), 'full-width' ); ?> value="full-width">
										<?php esc_html_e( 'Full Width (no padding)', 'aureon-studio' ); ?>
									</label>
								</div>

								<div class="layout-radio-item">
									<label for="contained-container">
										<input type="radio" name="_aureon_content_area" id="contained-container" <?php checked( get_post_meta( get_the_ID(), '_aureon_content_area', true ), 'contained' ); ?> value="contained">
										<?php echo esc_html_x( 'Contained (no padding)', 'Width', 'aureon-studio' ); ?>
									</label>
								</div>
							</td>
						</tr>
						<tr class="aureon-element-row">
							<td class="aureon-element-row-heading">
								<label for="_aureon_content_width"><?php _e( 'Content Width', 'aureon-studio' ); ?></label>
							</td>
							<td class="aureon-element-row-content">
								<input type="number" id="_aureon_content_width" name="_aureon_content_width" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_aureon_content_width', true ) ); ?>">
								<span class="unit">px</span>
							</td>
						</tr>
					</tbody>
				</table>
			<?php endif; ?>

			<table class="aureon-elements-settings" data-tab="display-rules" style="<?php echo 'block' !== $type ? 'display: none;' : ''; ?>">
				<tbody>
					<tr class="aureon-element-row">
						<td class="aureon-element-row-heading">
							<label><?php _e( 'Location', 'aureon-studio' ); ?></label>
							<span class="tip" data-balloon="<?php esc_attr_e( 'Choose when this element should display.', 'aureon-studio' ); ?>" data-balloon-pos="down">?</span>
						</td>
						<td class="aureon-element-row-content">
							<?php
							$display_conditionals = get_post_meta( get_the_ID(), '_aureon_element_display_conditions', true );
							$conditions = Aureon_Conditions::get_conditions();

							if ( $display_conditionals ) {
								foreach ( $display_conditionals as $field => $value ) {
									$type = explode( ':', $value['rule'] );

									if ( in_array( 'post', $type ) || in_array( 'taxonomy', $type ) ) {
										$show = true;
									} else {
										$show = false;
									}
									?>
									<div class="condition <?php echo $show ? 'aureon-elements-rule-objects-visible' : ''; ?>">
										<select class="condition-select" name="display-condition[]">
											<option value=""><?php esc_attr_e( 'Choose...', 'aureon-studio' ); ?></option>
											<?php foreach ( $conditions as $type ) { ?>
												<optgroup label="<?php echo esc_html( $type['label'] ); ?>">
													<?php
													foreach ( $type['locations'] as $id => $label ) {
														printf(
															'<option value="%1$s" %2$s>%3$s</option>',
															esc_attr( $id ),
															selected( $value['rule'], $id ),
															esc_html( $label )
														);
													}
													?>
												</optgroup>
											<?php } ?>
										</select>

										<select class="condition-object-select" data-saved-value="<?php echo isset( $value['object'] ) ? esc_html( $value['object'] ) : ''; ?>" name="display-condition-object[]">
											<option value="0"></option>
										</select>

										<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'aureon-studio' ); ?></span></button>
									</div>
									<?php
								}
							} else {
								?>
								<div class="condition">
									<select class="condition-select" name="display-condition[]">
										<option value=""><?php esc_attr_e( 'Choose...', 'aureon-studio' ); ?></option>
										<?php foreach ( $conditions as $type ) { ?>
											<optgroup label="<?php echo esc_html( $type['label'] ); ?>">
												<?php
												foreach ( $type['locations'] as $id => $label ) {
													printf(
														'<option value="%1$s">%2$s</option>',
														esc_attr( $id ),
														esc_html( $label )
													);
												}
												?>
											</optgroup>
										<?php } ?>
									</select>

									<select class="condition-object-select" name="display-condition-object[]">
										<option value="0"></option>
									</select>

									<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'aureon-studio' ); ?></span></button>
								</div>
								<?php
							}
							?>
							<div class="condition hidden screen-reader-text">
								<select class="condition-select" name="display-condition[]">
									<option value=""><?php esc_attr_e( 'Choose...', 'aureon-studio' ); ?></option>
									<?php foreach ( $conditions as $type ) { ?>
										<optgroup label="<?php echo esc_html( $type['label'] ); ?>">
											<?php
											foreach ( $type['locations'] as $id => $label ) {
												printf(
													'<option value="%1$s">%2$s</option>',
													esc_attr( $id ),
													esc_html( $label )
												);
											}
											?>
										</optgroup>
									<?php } ?>
								</select>

								<select class="condition-object-select" name="display-condition-object[]">
									<option value="0"></option>
								</select>

								<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'aureon-studio' ); ?></span></button>
							</div>

							<button class="button add-condition"><?php _e( 'Add Location Rule', 'aureon-studio' ); ?></button>
						</td>
					</tr>

					<tr class="aureon-element-row">
						<td class="aureon-element-row-heading">
							<label><?php _e( 'Exclude', 'aureon-studio' ); ?></label>
							<span class="tip" data-balloon="<?php esc_attr_e( 'Choose when this element should not display.', 'aureon-studio' ); ?>" data-balloon-pos="down">?</span>
						</td>
						<td class="aureon-element-row-content">
							<?php
							$exclude_conditionals = get_post_meta( get_the_ID(), '_aureon_element_exclude_conditions', true );
							$conditions = Aureon_Conditions::get_conditions();

							if ( $exclude_conditionals ) {
								foreach ( $exclude_conditionals as $field => $value ) {
									$type = explode( ':', $value['rule'] );

									if ( in_array( 'post', $type ) || in_array( 'taxonomy', $type ) ) {
										$show = true;
									} else {
										$show = false;
									}
									?>
									<div class="condition <?php echo $show ? 'aureon-elements-rule-objects-visible' : ''; ?>">
										<select class="condition-select" name="exclude-condition[]">
											<option value=""><?php esc_attr_e( 'Choose...', 'aureon-studio' ); ?></option>
											<?php foreach ( $conditions as $type ) { ?>
												<optgroup label="<?php echo esc_html( $type['label'] ); ?>">
													<?php
													foreach ( $type['locations'] as $id => $label ) {
														printf(
															'<option value="%1$s" %2$s>%3$s</option>',
															esc_attr( $id ),
															selected( $value['rule'], $id ),
															esc_html( $label )
														);
													}
													?>
												</optgroup>
											<?php } ?>
										</select>

										<select class="condition-object-select" data-saved-value="<?php echo isset( $value['object'] ) ? esc_html( $value['object'] ) : ''; ?>" name="exclude-condition-object[]">
											<option value="0"></option>
										</select>

										<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'aureon-studio' ); ?></span></button>
									</div>
									<?php
								}
							} else {
								?>
								<div class="condition">
									<select class="condition-select" name="exclude-condition[]">
										<option value=""><?php esc_attr_e( 'Choose...', 'aureon-studio' ); ?></option>
										<?php
										foreach ( $conditions as $type ) {
											?>
											<optgroup label="<?php echo esc_html( $type['label'] ); ?>">
												<?php
												foreach ( $type['locations'] as $id => $label ) {
													printf(
														'<option value="%1$s">%2$s</option>',
														esc_attr( $id ),
														esc_html( $label )
													);
												}
												?>
											</optgroup>
										<?php } ?>
									</select>

									<select class="condition-object-select" name="exclude-condition-object[]">
										<option value="0"></option>
									</select>

									<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'aureon-studio' ); ?></span></button>
								</div>
								<?php
							}
							?>
							<div class="condition hidden screen-reader-text">
								<select class="condition-select" name="exclude-condition[]">
									<option value=""><?php esc_attr_e( 'Choose...', 'aureon-studio' ); ?></option>
									<?php
									foreach ( $conditions as $type ) {
										?>
										<optgroup label="<?php echo esc_html( $type['label'] ); ?>">
											<?php
											foreach ( $type['locations'] as $id => $label ) {
												printf(
													'<option value="%1$s">%2$s</option>',
													esc_attr( $id ),
													esc_html( $label )
												);
											}
											?>
										</optgroup>
									<?php } ?>
								</select>

								<select class="condition-object-select" name="exclude-condition-object[]">
									<option value="0"></option>
								</select>

								<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'aureon-studio' ); ?></span></button>
							</div>

							<button class="button add-condition"><?php _e( 'Add Exclusion Rule', 'aureon-studio' ); ?></button>
						</td>
					</tr>

					<tr class="aureon-element-row">
						<td class="aureon-element-row-heading">
							<label><?php _e( 'Users', 'aureon-studio' ); ?></label>
							<span class="tip" data-balloon="<?php esc_attr_e( 'Display this element for specific user roles.', 'aureon-studio' ); ?>" data-balloon-pos="down">?</span>
						</td>
						<td class="aureon-element-row-content">
							<?php
							$user_conditionals = get_post_meta( get_the_ID(), '_aureon_element_user_conditions', true );
							$conditions = Aureon_Conditions::get_user_conditions();

							if ( $user_conditionals ) {

								foreach ( $user_conditionals as $field => $value ) {
									?>
									<div class="condition">
										<select class="condition-select" name="user-condition[]">
											<option value=""><?php esc_attr_e( 'Choose...', 'aureon-studio' ); ?></option>
											<?php
											foreach ( $conditions as $type ) {
												?>
												<optgroup label="<?php echo esc_html( $type['label'] ); ?>">
													<?php
													foreach ( $type['rules'] as $id => $label ) {
														printf(
															'<option value="%1$s" %2$s>%3$s</option>',
															esc_attr( $id ),
															selected( $value, $id ),
															esc_html( $label )
														);
													}
													?>
												</optgroup>
											<?php } ?>
										</select>

										<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'aureon-studio' ); ?></span></button>
									</div>
									<?php
								}
							} else {
								?>
								<div class="condition">
									<select class="condition-select" name="user-condition[]">
										<option value=""><?php esc_attr_e( 'Choose...', 'aureon-studio' ); ?></option>
										<?php
										foreach ( $conditions as $type ) {
											?>
											<optgroup label="<?php echo esc_html( $type['label'] ); ?>">
												<?php
												foreach ( $type['rules'] as $id => $label ) {
													printf(
														'<option value="%1$s">%2$s</option>',
														esc_attr( $id ),
														esc_html( $label )
													);
												}
												?>
											</optgroup>
										<?php } ?>
									</select>

									<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'aureon-studio' ); ?></span></button>
								</div>
								<?php
							}
							?>
							<div class="condition hidden screen-reader-text">
								<select class="condition-select" name="user-condition[]">
									<option value=""><?php esc_attr_e( 'Choose...', 'aureon-studio' ); ?></option>
									<?php
									foreach ( $conditions as $type ) {
										?>
										<optgroup label="<?php echo esc_html( $type['label'] ); ?>">
											<?php
											foreach ( $type['rules'] as $id => $label ) {
												printf(
													'<option value="%1$s">%2$s</option>',
													esc_attr( $id ),
													esc_html( $label )
												);
											}
											?>
										</optgroup>
									<?php } ?>
								</select>

								<button class="remove-condition"><span class="screen-reader-text"><?php _e( 'Remove', 'aureon-studio' ); ?></span></button>
							</div>

							<button class="button add-condition"><?php _e( 'Add User Rule', 'aureon-studio' ); ?></button>
						</td>
					</tr>
					<tr class="aureon-element-row" <?php echo ! function_exists( 'pll_get_post_language' ) ? 'style="display: none;"' : ''; ?>>
						<td class="aureon-element-row-heading">
							<label for="_aureon_element_ignore_languages"><?php _e( 'Ignore Languages', 'aureon-studio' ); ?></label>
							<span class="tip" data-balloon="<?php esc_attr_e( 'Show this Element to all languages.', 'aureon-studio' ); ?>" data-balloon-pos="up">?</span>
						</td>
						<td class="aureon-element-row-content">
							<input type="checkbox" name="_aureon_element_ignore_languages" id="_aureon_element_ignore_languages" value="true" <?php checked( get_post_meta( get_the_ID(), '_aureon_element_ignore_languages', true ), 'true' ); ?> />
						</td>
					</tr>
				</tbody>
			</table>

			<table class="aureon-elements-settings" data-type="all" data-tab="internal-notes" style="display: none;">
				<tbody>
					<tr id="hook-row" class="aureon-element-row">
						<td class="aureon-element-row-content">
							<textarea id="_aureon_element_internal_notes" name="_aureon_element_internal_notes"><?php echo esc_textarea( get_post_meta( get_the_ID(), '_aureon_element_internal_notes', true ) ); ?></textarea>
							<p style="margin: 5px 0 0;"><?php _e( 'Internal notes can be helpful to remember why this element was added.', 'aureon-studio' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Save all of our metabox values.
	 *
	 * @since 1.7
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST['aureon_elements_nonce'] ) && wp_verify_nonce( $_POST['aureon_elements_nonce'], basename( __FILE__ ) ) ) ? true : false;

		// Exits script depending on save status.
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		// Check that the logged in user has permission to edit this post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( ! isset( $_POST['_aureon_element_type'] ) ) {
			return $post_id;
		}

		// Save the type of element.
		$type_key = '_aureon_element_type';
		$type_value = sanitize_key( $_POST[ $type_key ] ); // phpcs:ignore -- Checked for existence above.

		if ( $type_value ) {
			update_post_meta( $post_id, $type_key, $type_value );
		} else {
			delete_post_meta( $post_id, $type_key );
		}

		// Content.
		if ( isset( $_POST['_aureon_element_content'] ) ) {
			$key = '_aureon_element_content';

			if ( current_user_can( 'unfiltered_html' ) ) {
				$value = $_POST[ $key ];
			} else {
				$value = wp_kses_post( $_POST[ $key ] );
			}

			if ( $value ) {
				update_post_meta( $post_id, $key, $value );
			} else {
				delete_post_meta( $post_id, $key );
			}
		}

		// Save Hooks type.
		if ( 'hook' === $type_value ) {
			$hook_values = array(
				'_aureon_hook'          => 'text',
				'_aureon_custom_hook'   => 'text',
				'_aureon_hook_priority' => 'number',
			);

			if ( 'hook' === $type_value ) {
				$hook_values['_aureon_hook_disable_site_header'] = 'key';
				$hook_values['_aureon_hook_disable_site_footer'] = 'key';
				$hook_values['_aureon_hook_execute_shortcodes']  = 'key';
				$hook_values['_aureon_hook_execute_php']         = 'key';
			}

			// We don't want people to be able to use these hooks.
			$blacklist = array(
				'muplugins_loaded',
				'registered_taxonomy',
				'plugins_loaded',
				'setup_theme',
				'after_setup_theme',
				'init',
				'widgets_init',
				'wp_loaded',
				'pre_get_posts',
				'wp',
				'template_redirect',
				'get_header',
				'wp_enqueue_scripts',
				'the_post',
				'dynamic_sidebar',
				'get_footer',
				'get_sidebar',
				'wp_print_footer_scripts',
				'shutdown',
			);

			foreach ( $hook_values as $key => $type ) {
				$value = false;

				if ( isset( $_POST[ $key ] ) ) {
					// Bail if we're using a blacklisted hook.
					if ( '_aureon_custom_hook' === $key ) {
						if ( in_array( $_POST[ $key ], $blacklist ) ) {
							continue;
						}
					}

					if ( 'number' === $type ) {
						$value = absint( $_POST[ $key ] );
					} elseif ( 'key' === $type ) {
						$value = sanitize_key( $_POST[ $key ] );
					} else {
						$value = sanitize_text_field( $_POST[ $key ] );
					}

					// Need to temporarily change the $value so it returns true.
					if ( '_aureon_hook_priority' === $key ) {
						if ( '0' === $_POST[ $key ] ) {
							$value = 'zero';
						}
					}
				}

				if ( $value ) {
					if ( 'zero' === $value ) {
						$value = '0';
					}

					update_post_meta( $post_id, $key, $value );
				} else {
					delete_post_meta( $post_id, $key );
				}
			}
		}

		// Page Header type.
		if ( 'header' === $type_value ) {
			$hero_values = array(
				'_aureon_hero_custom_classes'                 => 'attribute',
				'_aureon_hero_container'                      => 'text',
				'_aureon_hero_inner_container'                => 'text',
				'_aureon_hero_horizontal_alignment'           => 'text',
				'_aureon_hero_full_screen'                    => 'key',
				'_aureon_hero_vertical_alignment'             => 'text',
				'_aureon_hero_padding_top'                    => 'number',
				'_aureon_hero_padding_top_unit'               => 'text',
				'_aureon_hero_padding_right'                  => 'number',
				'_aureon_hero_padding_right_unit'             => 'text',
				'_aureon_hero_padding_bottom'                 => 'number',
				'_aureon_hero_padding_bottom_unit'            => 'text',
				'_aureon_hero_padding_left'                   => 'number',
				'_aureon_hero_padding_left_unit'              => 'text',
				'_aureon_hero_padding_top_mobile'             => 'number',
				'_aureon_hero_padding_top_unit_mobile'        => 'text',
				'_aureon_hero_padding_right_mobile'           => 'number',
				'_aureon_hero_padding_right_unit_mobile'      => 'text',
				'_aureon_hero_padding_bottom_mobile'          => 'number',
				'_aureon_hero_padding_bottom_unit_mobile'     => 'text',
				'_aureon_hero_padding_left_mobile'            => 'number',
				'_aureon_hero_padding_left_unit_mobile'       => 'text',
				'_aureon_hero_background_image'               => 'key',
				'_aureon_hero_disable_featured_image'         => 'key',
				'_aureon_hero_background_color'               => 'color',
				'_aureon_hero_text_color'                     => 'color',
				'_aureon_hero_link_color'                     => 'color',
				'_aureon_hero_background_link_color_hover'    => 'color',
				'_aureon_hero_background_overlay'             => 'key',
				'_aureon_hero_background_position'            => 'text',
				'_aureon_hero_background_parallax'            => 'key',
				'_aureon_site_header_merge'                   => 'key',
				'_aureon_site_header_height'                  => 'number',
				'_aureon_site_header_height_mobile'           => 'number',
				'_aureon_navigation_colors'                   => 'key',
				'_aureon_site_logo'                           => 'number',
				'_aureon_retina_logo'                         => 'number',
				'_aureon_navigation_logo'                     => 'number',
				'_aureon_mobile_logo'                         => 'number',
				'_aureon_navigation_location'                 => 'key',
				'_aureon_site_header_background_color'        => 'text',
				'_aureon_site_header_title_color'             => 'text',
				'_aureon_site_header_tagline_color'           => 'text',
				'_aureon_navigation_background_color'         => 'text',
				'_aureon_navigation_text_color'               => 'text',
				'_aureon_navigation_background_color_hover'   => 'text',
				'_aureon_navigation_text_color_hover'         => 'text',
				'_aureon_navigation_background_color_current' => 'text',
				'_aureon_navigation_text_color_current'       => 'text',
			);

			foreach ( $hero_values as $key => $type ) {
				$value = false;

				if ( isset( $_POST[ $key ] ) ) {
					if ( 'number' === $type ) {
						$value = absint( $_POST[ $key ] );
					} elseif ( 'key' === $type ) {
						$value = sanitize_key( $_POST[ $key ] );
					} elseif ( 'attribute' === $type ) {
						$value = esc_attr( $_POST[ $key ] );
					} else {
						$value = sanitize_text_field( $_POST[ $key ] );
					}
				}

				if (
					'_aureon_hero_padding_top_mobile' === $key ||
					'_aureon_hero_padding_right_mobile' === $key ||
					'_aureon_hero_padding_bottom_mobile' === $key ||
					'_aureon_hero_padding_left_mobile' === $key
				) {
					if ( '0' === $_POST[ $key ] ) {
						$value = 'zero';
					}
				}

				if ( $value ) {
					if ( 'zero' === $value ) {
						$value = '0'; // String on purpose.
					}

					update_post_meta( $post_id, $key, $value );
				} else {
					delete_post_meta( $post_id, $key );
				}
			}
		}

		// Save Layout type.
		if ( 'layout' === $type_value ) {
			$layout_values = array(
				'_aureon_sidebar_layout'               => 'key',
				'_aureon_footer_widgets'               => 'key',
				'_aureon_disable_site_header'          => 'key',
				'_aureon_disable_mobile_header'        => 'key',
				'_aureon_disable_top_bar'              => 'key',
				'_aureon_disable_primary_navigation'   => 'key',
				'_aureon_disable_secondary_navigation' => 'key',
				'_aureon_disable_featured_image'       => 'key',
				'_aureon_disable_content_title'        => 'key',
				'_aureon_disable_footer'               => 'key',
				'_aureon_content_area'                 => 'key',
				'_aureon_content_width'                => 'number',
			);

			foreach ( $layout_values as $key => $type ) {
				$value = false;

				if ( isset( $_POST[ $key ] ) ) {
					if ( 'number' === $type ) {
						$value = absint( $_POST[ $key ] );
					} elseif ( 'key' === $type ) {
						$value = sanitize_key( $_POST[ $key ] );
					} else {
						$value = sanitize_text_field( $_POST[ $key ] );
					}
				}

				if ( $value ) {
					update_post_meta( $post_id, $key, $value );
				} else {
					delete_post_meta( $post_id, $key );
				}
			}
		}

		$ignore_languages = false;

		if ( isset( $_POST['_aureon_element_ignore_languages'] ) ) {
			$ignore_languages = sanitize_key( $_POST['_aureon_element_ignore_languages'] );
		}

		if ( $ignore_languages ) {
			update_post_meta( $post_id, '_aureon_element_ignore_languages', $ignore_languages );
		} else {
			delete_post_meta( $post_id, '_aureon_element_ignore_languages' );
		}

		// Display conditions.
		$conditions = get_post_meta( $post_id, '_aureon_element_display_conditions', true );
		$new_conditions = array();

		$rules = $_POST['display-condition'];
		$objects = $_POST['display-condition-object'];

		$count = count( $rules );

		for ( $i = 0; $i < $count; $i++ ) {
			if ( '' !== $rules[ $i ] ) {
				if ( in_array( $rules[ $i ], $rules ) ) {
					$new_conditions[ $i ]['rule'] = sanitize_text_field( $rules[ $i ] );
					$new_conditions[ $i ]['object'] = sanitize_key( $objects[ $i ] );
				} else {
					$new_conditions[ $i ]['rule'] = '';
					$new_conditions[ $i ]['object'] = '';
				}
			}
		}

		if ( 'block' === $type_value ) {
			$dynamic_css_posts = get_option( 'generateblocks_dynamic_css_posts', array() );

			if ( $dynamic_css_posts ) {
				foreach ( $new_conditions as $condition ) {
					if ( $condition['object'] && isset( $dynamic_css_posts[ $condition['object'] ] ) ) {
						unset( $dynamic_css_posts[ $condition['object'] ] );
					}

					if ( ! $condition['object'] ) {
						$dynamic_css_posts = array();
						break;
					}
				}

				update_option( 'generateblocks_dynamic_css_posts', $dynamic_css_posts );
			}
		}

		if ( ! empty( $new_conditions ) && $new_conditions !== $conditions ) {
			update_post_meta( $post_id, '_aureon_element_display_conditions', $new_conditions );
		} elseif ( empty( $new_conditions ) && $conditions ) {
			delete_post_meta( $post_id, '_aureon_element_display_conditions', $conditions );
		}

		// Exclude conditions.
		$exclude_conditions = get_post_meta( $post_id, '_aureon_element_exclude_conditions', true );
		$new_exclude_conditions = array();

		$exclude_rules = $_POST['exclude-condition'];
		$exclude_objects = $_POST['exclude-condition-object'];

		$exclude_count = count( $exclude_rules );

		for ( $i = 0; $i < $exclude_count; $i++ ) {
			if ( '' !== $exclude_rules[ $i ] ) {
				if ( in_array( $exclude_rules[ $i ], $exclude_rules ) ) {
					$new_exclude_conditions[ $i ]['rule'] = sanitize_text_field( $exclude_rules[ $i ] );
					$new_exclude_conditions[ $i ]['object'] = sanitize_key( $exclude_objects[ $i ] );
				} else {
					$new_exclude_conditions[ $i ]['rule'] = '';
					$new_exclude_conditions[ $i ]['object'] = '';
				}
			}
		}

		if ( ! empty( $new_exclude_conditions ) && $new_exclude_conditions !== $exclude_conditions ) {
			update_post_meta( $post_id, '_aureon_element_exclude_conditions', $new_exclude_conditions );
		} elseif ( empty( $new_exclude_conditions ) && $exclude_conditions ) {
			delete_post_meta( $post_id, '_aureon_element_exclude_conditions', $exclude_conditions );
		}

		// User conditions.
		$user_conditions = get_post_meta( $post_id, '_aureon_element_user_conditions', true );
		$new_user_conditions = array();

		$user_rules = $_POST['user-condition'];
		$user_count = count( $user_rules );

		for ( $i = 0; $i < $user_count; $i++ ) {
			if ( '' !== $user_rules[ $i ] ) {
				if ( in_array( $user_rules[ $i ], $user_rules ) ) {
					$new_user_conditions[ $i ] = sanitize_text_field( $user_rules[ $i ] );
				} else {
					$new_user_conditions[ $i ] = '';
				}
			}
		}

		if ( ! empty( $new_user_conditions ) && $new_user_conditions !== $user_conditions ) {
			update_post_meta( $post_id, '_aureon_element_user_conditions', $new_user_conditions );
		} elseif ( empty( $new_user_conditions ) && $user_conditions ) {
			delete_post_meta( $post_id, '_aureon_element_user_conditions', $user_conditions );
		}

		// Internal notes.
		$notes_key = '_aureon_element_internal_notes';

		if ( isset( $_POST[ $notes_key ] ) ) {
			if ( function_exists( 'sanitize_textarea_field' ) ) {
				$notes_value = sanitize_textarea_field( $_POST[ $notes_key ] );
			} else {
				$notes_value = sanitize_text_field( $_POST[ $notes_key ] );
			}

			if ( $notes_value ) {
				update_post_meta( $post_id, $notes_key, $notes_value );
			} else {
				delete_post_meta( $post_id, $notes_key );
			}
		}
	}

	/**
	 * Get terms of a set taxonomy.
	 *
	 * @since 1.7
	 */
	public function get_terms() {
		check_ajax_referer( 'aureon-elements-location', 'nonce' );

		$current_user_can = 'manage_options';

		if ( apply_filters( 'aureon_elements_metabox_ajax_allow_editors', false ) ) {
			$current_user_can = 'edit_posts';
		}

		if ( ! current_user_can( $current_user_can ) ) {
			return;
		}

		if ( ! isset( $_POST['id'] ) ) {
			return;
		}

		$tax_id = sanitize_text_field( $_POST['id'] );

		echo wp_json_encode( self::get_taxonomy_terms( $tax_id ) );

		die();
	}

	/**
	 * Get all posts inside a specific post type.
	 *
	 * @since 1.7
	 */
	public function get_posts() {
		check_ajax_referer( 'aureon-elements-location', 'nonce' );

		$current_user_can = 'manage_options';

		if ( apply_filters( 'aureon_elements_metabox_ajax_allow_editors', false ) ) {
			$current_user_can = 'edit_posts';
		}

		if ( ! current_user_can( $current_user_can ) ) {
			return;
		}

		if ( ! isset( $_POST['id'] ) ) {
			return;
		}

		$post_type = sanitize_text_field( $_POST['id'] );

		echo wp_json_encode( self::get_post_type_posts( $post_type ) );

		die();
	}

	/**
	 * Get all of our posts and terms in one go on load.
	 *
	 * @since 2.0.0
	 */
	public function get_all_objects() {
		check_ajax_referer( 'aureon-elements-location', 'nonce' );

		$current_user_can = 'manage_options';

		if ( apply_filters( 'aureon_elements_metabox_ajax_allow_editors', false ) ) {
			$current_user_can = 'edit_posts';
		}

		if ( ! current_user_can( $current_user_can ) ) {
			return;
		}

		if ( ! isset( $_POST['posts'] ) && ! isset( $_POST['terms'] ) ) {
			return;
		}

		$posts = array();
		$terms = array();

		if ( ! empty( $_POST['posts'] ) ) {
			$posts = array_map( 'sanitize_text_field', $_POST['posts'] );
		}

		if ( ! empty( $_POST['terms'] ) ) {
			$terms = array_map( 'sanitize_text_field', $_POST['terms'] );
		}

		$all_posts = self::get_post_type_posts( $posts );
		$all_terms = self::get_taxonomy_terms( $terms );

		echo wp_json_encode( array_merge( $all_posts, $all_terms ) );

		die();
	}

	/**
	 * Look up posts inside a post type.
	 *
	 * @since 1.7
	 *
	 * @param string|array $post_type The post type to look up.
	 * @return array
	 */
	public static function get_post_type_posts( $post_type ) {
		if ( ! is_array( $post_type ) ) {
			$post_type = array( $post_type );
		}

		$data = array();

		foreach ( $post_type as $type ) {
			global $wpdb;

			$post_status = array( 'publish', 'future', 'draft', 'pending', 'private' );

			$object = get_post_type_object( $type );

			$data[ $type ] = array(
				'type'     => 'posts',
				'postType' => $type,
				'label'    => $object->label,
				'objects'  => array(),
			);

			if ( 'attachment' === $type ) {
				$posts = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title from $wpdb->posts where post_type = %s ORDER BY post_title", $type ) );

			} else {
				$format = implode( ', ', array_fill( 0, count( $post_status ), '%s' ) );
				$query = sprintf( "SELECT ID, post_title from $wpdb->posts where post_type = '%s' AND post_status IN(%s) ORDER BY post_title", $type, $format );
				// @codingStandardsIgnoreLine
				$posts = $wpdb->get_results( $wpdb->prepare( $query, $post_status ) );
			}

			foreach ( $posts as $post ) {
				$title = ( '' !== $post->post_title ) ? esc_attr( $post->post_title ) : $type . '-' . $post->ID;
				$data[ $type ]['objects'][] = array(
					'id'    => $post->ID,
					'name'  => $title,
				);
			}
		}

		return $data;
	}

	/**
	 * Get taxonomy terms for a specific taxonomy.
	 *
	 * @since 1.7
	 *
	 * @param int|array $tax_id The taxonomy ID.
	 * @return array
	 */
	public static function get_taxonomy_terms( $tax_id ) {
		if ( ! is_array( $tax_id ) ) {
			$tax_id = array( $tax_id );
		}

		$data = array();

		foreach ( $tax_id as $id ) {
			$tax = get_taxonomy( $id );

			$terms = get_terms(
				array(
					'taxonomy'   => $id,
					'hide_empty' => false,
				)
			);

			$data[ $id ] = array(
				'type'     => 'terms',
				'taxonomy' => $id,
				'label'    => $tax->label,
				'objects'  => array(),
			);

			foreach ( $terms as $term ) {
				$data[ $id ]['objects'][] = array(
					'id'    => $term->term_id,
					'name'  => esc_attr( $term->name ),
				);
			}
		}

		return $data;
	}

	/**
	 * Build our entire list of hooks to display.
	 *
	 * @since 1.7
	 *
	 * @return array Our list of hooks.
	 */
	public static function get_available_hooks() {
		return Aureon_Elements_Helper::get_available_hooks();
	}

	/**
	 * Add available template tags to Header Elements.
	 *
	 * @since 1.7
	 */
	public static function template_tags() {
		?>
		<input type="text" readonly="readonly" value="{{post_title}}" />
		<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;">
			<?php _e( 'The content title of the current post/taxonomy.', 'aureon-studio' ); ?>
		</p>

		<input type="text" readonly="readonly" value="{{post_date}}" />
		<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;">
			<?php _e( 'The published date of the current post.', 'aureon-studio' ); ?>
		</p>

		<input type="text" readonly="readonly" value="{{post_author}}" />
		<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;">
			<?php _e( 'The author of the current post.', 'aureon-studio' ); ?>
		</p>

		<input type="text" readonly="readonly" value="{{post_terms.taxonomy}}" />
		<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;">
			<?php _e( 'The terms attached to the chosen taxonomy (category, post_tag, product_cat).', 'aureon-studio' ); ?>
		</p>

		<input type="text" readonly="readonly" value='{{custom_field.name}}' />
		<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;">
			<?php _e( 'Custom post meta. Replace "name" with the name of your custom field.', 'aureon-studio' ); ?>
		</p>
		<?php
	}
}

Aureon_Elements_Metabox::get_instance();
