<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'Aureon_Spacing_Control' ) ) :
class Aureon_Spacing_Control extends WP_Customize_Control {

	public $type = 'aureon-spacing';

	public $l10n = array();

	public $element = '';

	public function __construct( $manager, $id, $args = array() ) {
		// Let the parent class do its thing.
		parent::__construct( $manager, $id, $args );
	}

	public function enqueue() {
		wp_enqueue_script( 'aureon-spacing-customizer', trailingslashit( plugin_dir_url( __FILE__ ) )  . 'js/spacing-customizer.js', array( 'customize-controls' ), AUREON_STUDIO_VERSION, true );
		wp_enqueue_style( 'aureon-spacing-customizer-controls-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/spacing-customizer.css', array(), AUREON_STUDIO_VERSION );
	}

	public function to_json() {
		parent::to_json();
		// Loop through each of the settings and set up the data for it.
		foreach ( $this->settings as $setting_key => $setting_id ) {
			$this->json[ $setting_key ] = array(
				'link'  => $this->get_link( $setting_key ),
				'value' => $this->value( $setting_key )
			);
		}

		$this->json[ 'element' ] = $this->element;
		$this->json[ 'title' ] = __( 'Link values', 'aureon-studio' );
		$this->json[ 'unlink_title' ] = __( 'Un-link values', 'aureon-studio' );

		$this->json['label_top'] = esc_html__( 'Top', 'aureon-studio' );
		$this->json['label_right'] = esc_html__( 'Right', 'aureon-studio' );
		$this->json['label_bottom'] = esc_html__( 'Bottom', 'aureon-studio' );
		$this->json['label_left'] = esc_html__( 'Left', 'aureon-studio' );
		$this->json['desktop_label'] = esc_html__( 'Desktop', 'aureon-studio' );
		$this->json['tablet_label'] = esc_html__( 'Tablet', 'aureon-studio' );
		$this->json['mobile_label'] = esc_html__( 'Mobile', 'aureon-studio' );
	}

	public function content_template() {
		?>
		<div class="aureon-spacing-control-section">
			<div class="aureon-spacing-control-section-title-area">
				<# if ( data.label || data.description ) { #>
					<div class="aureon-spacing-control-title-info">
						<# if ( data.label ) { #>
							<label for="{{{ data.element }}}-{{{ data.top_label }}}">
								<span class="customize-control-title">{{ data.label }}</span>
							</label>
						<# } #>

						<# if ( data.description ) { #>
							<span class="description customize-control-description">{{{ data.description }}}</span>
						<# } #>
					</div>
				<# } #>

				<div class="aureon-range-slider-controls">
					<span class="aureon-device-controls">
						<# if ( 'undefined' !== typeof ( data.desktop_top ) ) { #>
							<span class="aureon-device-desktop dashicons dashicons-desktop" data-option="desktop" title="{{ data.desktop_label }}"></span>
						<# } #>

						<# if ( 'undefined' !== typeof (data.tablet_top) ) { #>
							<span class="aureon-device-tablet dashicons dashicons-tablet" data-option="tablet" title="{{ data.tablet_label }}"></span>
						<# } #>

						<# if ( 'undefined' !== typeof (data.mobile_top) ) { #>
							<span class="aureon-device-mobile dashicons dashicons-smartphone" data-option="mobile" title="{{ data.mobile_label }}"></span>
						<# } #>
					</span>
				</div>
			</div>

			<div class="spacing-values-container">
				<div class="spacing-values-desktop spacing-values-area" data-option="desktop" style="display: none;">
					<div class="aureon-spacing-section">
						<input id="{{{ data.element }}}-{{{ data.label_top }}}" min="0" class="aureon-number-control spacing-top" type="number" style="text-align: center;" {{{ data.desktop_top.link }}} value="{{{ data.desktop_top.value }}}" />
						<# if ( data.label_top ) { #>
							<label for="{{{ data.element }}}-{{{ data.label_top }}}" class="description" style="font-style:normal;">{{ data.label_top }}</label>
						<# } #>
					</div>

					<div class="aureon-spacing-section">
						<input id="{{{ data.element }}}-{{{ data.label_right }}}" min="0" class="aureon-number-control spacing-right" type="number" style="text-align: center;" {{{ data.desktop_right.link }}} value="{{{ data.desktop_right.value }}}" />
						<# if ( data.label_right ) { #>
							<label for="{{{ data.element }}}-{{{ data.label_right }}}" class="description" style="font-style:normal;">{{ data.label_right }}</label>
						<# } #>
					</div>

					<div class="aureon-spacing-section">
						<input id="{{{ data.element }}}-{{{ data.label_bottom }}}" min="0" class="aureon-number-control spacing-bottom" type="number" style="text-align: center;" {{{ data.desktop_bottom.link }}} value="{{{ data.desktop_bottom.value }}}" />
						<# if ( data.label_bottom ) { #>
							<label for="{{{ data.element }}}-{{{ data.label_bottom }}}" class="description" style="font-style:normal;">{{ data.label_bottom }}</label>
						<# } #>
					</div>

					<div class="aureon-spacing-section">
						<input id="{{{ data.element }}}-{{{ data.label_left }}}" min="0" class="aureon-number-control spacing-left" type="number" style="text-align: center;" {{{ data.desktop_left.link }}} value="{{{ data.desktop_left.value }}}" />
						<# if ( data.label_left ) { #>
							<label for="{{{ data.element }}}-{{{ data.label_left }}}" class="description" style="font-style:normal;">{{ data.label_left }}</label>
						<# } #>
					</div>

					<# if ( data.element ) { #>
						<div class="aureon-spacing-section aureon-link-spacing-section">
							<span class="dashicons dashicons-editor-unlink aureon-link-spacing" data-element="{{ data.element }}" title="{{ data.title }}"></span>
							<span class="dashicons dashicons-admin-links aureon-unlink-spacing" style="display:none" data-element="{{ data.element }}" title="{{ data.unlink_title }}"></span>
						</div>
					<# } #>
				</div>

				<# if ( 'undefined' !== typeof ( data.mobile_top ) ) { #>
					<div class="spacing-values-mobile spacing-values-area" data-option="mobile" style="display: none;">
						<div class="aureon-spacing-section">
							<input id="{{{ data.element }}}-mobile-{{{ data.label_top }}}" min="0" class="aureon-number-control mobile-spacing-top" type="number" style="text-align: center;" {{{ data.mobile_top.link }}} value="{{{ data.mobile_top.value }}}" />
							<# if ( data.label_top ) { #>
								<label for="{{{ data.element }}}-mobile-{{{ data.label_top }}}" class="description" style="font-style:normal;">{{ data.label_top }}</label>
							<# } #>
						</div>

						<div class="aureon-spacing-section">
							<input id="{{{ data.element }}}-mobile-{{{ data.label_right }}}" min="0" class="aureon-number-control mobile-spacing-right" type="number" style="text-align: center;" {{{ data.mobile_right.link }}} value="{{{ data.mobile_right.value }}}" />
							<# if ( data.label_right ) { #>
								<label for="{{{ data.element }}}-mobile-{{{ data.label_right }}}" class="description" style="font-style:normal;">{{ data.label_right }}</label>
							<# } #>
						</div>

						<div class="aureon-spacing-section">
							<input id="{{{ data.element }}}-mobile-{{{ data.label_bottom }}}" min="0" class="aureon-number-control mobile-spacing-bottom" type="number" style="text-align: center;" {{{ data.mobile_bottom.link }}} value="{{{ data.mobile_bottom.value }}}" />
							<# if ( data.label_bottom ) { #>
								<label for="{{{ data.element }}}-mobile-{{{ data.label_bottom }}}" class="description" style="font-style:normal;">{{ data.label_bottom }}</label>
							<# } #>
						</div>

						<div class="aureon-spacing-section">
							<input id="{{{ data.element }}}-mobile-{{{ data.label_left }}}" min="0" class="aureon-number-control mobile-spacing-left" type="number" style="text-align: center;" {{{ data.mobile_left.link }}} value="{{{ data.mobile_left.value }}}" />
							<# if ( data.label_left ) { #>
								<label for="{{{ data.element }}}-mobile-{{{ data.label_left }}}" class="description" style="font-style:normal;">{{ data.label_left }}</label>
							<# } #>
						</div>

						<# if ( data.element ) { #>
							<div class="aureon-spacing-section aureon-link-spacing-section">
								<span class="dashicons dashicons-editor-unlink aureon-link-spacing" data-element="{{ data.element }}" title="{{ data.title }}"></span>
								<span class="dashicons dashicons-admin-links aureon-unlink-spacing" style="display:none" data-element="{{ data.element }}" title="{{ data.unlink_title }}"></span>
							</div>
						<# } #>
					</div>
				<# } #>
			</div>
		</div>
		<?php
	}
}
endif;
