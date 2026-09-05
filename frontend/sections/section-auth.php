<?php
/**
 * Auth section — AETHER-styled login / register cards.
 *
 * Renders the page-hero auth frame from login.html/join-now.html with
 * either the login or register form (mode passed from the template).
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'auth', array(
	'template' => 'sections/section-auth.php',
	'adapter'  => 'adapter-auth.php',
	'behavior' => array( 'reveal' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$mode         = isset( $sectionData['mode'] ) ? $sectionData['mode'] : 'login';
$login_data   = isset( $sectionData['login'] ) ? (array) $sectionData['login'] : array();
$register_data = isset( $sectionData['register'] ) ? (array) $sectionData['register'] : array();
$forgot_data  = isset( $sectionData['forgot'] ) ? (array) $sectionData['forgot'] : array();
$behavior     = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();
?>
<section class="page-hero auth-hero" data-phantom-bg="hero" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="hero-fog" aria-hidden="true">
		<div id="hl_01" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
		<div id="hl_02" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
		<div id="hl_03" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
	</div>
	<div class="container">
		<?php if ( 'register' === $mode ) : ?>
			<?php aether_render_component( 'form/register', array(
				'action'       => isset( $register_data['action'] ) ? $register_data['action'] : '',
				'nonce'        => isset( $register_data['nonce'] ) ? $register_data['nonce'] : '',
				'login_url'    => isset( $register_data['login_url'] ) ? $register_data['login_url'] : '',
				'show_strength' => ! empty( $register_data['show_strength'] ),
				'brand'        => isset( $sectionData['brand'] ) ? $sectionData['brand'] : '',
			) ); ?>
		<?php else : ?>
			<?php aether_render_component( 'form/login', array(
				'action'            => isset( $login_data['action'] ) ? $login_data['action'] : '',
				'nonce'             => isset( $login_data['nonce'] ) ? $login_data['nonce'] : '',
				'lost_url'          => isset( $login_data['lost_url'] ) ? $login_data['lost_url'] : '',
				'forgot_modal'      => ! empty( $login_data['forgot_modal'] ),
				'register_enabled'  => ! empty( $login_data['register_enabled'] ),
				'register_url'      => isset( $login_data['register_url'] ) ? $login_data['register_url'] : '',
				'brand'             => isset( $sectionData['brand'] ) ? $sectionData['brand'] : '',
			) ); ?>
			<?php if ( ! empty( $forgot_data['action'] ) && function_exists( 'aether_render_component' ) ) : ?>
				<?php aether_render_component( 'form/forgot-password', array(
					'action' => isset( $forgot_data['action'] ) ? $forgot_data['action'] : '',
					'nonce'  => isset( $forgot_data['nonce'] ) ? $forgot_data['nonce'] : '',
					'brand'  => isset( $sectionData['brand'] ) ? $sectionData['brand'] : '',
				) ); ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>