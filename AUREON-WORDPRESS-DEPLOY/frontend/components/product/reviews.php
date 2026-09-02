<?php
/**
 * Product reviews — review list with rating summary header.
 *
 * Key:    'product/reviews'
 * Source: product-detail.html `.pd-reviews`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $title  Section title. Default 'Customer Reviews'.`
 * - `float $score   Average score. Default 0.`
 * - `int $count    Review count. Default 0.`
 * - `array $bars   Distribution bar schema (label/percent). Default [].`
 * - `array $items   Review schema (author/stars/date/body). Default [].`
 *
 * Slots:  'commerce/rating'
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$title      = isset( $componentData['title'] ) ? $componentData['title'] : __( 'Customer Reviews', 'aureon' );
$score      = isset( $componentData['score'] ) ? (float) $componentData['score'] : 0;
$count      = isset( $componentData['count'] ) ? (int) $componentData['count'] : 0;
$bars       = isset( $componentData['bars'] ) ? (array) $componentData['bars'] : array();
$items      = isset( $componentData['items'] ) ? (array) $componentData['items'] : array();

if ( ! $score && empty( $items ) ) {
	return;
}
?>
<section class="pd-reviews">
	<div class="container">
		<h2 class="pd-section-title"><?php echo esc_html( $title ); ?></h2>
		<div class="pd-gold-line"></div>

		<div class="pd-reviews-grid">
			<?php if ( $score > 0 ) : ?>
				<div class="pd-reviews-summary">
					<div class="pd-reviews-score">
						<span class="pd-score-number"><?php echo esc_html( number_format_i18n( $score, 1 ) ); ?></span>
						<div class="pd-score-stars"><?php aether_render_component( 'commerce/rating', array( 'stars' => $score ) ); ?></div>
						<span class="pd-score-count"><?php echo esc_html( sprintf( __( 'Based on %d reviews', 'aureon' ), $count ) ); ?></span>
					</div>
					<?php if ( ! empty( $bars ) ) : ?>
						<div class="pd-reviews-bars">
							<?php foreach ( $bars as $bar ) : ?>
								<div class="pd-bar-row">
									<span><?php echo (int) $bar['star']; ?> <i class="fas fa-star"></i></span>
									<div class="pd-bar-track"><div class="pd-bar-fill" style="width: <?php echo (int) $bar['percent']; ?>%"></div></div>
									<span><?php echo (int) $bar['count']; ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $items ) ) : ?>
				<div class="pd-reviews-list">
					<?php foreach ( $items as $review ) : ?>
						<div class="pd-review-card">
							<div class="pd-review-header">
								<div class="pd-review-avatar"><?php echo esc_html( $review['initials'] ); ?></div>
								<div>
									<span class="pd-review-name"><?php echo esc_html( $review['name'] ); ?></span>
									<span class="pd-review-date"><?php echo esc_html( $review['meta'] ); ?></span>
								</div>
							</div>
							<div class="pd-review-stars"><?php aether_render_component( 'commerce/rating', array( 'stars' => (float) $review['stars'] ) ); ?></div>
							<h4 class="pd-review-title"><?php echo esc_html( $review['title'] ); ?></h4>
							<p class="pd-review-text"><?php echo esc_html( $review['text'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
