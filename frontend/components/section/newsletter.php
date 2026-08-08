<?php
/**
 * Newsletter section — section wrapper delegating to the newsletter form.
 *
 * Key:    'section/newsletter'
 * Source: index.html `.newsletter`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $label         Eyebrow label. Default ''.`
 * - `string $title         Section title. Default ''.`
 * - `string $subtitle      Subtitle. Default ''.`
 * - `string $button_text   Button label (forwarded). Default ''.`
 * - `string $note          Lock-in note (forwarded). Default ''.`
 * - `string $success_text  Success message (forwarded). Default ''.`
 *
 * Slots:  'form/newsletter'
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
<div class="newsletter-glow" aria-hidden="true"></div>
<div class="container">
	<div class="newsletter-inner">
		<?php if ( $label ) : ?>
			<span class="section-label" data-motion-text="words"><?php echo esc_html( $label ); ?></span>
		<?php endif; ?>
		<?php if ( $title ) : ?>
			<h2 class="newsletter-title" data-motion-text="words"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( $subtitle ) : ?>
			<p class="newsletter-text"><?php echo esc_html( $subtitle ); ?></p>
		<?php endif; ?>
		<?php aether_render_component( 'form/newsletter', array(
			'button_text' => isset( $componentData['button_text'] ) ? $componentData['button_text'] : '',
			'note'        => isset( $componentData['note'] ) ? $componentData['note'] : '',
			'success_text' => isset( $componentData['success_text'] ) ? $componentData['success_text'] : '',
		) ); ?>
	</div>
</div>
