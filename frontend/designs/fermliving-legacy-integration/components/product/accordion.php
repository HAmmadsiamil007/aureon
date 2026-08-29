<?php
/**
 * Ferm Living product accordion — Materials, Fit, Performance, Care sections.
 *
 * Replaces AETHER specs component with Ferm-style collapsible accordion items.
 *
 * Key:    'product/accordion' (override)
 * Source: fermliving.com product page accordion
 * Props:  items (icon, title, body), title.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$title = isset( $componentData['title'] ) ? $componentData['title'] : __( 'Product Details', 'aureon' );
$items = isset( $componentData['items'] ) ? (array) $componentData['items'] : array();

if ( empty( $items ) ) {
	return;
}
?>
<section class="pd-specs">
	<div class="container">
		<h2 class="pd-section-title"><?php echo esc_html( $title ); ?></h2>
		<div class="pd-gold-line"></div>
		<div class="pd-accordion">
			<?php foreach ( $items as $i => $item ) :
				$icon  = isset( $item['icon'] ) ? $item['icon'] : '';
				$label = isset( $item['title'] ) ? $item['title'] : '';
				$body  = isset( $item['body'] ) ? $item['body'] : '';
				if ( empty( $label ) && empty( $body ) ) {
					continue;
				}
				?>
				<div class="pd-accordion-item<?php echo 0 === $i ? ' active' : ''; ?>">
					<button class="pd-accordion-header" aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>">
						<span>
							<?php if ( $icon ) : ?>
								<i class="fas <?php echo esc_attr( $icon ); ?>"></i>
							<?php endif; ?>
							<?php echo esc_html( $label ); ?>
						</span>
						<i class="fas fa-chevron-down pd-accordion-icon"></i>
					</button>
					<div class="pd-accordion-body" <?php echo 0 === $i ? 'style="max-height: 500px;"' : ''; ?>>
						<?php if ( $body ) : ?>
							<?php echo wp_kses_post( $body ); ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
