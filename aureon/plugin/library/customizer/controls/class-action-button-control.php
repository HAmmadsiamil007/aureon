<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

if ( ! class_exists( 'Aureon_Action_Button_Control' ) ) {
	/**
	 * Add a button which needs javascript attached to it.
	 */
	class Aureon_Action_Button_Control extends WP_Customize_Control {
		public $type = 'aureon_action_button';
		public $data_type = '';
		public $description = '';
		public $nonce = '';

		public function enqueue() {
			wp_enqueue_script( 'aureon-button-actions', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/button-actions.js', array( 'customize-controls' ), AUREON_STUDIO_VERSION, true );
			wp_enqueue_style( 'aureon-button-actions', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/button-actions.css', array(), AUREON_STUDIO_VERSION );
		}

		public function to_json() {
			parent::to_json();

			$this->json['data_type'] = $this->data_type;
			$this->json['description'] = $this->description;
			$this->json['nonce'] = $this->nonce;
		}

		public function content_template() {
			?>
			<button class="button" data-type="{{{ data.data_type }}}" data-nonce="{{{ data.nonce }}}">{{{ data.label }}}</button>
			<# if ( data.description ) { #>
				<span class="description customize-control-description">
					<p>{{{ data.description }}}</p>
				</span>
			<# } #>
			<?php
		}
	}
}
