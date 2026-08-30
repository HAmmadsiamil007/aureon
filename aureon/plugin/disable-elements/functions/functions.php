<?php
/**
 * This file handles the Disable Elements functionality.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

define( 'AUREON_DE_LAYOUT_META_BOX', true );

if ( ! function_exists( 'aureon_disable_elements' ) ) {
	/**
	 * Remove the default disable_elements.
	 *
	 * @since 0.1
	 */
	function aureon_disable_elements() {
		// Don't run the function unless we're on a page it applies to.
		if ( ! is_singular() ) {
			return '';
		}

		global $post;

		// Prevent PHP notices.
		if ( isset( $post ) ) {
			$disable_header = get_post_meta( $post->ID, '_aureon-disable-header', true );
			$disable_nav = get_post_meta( $post->ID, '_aureon-disable-nav', true );
			$disable_secondary_nav = get_post_meta( $post->ID, '_aureon-disable-secondary-nav', true );
			$disable_post_image = get_post_meta( $post->ID, '_aureon-disable-post-image', true );
			$disable_headline = get_post_meta( $post->ID, '_aureon-disable-headline', true );
			$disable_footer = get_post_meta( $post->ID, '_aureon-disable-footer', true );
		}

		$return = '';

		if ( ! empty( $disable_header ) && false !== $disable_header ) {
			$return = '.site-header {display:none}';
		}

		if ( ! empty( $disable_nav ) && false !== $disable_nav ) {
			$return .= '#site-navigation,.navigation-clone, #mobile-header {display:none !important}';
		}

		if ( ! empty( $disable_secondary_nav ) && false !== $disable_secondary_nav ) {
			$return .= '#secondary-navigation {display:none}';
		}

		if ( ! empty( $disable_post_image ) && false !== $disable_post_image ) {
			$return .= '.aureon-page-header, .page-header-image, .page-header-image-single {display:none}';
		}

		$need_css_removal = true;

		if ( defined( 'AUREON_VERSION' ) && version_compare( AUREON_VERSION, '3.0.0-alpha.1', '>=' ) ) {
			$need_css_removal = false;
		}

		if ( $need_css_removal && ! empty( $disable_headline ) && false !== $disable_headline && ! is_single() ) {
			$return .= '.entry-header {display:none} .page-content, .entry-content, .entry-summary {margin-top:0}';
		}

		if ( ! empty( $disable_footer ) && false !== $disable_footer ) {
			$return .= '.site-footer {display:none}';
		}

		return $return;
	}
}

if ( ! function_exists( 'aureon_de_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'aureon_de_scripts', 50 );
	/**
	 * Enqueue scripts and styles
	 */
	function aureon_de_scripts() {
		wp_add_inline_style( 'aureon-style', aureon_disable_elements() );
	}
}

if ( ! function_exists( 'aureon_add_de_meta_box' ) ) {
	add_action( 'add_meta_boxes', 'aureon_add_de_meta_box', 50 );
	/**
	 * Generate the layout metabox.
	 *
	 * @since 0.1
	 */
	function aureon_add_de_meta_box() {
		// Set user role - make filterable.
		$allowed = apply_filters( 'aureon_metabox_capability', 'edit_theme_options' );

		// If not an administrator, don't show the metabox.
		if ( ! current_user_can( $allowed ) ) {
			return;
		}

		if ( defined( 'AUREON_LAYOUT_META_BOX' ) ) {
			return;
		}

		$args = array( 'public' => true );
		$post_types = get_post_types( $args );
		foreach ( $post_types as $type ) {
			if ( 'attachment' !== $type ) {
				add_meta_box(
					'aureon_de_meta_box',
					__( 'Disable Elements', 'aureon-studio' ),
					'aureon_show_de_meta_box',
					$type,
					'side',
					'default'
				);
			}
		}
	}
}

if ( ! function_exists( 'aureon_show_de_meta_box' ) ) {
	/**
	 * Outputs the content of the metabox.
	 *
	 * @param object $post The post object.
	 */
	function aureon_show_de_meta_box( $post ) {
		wp_nonce_field( basename( __FILE__ ), 'aureon_de_nonce' );
		$stored_meta = get_post_meta( $post->ID );
		$stored_meta['_aureon-disable-header'][0] = ( isset( $stored_meta['_aureon-disable-header'][0] ) ) ? $stored_meta['_aureon-disable-header'][0] : '';
		$stored_meta['_aureon-disable-nav'][0] = ( isset( $stored_meta['_aureon-disable-nav'][0] ) ) ? $stored_meta['_aureon-disable-nav'][0] : '';
		$stored_meta['_aureon-disable-secondary-nav'][0] = ( isset( $stored_meta['_aureon-disable-secondary-nav'][0] ) ) ? $stored_meta['_aureon-disable-secondary-nav'][0] : '';
		$stored_meta['_aureon-disable-post-image'][0] = ( isset( $stored_meta['_aureon-disable-post-image'][0] ) ) ? $stored_meta['_aureon-disable-post-image'][0] : '';
		$stored_meta['_aureon-disable-headline'][0] = ( isset( $stored_meta['_aureon-disable-headline'][0] ) ) ? $stored_meta['_aureon-disable-headline'][0] : '';
		$stored_meta['_aureon-disable-footer'][0] = ( isset( $stored_meta['_aureon-disable-footer'][0] ) ) ? $stored_meta['_aureon-disable-footer'][0] : '';
		$stored_meta['_aureon-disable-top-bar'][0] = ( isset( $stored_meta['_aureon-disable-top-bar'][0] ) ) ? $stored_meta['_aureon-disable-top-bar'][0] : '';
		?>

		<p>
			<div class="aureon_disable_elements">
				<?php if ( function_exists( 'aureon_top_bar' ) ) : ?>
					<label for="meta-aureon-disable-top-bar" style="display:block;margin-bottom:3px;" title="<?php _e( 'Top Bar', 'aureon-studio' ); ?>">
						<input type="checkbox" name="_aureon-disable-top-bar" id="meta-aureon-disable-top-bar" value="true" <?php checked( $stored_meta['_aureon-disable-top-bar'][0], 'true' ); ?>>
						<?php _e( 'Top Bar', 'aureon-studio' ); ?>
					</label>
				<?php endif; ?>

				<label for="meta-aureon-disable-header" style="display:block;margin-bottom:3px;" title="<?php _e( 'Header', 'aureon-studio' ); ?>">
					<input type="checkbox" name="_aureon-disable-header" id="meta-aureon-disable-header" value="true" <?php checked( $stored_meta['_aureon-disable-header'][0], 'true' ); ?>>
					<?php _e( 'Header', 'aureon-studio' ); ?>
				</label>

				<label for="meta-aureon-disable-nav" style="display:block;margin-bottom:3px;" title="<?php _e( 'Primary Navigation', 'aureon-studio' ); ?>">
					<input type="checkbox" name="_aureon-disable-nav" id="meta-aureon-disable-nav" value="true" <?php checked( $stored_meta['_aureon-disable-nav'][0], 'true' ); ?>>
					<?php _e( 'Primary Navigation', 'aureon-studio' ); ?>
				</label>

				<?php if ( function_exists( 'aureon_secondary_nav_setup' ) ) : ?>
					<label for="meta-aureon-disable-secondary-nav" style="display:block;margin-bottom:3px;" title="<?php _e( 'Secondary Navigation', 'aureon-studio' ); ?>">
						<input type="checkbox" name="_aureon-disable-secondary-nav" id="meta-aureon-disable-secondary-nav" value="true" <?php checked( $stored_meta['_aureon-disable-secondary-nav'][0], 'true' ); ?>>
						<?php _e( 'Secondary Navigation', 'aureon-studio' ); ?>
					</label>
				<?php endif; ?>

				<label for="meta-aureon-disable-post-image" style="display:block;margin-bottom:3px;" title="<?php _e( 'Featured Image', 'aureon-studio' ); ?>">
					<input type="checkbox" name="_aureon-disable-post-image" id="meta-aureon-disable-post-image" value="true" <?php checked( $stored_meta['_aureon-disable-post-image'][0], 'true' ); ?>>
					<?php _e( 'Featured Image', 'aureon-studio' ); ?>
				</label>

				<label for="meta-aureon-disable-headline" style="display:block;margin-bottom:3px;" title="<?php _e( 'Content Title', 'aureon-studio' ); ?>">
					<input type="checkbox" name="_aureon-disable-headline" id="meta-aureon-disable-headline" value="true" <?php checked( $stored_meta['_aureon-disable-headline'][0], 'true' ); ?>>
					<?php _e( 'Content Title', 'aureon-studio' ); ?>
				</label>

				<label for="meta-aureon-disable-footer" style="display:block;margin-bottom:3px;" title="<?php _e( 'Footer', 'aureon-studio' ); ?>">
					<input type="checkbox" name="_aureon-disable-footer" id="meta-aureon-disable-footer" value="true" <?php checked( $stored_meta['_aureon-disable-footer'][0], 'true' ); ?>>
					<?php _e( 'Footer', 'aureon-studio' ); ?>
				</label>
			</div>
		</p>

		<?php
	}
}

if ( ! function_exists( 'aureon_save_de_meta' ) ) {
	add_action( 'save_post', 'aureon_save_de_meta' );
	/**
	 * Save our options.
	 *
	 * @param int $post_id The post ID.
	 */
	function aureon_save_de_meta( $post_id ) {

		if ( defined( 'AUREON_LAYOUT_META_BOX' ) ) {
			return;
		}

		// Checks save status.
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST['aureon_de_nonce'] ) && wp_verify_nonce( $_POST['aureon_de_nonce'], basename( __FILE__ ) ) ) ? true : false;

		// Exits script depending on save status.
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		// Check that the logged in user has permission to edit this post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$options = array(
			'_aureon-disable-top-bar',
			'_aureon-disable-header',
			'_aureon-disable-nav',
			'_aureon-disable-secondary-nav',
			'_aureon-disable-headline',
			'_aureon-disable-footer',
			'_aureon-disable-post-image',
		);

		foreach ( $options as $key ) {
			$value = isset( $_POST[ $key ] )
				? sanitize_text_field( wp_unslash( $_POST[ $key ] ) )
				: '';

			if ( $value ) {
				update_post_meta( $post_id, $key, $value );
			} else {
				delete_post_meta( $post_id, $key );
			}
		}
	}
}

if ( ! function_exists( 'aureon_disable_elements_setup' ) ) {
	add_action( 'wp', 'aureon_disable_elements_setup', 50 );
	/**
	 * Disable the things.
	 */
	function aureon_disable_elements_setup() {
		// Don't run the function unless we're on a page it applies to.
		if ( ! is_singular() ) {
			return;
		}

		// Get the current post.
		global $post;

		// Grab our values.
		if ( isset( $post ) ) {
			$disable_top_bar = get_post_meta( $post->ID, '_aureon-disable-top-bar', true );
			$disable_header = get_post_meta( $post->ID, '_aureon-disable-header', true );
			$disable_mobile_header = get_post_meta( $post->ID, '_aureon-disable-mobile-header', true );
			$disable_nav = get_post_meta( $post->ID, '_aureon-disable-nav', true );
			$disable_headline = get_post_meta( $post->ID, '_aureon-disable-headline', true );
			$disable_footer = get_post_meta( $post->ID, '_aureon-disable-footer', true );
		}

		// Remove the top bar.
		if ( ! empty( $disable_top_bar ) && false !== $disable_top_bar && function_exists( 'aureon_top_bar' ) ) {
			remove_action( 'aureon_before_header', 'aureon_top_bar', 5 );
			remove_action( 'aureon_inside_secondary_navigation', 'aureon_secondary_nav_top_bar_widget', 5 );
		}

		// Remove the header.
		if ( ! empty( $disable_header ) && false !== $disable_header && function_exists( 'aureon_construct_header' ) ) {
			remove_action( 'aureon_header', 'aureon_construct_header' );
		}

		// Remove the mobile header.
		if ( ! empty( $disable_mobile_header ) && false !== $disable_mobile_header && function_exists( 'aureon_menu_plus_mobile_header' ) ) {
			remove_action( 'aureon_after_header', 'aureon_menu_plus_mobile_header', 5 );
		}

		// Remove the navigation.
		if ( ! empty( $disable_nav ) && false !== $disable_nav && function_exists( 'aureon_get_navigation_location' ) ) {
			add_filter( 'aureon_navigation_location', '__return_false', 20 );
			add_filter( 'aureon_disable_mobile_header_menu', '__return_true' );
		}

		// Remove the title.
		if ( ! empty( $disable_headline ) && false !== $disable_headline && function_exists( 'aureon_show_title' ) ) {
			add_filter( 'aureon_show_title', '__return_false' );
		}

		// Remove the footer.
		if ( ! empty( $disable_footer ) && false !== $disable_footer ) {
			if ( function_exists( 'aureon_construct_footer_widgets' ) ) {
				remove_action( 'aureon_footer', 'aureon_construct_footer_widgets', 5 );
			}

			if ( function_exists( 'aureon_construct_footer' ) ) {
				remove_action( 'aureon_footer', 'aureon_construct_footer' );
			}
		}
	}
}

add_action( 'aureon_layout_disable_elements_section', 'aureon_premium_disable_elements_options' );
/**
 * Add the meta box options to the Layout meta box in the new GP
 *
 * @since 1.4
 * @param array $stored_meta Existing meta data.
 */
function aureon_premium_disable_elements_options( $stored_meta ) {
	$stored_meta['_aureon-disable-header'][0] = ( isset( $stored_meta['_aureon-disable-header'][0] ) ) ? $stored_meta['_aureon-disable-header'][0] : '';
	$stored_meta['_aureon-disable-mobile-header'][0] = ( isset( $stored_meta['_aureon-disable-mobile-header'][0] ) ) ? $stored_meta['_aureon-disable-mobile-header'][0] : '';
	$stored_meta['_aureon-disable-nav'][0] = ( isset( $stored_meta['_aureon-disable-nav'][0] ) ) ? $stored_meta['_aureon-disable-nav'][0] : '';
	$stored_meta['_aureon-disable-secondary-nav'][0] = ( isset( $stored_meta['_aureon-disable-secondary-nav'][0] ) ) ? $stored_meta['_aureon-disable-secondary-nav'][0] : '';
	$stored_meta['_aureon-disable-post-image'][0] = ( isset( $stored_meta['_aureon-disable-post-image'][0] ) ) ? $stored_meta['_aureon-disable-post-image'][0] : '';
	$stored_meta['_aureon-disable-headline'][0] = ( isset( $stored_meta['_aureon-disable-headline'][0] ) ) ? $stored_meta['_aureon-disable-headline'][0] : '';
	$stored_meta['_aureon-disable-footer'][0] = ( isset( $stored_meta['_aureon-disable-footer'][0] ) ) ? $stored_meta['_aureon-disable-footer'][0] : '';
	$stored_meta['_aureon-disable-top-bar'][0] = ( isset( $stored_meta['_aureon-disable-top-bar'][0] ) ) ? $stored_meta['_aureon-disable-top-bar'][0] : '';
	?>
	<div class="aureon_disable_elements">
		<?php if ( function_exists( 'aureon_top_bar' ) ) : ?>
			<label for="meta-aureon-disable-top-bar" style="display:block;margin-bottom:3px;" title="<?php _e( 'Top Bar', 'aureon-studio' ); ?>">
				<input type="checkbox" name="_aureon-disable-top-bar" id="meta-aureon-disable-top-bar" value="true" <?php checked( $stored_meta['_aureon-disable-top-bar'][0], 'true' ); ?>>
				<?php _e( 'Top Bar', 'aureon-studio' ); ?>
			</label>
		<?php endif; ?>

		<label for="meta-aureon-disable-header" style="display:block;margin-bottom:3px;" title="<?php _e( 'Header', 'aureon-studio' ); ?>">
			<input type="checkbox" name="_aureon-disable-header" id="meta-aureon-disable-header" value="true" <?php checked( $stored_meta['_aureon-disable-header'][0], 'true' ); ?>>
			<?php _e( 'Header', 'aureon-studio' ); ?>
		</label>

		<?php
		if ( function_exists( 'aureon_menu_plus_get_defaults' ) ) :
			$menu_plus_settings = wp_parse_args(
				get_option( 'aureon_menu_plus_settings', array() ),
				aureon_menu_plus_get_defaults()
			);

			if ( 'enable' === $menu_plus_settings['mobile_header'] ) :
				?>
				<label for="meta-aureon-disable-mobile-header" style="display:block;margin-bottom:3px;" title="<?php esc_attr_e( 'Mobile Header', 'aureon-studio' ); ?>">
					<input type="checkbox" name="_aureon-disable-mobile-header" id="meta-aureon-disable-mobile-header" value="true" <?php checked( $stored_meta['_aureon-disable-mobile-header'][0], 'true' ); ?>>
					<?php esc_html_e( 'Mobile Header', 'aureon-studio' ); ?>
				</label>
				<?php
			endif;
		endif;
		?>

		<label for="meta-aureon-disable-nav" style="display:block;margin-bottom:3px;" title="<?php _e( 'Primary Navigation', 'aureon-studio' ); ?>">
			<input type="checkbox" name="_aureon-disable-nav" id="meta-aureon-disable-nav" value="true" <?php checked( $stored_meta['_aureon-disable-nav'][0], 'true' ); ?>>
			<?php _e( 'Primary Navigation', 'aureon-studio' ); ?>
		</label>

		<?php if ( function_exists( 'aureon_secondary_nav_setup' ) ) : ?>
			<label for="meta-aureon-disable-secondary-nav" style="display:block;margin-bottom:3px;" title="<?php _e( 'Secondary Navigation', 'aureon-studio' ); ?>">
				<input type="checkbox" name="_aureon-disable-secondary-nav" id="meta-aureon-disable-secondary-nav" value="true" <?php checked( $stored_meta['_aureon-disable-secondary-nav'][0], 'true' ); ?>>
				<?php _e( 'Secondary Navigation', 'aureon-studio' ); ?>
			</label>
		<?php endif; ?>

		<label for="meta-aureon-disable-post-image" style="display:block;margin-bottom:3px;" title="<?php _e( 'Featured Image', 'aureon-studio' ); ?>">
			<input type="checkbox" name="_aureon-disable-post-image" id="meta-aureon-disable-post-image" value="true" <?php checked( $stored_meta['_aureon-disable-post-image'][0], 'true' ); ?>>
			<?php _e( 'Featured Image', 'aureon-studio' ); ?>
		</label>

		<label for="meta-aureon-disable-headline" style="display:block;margin-bottom:3px;" title="<?php _e( 'Content Title', 'aureon-studio' ); ?>">
			<input type="checkbox" name="_aureon-disable-headline" id="meta-aureon-disable-headline" value="true" <?php checked( $stored_meta['_aureon-disable-headline'][0], 'true' ); ?>>
			<?php _e( 'Content Title', 'aureon-studio' ); ?>
		</label>

		<label for="meta-aureon-disable-footer" style="display:block;margin-bottom:3px;" title="<?php _e( 'Footer', 'aureon-studio' ); ?>">
			<input type="checkbox" name="_aureon-disable-footer" id="meta-aureon-disable-footer" value="true" <?php checked( $stored_meta['_aureon-disable-footer'][0], 'true' ); ?>>
			<?php _e( 'Footer', 'aureon-studio' ); ?>
		</label>
	</div>
	<?php
}

add_action( 'aureon_layout_meta_box_save', 'aureon_premium_save_disable_elements_meta' );
/**
 * Save the Disable Elements meta box values
 *
 * @since 1.4
 * @param int $post_id The post ID.
 */
function aureon_premium_save_disable_elements_meta( $post_id ) {
	$options = array(
		'_aureon-disable-top-bar',
		'_aureon-disable-header',
		'_aureon-disable-mobile-header',
		'_aureon-disable-nav',
		'_aureon-disable-secondary-nav',
		'_aureon-disable-headline',
		'_aureon-disable-footer',
		'_aureon-disable-post-image',
	);

	foreach ( $options as $key ) {
		$value = isset( $_POST[ $key ] ) // phpcs:ignore -- Nonce exists within `aureon_layout_meta_box_save` hook.
			? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) // phpcs:ignore -- Nonce exists within `aureon_layout_meta_box_save` hook.
			: '';

		if ( $value ) {
			update_post_meta( $post_id, $key, $value );
		} else {
			delete_post_meta( $post_id, $key );
		}
	}
}

add_filter( 'body_class', 'aureon_disable_elements_body_classes', 20 );
/**
 * Remove body classes if certain elements are disabled.
 *
 * @since 2.1.0
 * @param array $classes Our body classes.
 */
function aureon_disable_elements_body_classes( $classes ) {
	if ( is_singular() ) {
		$disable_featured_image = get_post_meta( get_the_ID(), '_aureon-disable-post-image', true );
		$classes = aureon_premium_remove_featured_image_class( $classes, $disable_featured_image );
	}

	return $classes;
}
