<?php
/**
 * Error 404 — lost-in-the-void empty state for 404 pages.
 *
 * Key:    'utility/error-404'
 * Source: 404.html `.error-404`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $code         Error code. Default '404'.`
 * - `string $title        Page title. Default 'Lost in the Void'.`
 * - `string $description  Description. Default 'The page you're looking for doesn't exist or has been moved.'.`
 * - `string $home_url     Home link. Default ''.`
 * - `string $shop_url     Shop link. Default ''.`
 * - `array $behavior    Behavior whitelist. Default [].`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$code        = isset( $componentData['code'] ) ? $componentData['code'] : '404';
$title       = isset( $componentData['title'] ) ? $componentData['title'] : __( 'Lost in the Void', 'aureon' );
$description = isset( $componentData['description'] ) ? $componentData['description'] : __( "The page you're looking for doesn't exist or has been moved.", 'aureon' );
$home_url    = isset( $componentData['home_url'] ) ? $componentData['home_url'] : '';
$shop_url    = isset( $componentData['shop_url'] ) ? $componentData['shop_url'] : '';
$behavior    = isset( $componentData['behavior'] ) ? (array) $componentData['behavior'] : array();
?>
<section class="error-page" data-parallax-section data-phantom-bg="hero" <?php echo aether_behavior_attrs( $behavior ); ?>>
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
	<div class="error-content">
		<span class="error-code" data-motion-text="words" data-phantom="error_code"><?php echo esc_html( $code ); ?></span>
		<h1 class="error-title" data-phantom="page_title"><?php echo esc_html( $title ); ?></h1>
		<p class="error-description" data-phantom="page_description"><?php echo esc_html( $description ); ?></p>
		<div class="error-buttons">
			<?php if ( $home_url ) : ?>
				<a href="<?php echo esc_url( $home_url ); ?>" class="btn btn-primary" data-magnetic="0.12"><?php esc_html_e( 'Return Home', 'aureon' ); ?></a>
			<?php endif; ?>
			<?php if ( $shop_url ) : ?>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-outline" data-magnetic="0.12"><?php esc_html_e( 'Back to Shop', 'aureon' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>