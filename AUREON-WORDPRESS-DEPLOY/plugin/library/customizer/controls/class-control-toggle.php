<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Aureon_Control_Toggle_Customize_Control' ) ) :
/**
 * Add a button to initiate refresh when changing featured image sizes
 */
class Aureon_Control_Toggle_Customize_Control extends WP_Customize_Control {
	public $type = 'control_section_toggle';
	public $targets = '';
	
	public function enqueue() {
		wp_enqueue_script( 'aureon-pro-control-target', trailingslashit( plugin_dir_url( __FILE__ ) )  . 'js/control-toggle-customizer.js', array( 'customize-controls', 'jquery' ), AUREON_STUDIO_VERSION, true );
		wp_enqueue_style( 'aureon-pro-control-target', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/control-toggle-customizer.css', array(), AUREON_STUDIO_VERSION );
	}
	
	public function to_json() {
		parent::to_json();
		
		$this->json[ 'targets' ] = $this->targets;

	}
	
	public function content_template() {
		?>
		<div class="aureon-control-toggles">
			<# jQuery.each( data.targets, function( index, value ) { #>
				<button data-target="{{ index }}">{{ value }}</button>
			<# } ); #>
		</div>
		<?php
	}
}
endif;