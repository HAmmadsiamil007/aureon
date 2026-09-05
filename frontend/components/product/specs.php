<?php
/**
 * Product specs — technical specification table.
 *
 * Key:    'product/specs'
 * Source: product-detail.html `.pd-specs`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $title  Section title. Default 'Specifications'.`
 * - `array $items  Spec row schema (label/value). Default [].`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  inline `max-height` on the open accordion row (from design) — move to CSS class in M3.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$title = isset( $componentData['title'] ) ? $componentData['title'] : __( 'Tech Specs', 'aureon' );
$items = isset( $componentData['items'] ) ? (array) $componentData['items'] : array();

if ( empty( $items ) ) {
	return;
}
?>
<section class="pd-specs">
	<div class="container">
		<h2 class="pd-section-title" data-phantom="section_title"><?php echo esc_html( $title ); ?></h2>
		<div class="pd-gold-line"></div>
		<div class="pd-accordion">
			<?php foreach ( $items as $i => $item ) : ?>
				<div class="pd-accordion-item<?php echo 0 === $i ? ' active' : ''; ?>">
					<button class="pd-accordion-header">
						<span><i class="fas <?php echo esc_attr( $item['icon'] ); ?>"></i> <?php echo esc_html( $item['title'] ); ?></span>
						<i class="fas fa-chevron-down pd-accordion-icon"></i>
					</button>
					<div class="pd-accordion-body"<?php echo 0 === $i ? ' style="max-height: 200px;"' : ''; ?>>
						<p><?php echo esc_html( $item['body'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
