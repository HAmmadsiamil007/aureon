<?php
/**
 * Ferm Living newsletter section — signup form.
 *
 * Used on homepage between content and footer.
 * Source: fermliving.com footer newsletter area — heading, description text,
 * and embedded signup form (Klaviyo in frozen source, replaced by Aureon newsletter).
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

aether_register_section( 'newsletter', array(
	'template' => 'sections/section-newsletter.php',
	'adapter'  => 'adapter-options.php',
	'behavior' => array(),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return;
}

$heading = isset( $sectionData['heading'] ) ? $sectionData['heading'] : '';
$text    = isset( $sectionData['text'] ) ? $sectionData['text'] : '';

if ( empty( $heading ) && empty( $text ) ) {
	return;
}
?>
<section class="section-newsletter" aria-label="<?php esc_attr_e( 'Newsletter signup', 'aureon' ); ?>">
	<div class="limit">
		<?php if ( $heading ) : ?>
			<h2 class="section-newsletter-title"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( $text ) : ?>
			<p class="section-newsletter-text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>

		<?php /* Aureon newsletter form */ ?>
		<?php if ( function_exists( 'aether_render_component' ) ) : ?>
			<?php aether_render_component( 'newsletter/form', array(
				'heading' => $heading,
				'text'    => $text,
			) ); ?>
		<?php endif; ?>
	</div>
</section>
