<?php
/**
 * Schema-driven repeater Customizer control.
 *
 * Generic, reusable repeater: the control itself knows nothing about the
 * data domain (hero slides, testimonials, …). The field schema is passed
 * through $args['choices']['schema'] and the sanitizer receives its key via
 * the aureon_sanitize_repeater() setting callback.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Aureon_Customize_Repeater_Control' ) ) {
	/**
	 * Repeater control class.
	 *
	 * @since 1.2.0
	 */
	class Aureon_Customize_Repeater_Control extends WP_Customize_Control {
		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'aureon-repeater';

		/**
		 * Default label for the add-more button.
		 *
		 * @access public
		 * @var string
		 */
		public $add_label = '';

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();

			$value = $this->value();
			$value = is_string( $value ) ? json_decode( $value, true ) : $value;
			$this->json['value']       = is_array( $value ) ? array_values( $value ) : array();
			$this->json['schema']      = isset( $this->choices['schema'] ) ? (array) $this->choices['schema'] : array();
			$this->json['add_label']   = $this->get_add_label();
			$this->json['item_label']  = isset( $this->choices['item_label'] ) ? $this->choices['item_label'] : __( 'Item', 'aureon' );
			$this->json['title_key']   = isset( $this->choices['title_key'] ) ? $this->choices['title_key'] : '';

			$this->json['labels'] = array(
				'remove'  => __( 'Remove', 'aureon' ),
				'expand'  => __( 'Expand', 'aureon' ),
				'collapse' => __( 'Collapse', 'aureon' ),
				'visible' => __( 'Visible', 'aureon' ),
				'hidden'  => __( 'Hidden', 'aureon' ),
				'upload'  => __( 'Upload', 'aureon' ),
				'removeImage' => __( 'Remove image', 'aureon' ),
			);
		}

		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {
			wp_enqueue_script(
				'aureon-repeater',
				trailingslashit( get_template_directory_uri() ) . 'inc/customizer/controls/js/repeater.js',
				array( 'customize-controls', 'jquery-ui-sortable', 'wp-media-utils' ),
				AUREON_VERSION,
				true
			);

			wp_enqueue_style(
				'aureon-repeater',
				trailingslashit( get_template_directory_uri() ) . 'inc/customizer/controls/css/repeater.css',
				array(),
				AUREON_VERSION
			);
		}

		/**
		 * Add-more button label: control arg > schema add_label > default.
		 *
		 * @access private
		 * @return string
		 */
		private function get_add_label() {
			if ( $this->add_label ) {
				return $this->add_label;
			}

			$schema = isset( $this->choices['schema'] ) ? (array) $this->choices['schema'] : array();
			if ( ! empty( $schema['add_label'] ) ) {
				return $schema['add_label'];
			}

			return __( 'Add item', 'aureon' );
		}

		/**
		 * Render the control's content.
		 *
		 * This control type is registered via register_control_type(), so the
		 * Customizer renders it through the JS content template below
		 * (tmpl-customize-control-aureon-repeater-content) instead of the
		 * server-side render_content() output.
		 *
		 * @access public
		 */
		public function content_template() {
			?>
			<#
			var descriptionId = _.uniqueId( 'aureon-repeater-description-' );
			var describedByAttr = data.description ? ' aria-describedby="' + descriptionId + '" ' : '';
			#>
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>
			<div class="aureon-repeater" data-repeater-root data-add-label="<?php echo esc_attr( $this->get_add_label() ); ?>">
				<div class="aureon-repeater__items"></div>
				<button type="button" class="button aureon-repeater__add">
					<span class="dashicons dashicons-plus-alt2"></span>
					<span class="aureon-repeater__add-label"><?php echo esc_html( $this->get_add_label() ); ?></span>
				</button>
			</div>
			<div class="customize-control-notifications-container"></div>
			<# if ( data.description ) { #>
				<span id="{{ descriptionId }}" class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>
			<?php
		}

		/**
		 * No server-side content — rendering happens via content_template().
		 *
		 * @access public
		 */
		public function render_content() {}
	}
}