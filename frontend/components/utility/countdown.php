<?php
/**
 * Coming soon countdown — launch countdown with brand and socials.
 *
 * Key:    'utility/countdown'
 * Source: coming-soon.html `.countdown`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $brand     Brand wordmark. Default ''.`
 * - `string $title     Launch title. Default 'Something is Coming'.`
 * - `string $subtitle  Subtitle. Default 'The next evolution in performance footwear drops soon.'.`
 * - `string $target    Target timestamp (ISO). Default ''.`
 * - `array $socials   Social link schema. Default [].`
 * - `array $behavior  Behavior whitelist. Default [].`
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

$brand     = isset( $componentData['brand'] ) ? $componentData['brand'] : '';
$title     = isset( $componentData['title'] ) ? $componentData['title'] : __( 'Something is Coming', 'aureon' );
$subtitle  = isset( $componentData['subtitle'] ) ? $componentData['subtitle'] : __( 'The next evolution in performance footwear drops soon.', 'aureon' );
$target    = isset( $componentData['target'] ) ? $componentData['target'] : '';
$socials   = isset( $componentData['socials'] ) ? (array) $componentData['socials'] : array();
$behavior  = isset( $componentData['behavior'] ) ? (array) $componentData['behavior'] : array();

$aether_countdown_id = 'aetherCountdown_' . uniqid();
?>
<section class="coming-soon-section" data-phantom-bg="hero" <?php echo aether_behavior_attrs( $behavior ); ?>>
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
	<div class="coming-soon-inner">
		<div class="cs-logo" data-phantom="brand_logo"><?php echo esc_html( $brand ); ?></div>
		<h1 class="cs-title" data-phantom="page_title"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $subtitle ) : ?>
			<p class="cs-subtitle" data-phantom="page_description"><?php echo esc_html( $subtitle ); ?></p>
		<?php endif; ?>

		<div class="countdown" id="<?php echo esc_attr( $aether_countdown_id ); ?>" data-target="<?php echo esc_attr( $target ); ?>">
			<div class="countdown-box">
				<div class="countdown-number" data-unit="days">00</div>
				<div class="countdown-label"><?php esc_html_e( 'Days', 'aureon' ); ?></div>
			</div>
			<div class="countdown-box">
				<div class="countdown-number" data-unit="hours">00</div>
				<div class="countdown-label"><?php esc_html_e( 'Hours', 'aureon' ); ?></div>
			</div>
			<div class="countdown-box">
				<div class="countdown-number" data-unit="minutes">00</div>
				<div class="countdown-label"><?php esc_html_e( 'Minutes', 'aureon' ); ?></div>
			</div>
			<div class="countdown-box">
				<div class="countdown-number" data-unit="seconds">00</div>
				<div class="countdown-label"><?php esc_html_e( 'Seconds', 'aureon' ); ?></div>
			</div>
		</div>

		<form class="notify-form" id="notifyForm" aria-label="<?php esc_attr_e( 'Notify me form', 'aureon' ); ?>">
			<input type="email" class="notify-input" id="notifyEmail" placeholder="<?php esc_attr_e( 'Enter your email', 'aureon' ); ?>" aria-label="<?php esc_attr_e( 'Email address', 'aureon' ); ?>" required>
			<button type="submit" class="notify-btn"><?php esc_html_e( 'Get Notified', 'aureon' ); ?></button>
		</form>

		<?php if ( ! empty( $socials ) ) : ?>
			<div class="cs-socials">
				<?php foreach ( $socials as $social ) : ?>
					<?php if ( ! empty( $social['url'] ) ) : ?>
						<a href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $social['label'] ); ?>"><i class="fab <?php echo esc_attr( $social['icon'] ); ?>"></i></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>