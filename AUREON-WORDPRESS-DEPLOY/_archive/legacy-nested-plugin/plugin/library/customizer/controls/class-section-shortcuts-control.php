<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

/**
 * Add a button which needs javascript attached to it.
 */
class Aureon_Section_Shortcut_Control extends WP_Customize_Control {
	public $type = 'aureon_section_shortcut';
	public $element = '';
	public $shortcuts = array();

	public function enqueue() {
		wp_enqueue_script( 'aureon-section-shortcuts', trailingslashit( plugin_dir_url( __FILE__ ) )  . 'js/section-shortcuts.js', array( 'customize-controls' ), AUREON_STUDIO_VERSION, true );
		wp_enqueue_style( 'aureon-section-shortcuts', trailingslashit( plugin_dir_url( __FILE__ ) )  . 'css/section-shortcuts.css', false, AUREON_STUDIO_VERSION );
	}

	public function to_json() {
		parent::to_json();

		$shortcuts = array();
		foreach( $this->shortcuts as $name => $id ) {
			if ( 'colors' === $name ) {
				$name = esc_html__( 'Colors', 'aureon-studio' );

				if ( version_compare( aureon_premium_get_theme_version(), '3.1.0-alpha.1', '>=' ) && 'aureon_woocommerce_colors' !== $id ) {
					$id = 'aureon_colors_section';
				}

				if ( ! aureon_is_module_active( 'aureon_package_colors', 'AUREON_COLORS' ) ) {
					$id = false;
					$name = false;
				}
			}

			if ( 'typography' === $name ) {
				$name = esc_html__( 'Typography', 'aureon-studio' );

				if ( function_exists( 'aureon_is_using_dynamic_typography' ) && aureon_is_using_dynamic_typography() ) {
					$id = 'aureon_typography_section';
				}

				if ( ! aureon_is_module_active( 'aureon_package_typography', 'AUREON_TYPOGRAPHY' ) ) {
					$id = false;
					$name = false;
				}
			}

			if ( 'backgrounds' === $name ) {
				$name = esc_html__( 'Backgrounds', 'aureon-studio' );

				if ( ! aureon_is_module_active( 'aureon_package_backgrounds', 'AUREON_BACKGROUNDS' ) ) {
					$id = false;
					$name = false;
				}
			}

			if ( 'layout' === $name ) {
				$name = esc_html__( 'Layout', 'aureon-studio' );
			}

			if ( $id && $name ) {
				$shortcuts[ $id ] = $name;
			}
		}

		if ( ! empty( $shortcuts ) ) {
			$this->json['shortcuts'] = $shortcuts;
		} else {
			$this->json['shortcuts'] = false;
		}

		if ( 'WooCommerce' !== $this->element ) {
			$this->element = strtolower( $this->element );
		}

		$this->json['more'] = sprintf(
			__( 'More %s controls:', 'aureon-studio' ),
			'<span class="more-element">' . $this->element . '</span>'
		);

		$this->json['return'] = __( 'Go Back', 'aureon-studio' );

		$this->json['section'] = $this->section;

		if ( apply_filters( 'aureon_disable_customizer_shortcuts', false ) ) {
			$this->json['shortcuts'] = false;
		}
	}

	public function content_template() {
		?>
			<div class="aureon-shortcuts">
				<# if ( data.shortcuts ) { #>
					<div class="show-shortcuts">
						<span class="more-controls">
							{{{ data.more }}}
						</span>

						<span class="shortcuts">
							<# _.each( data.shortcuts, function( label, section ) { #>
								<span class="shortcut">
									<a href="#" data-section="{{{ section }}}" data-current-section="{{{ data.section }}}">{{{ label }}}</a>
								</span>
							<# } ) #>
						</span>
					</div>
				<# } #>

				<div class="return-shortcut" style="display: none;">
					<span class="dashicons dashicons-no-alt"></span>
					<a href="#">&larr; {{{ data.return }}}</a>
				</div>
			</div>

		<?php
	}
}
