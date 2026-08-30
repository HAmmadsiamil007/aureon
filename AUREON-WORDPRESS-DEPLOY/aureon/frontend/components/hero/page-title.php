<?php
/**
 * Page title — subpage hero: label eyebrow, title, subtitle.
 *
 * Key:    'hero/page-title'
 * Source: all subpages (e.g. shop.html) `.page-title`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $label     Eyebrow label. Default ''.`
 * - `string $title     Page title. Default ''.`
 * - `string $subtitle  Subtitle. Default ''.`
 * - `array $behavior   Behavior whitelist. Default [].`
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

$label    = isset( $componentData['label'] ) ? $componentData['label'] : '';
$title    = isset( $componentData['title'] ) ? $componentData['title'] : '';
$subtitle = isset( $componentData['subtitle'] ) ? $componentData['subtitle'] : '';
$behavior = isset( $componentData['behavior'] ) ? (array) $componentData['behavior'] : array();
?>
<section class="page-hero" data-phantom-bg="hero" <?php echo aether_behavior_attrs( $behavior ); ?>>
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
		<?php if ( $label ) : ?>
			<span class="section-label" data-phantom="section_label"><?php echo esc_html( $label ); ?></span>
		<?php endif; ?>
		<?php if ( $title ) : ?>
			<h1 class="page-hero-title" data-phantom="page_title" data-motion-text="words"><?php echo esc_html( $title ); ?></h1>
		<?php endif; ?>
		<?php if ( $subtitle ) : ?>
			<p class="page-hero-subtitle" data-phantom="page_description" data-motion-text="lines"><?php echo esc_html( $subtitle ); ?></p>
		<?php endif; ?>
	</div>
</section>
