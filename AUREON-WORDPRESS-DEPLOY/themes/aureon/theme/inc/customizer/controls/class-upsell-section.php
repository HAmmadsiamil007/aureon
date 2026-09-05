<?php
/**
 * The upsell Customizer section.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'WP_Customize_Section' ) && ! class_exists( 'Aureon_Upsell_Section' ) ) {
	/**
	 * Create our upsell section.
	 * Escape your URL in the Customizer using esc_url().
	 *
	 * @since unknown
	 */
	class Aureon_Upsell_Section extends WP_Customize_Section {
		/**
		 * Set type.
		 *
		 * @var public $type
		 */
		public $type = 'aureon-upsell-section';

		/**
		 * Set pro URL.
		 *
		 * @var public $pro_url
		 */
		public $pro_url = '';

		/**
		 * Set pro text.
		 *
		 * @var public $pro_text
		 */
		public $pro_text = '';

		/**
		 * Set ID.
		 *
		 * @var public $id
		 */
		public $id = '';

		/**
		 * Send variables to json.
		 */
		public function json() {
			$json = parent::json();
			$json['pro_text'] = $this->pro_text;
			$json['pro_url']  = esc_url( $this->pro_url );
			$json['id'] = $this->id;
			return $json;
		}

		/**
		 * Render content.
		 */
		protected function render_template() {
			?>
			<li id="accordion-section-{{ data.id }}" class="aureon-upsell-accordion-section control-section-{{ data.type }} cannot-expand accordion-section">
				<h3><a href="{{{ data.pro_url }}}" target="_blank">{{ data.pro_text }}</a></h3>
			</li>
			<?php
		}
	}
}

if ( ! function_exists( 'aureon_customizer_controls_css' ) ) {
	add_action( 'customize_controls_enqueue_scripts', 'aureon_customizer_controls_css' );
	/**
	 * Add CSS for our controls
	 *
	 * @since 1.3.41
	 */
	function aureon_customizer_controls_css() {
		wp_enqueue_style(
			'aureon-customizer-controls-css',
			trailingslashit( get_template_directory_uri() ) . 'inc/customizer/controls/css/upsell-customizer.css',
			array(),
			AUREON_VERSION
		);

		wp_enqueue_script(
			'aureon-upsell',
			trailingslashit( get_template_directory_uri() ) . 'inc/customizer/controls/js/upsell-control.js',
			array( 'customize-controls' ),
			AUREON_VERSION,
			true
		);
	}
}
