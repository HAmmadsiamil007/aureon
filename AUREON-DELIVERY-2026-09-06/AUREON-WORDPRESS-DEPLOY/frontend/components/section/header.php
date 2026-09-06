<?php
/**
 * Section header — eyebrow + title + subtitle heading block.
 *
 * Key:    'section/header'
 * Source: engine-native (section label pattern across source pages)
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $label     Eyebrow label. Default ''.`
 * - `string $title     Section title. Default ''.`
 * - `string $subtitle  Subtitle. Default ''.`
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
?>
<div class="section-header">
	<?php if ( $label ) : ?>
		<span class="section-label" data-motion-text="words"><?php echo esc_html( $label ); ?></span>
	<?php endif; ?>
	<?php if ( $title ) : ?>
		<h2 class="section-title" data-motion-text="words"><?php echo esc_html( $title ); ?></h2>
	<?php endif; ?>
	<?php if ( $subtitle ) : ?>
		<p class="section-subtitle" data-motion-text="lines"><?php echo esc_html( $subtitle ); ?></p>
	<?php endif; ?>
</div>
