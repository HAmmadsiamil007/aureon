<?php
/**
 * Lumen section header — eyebrow + title + subtitle (M10 proof pack).
 *
 * Key:    'section/header' (override)
 * Props:  label, title, subtitle (same schema as engine section/header).
 * Contract: keeps .section-header, .section-label, .section-title,
 *           .section-subtitle — styling only; motion-text effects are a
 *           luxury design choice (REMOVE).
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
		<span class="section-label"><?php echo esc_html( $label ); ?></span>
	<?php endif; ?>
	<?php if ( $title ) : ?>
		<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
	<?php endif; ?>
	<?php if ( $subtitle ) : ?>
		<p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
	<?php endif; ?>
</div>