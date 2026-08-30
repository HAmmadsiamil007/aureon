<?php
/**
 * Review card — testimonial card: avatar, name, stars, quote.
 *
 * Key:    'card/review'
 * Source: testimonials.html `.card-review`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $name      Reviewer name. Default ''.`
 * - `string $role      Reviewer role. Default ''.`
 * - `bool $verified  Verification badge. Default false.`
 * - `float $stars     Star score 0-5. Default 0.`
 * - `string $title     Review title. Default ''.`
 * - `string $quote     Review quote. Default ''.`
 * - `string $date      Date label. Default ''.`
 * - `string $image     Avatar image URL. Default ''.`
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

$name     = isset( $componentData['name'] ) ? $componentData['name'] : '';
$role     = isset( $componentData['role'] ) ? $componentData['role'] : '';
$verified = ! empty( $componentData['verified'] );
$stars    = isset( $componentData['stars'] ) ? (float) $componentData['stars'] : 0;
$title    = isset( $componentData['title'] ) ? $componentData['title'] : '';
$quote    = isset( $componentData['quote'] ) ? $componentData['quote'] : '';
$date     = isset( $componentData['date'] ) ? $componentData['date'] : '';
$image    = isset( $componentData['image'] ) ? $componentData['image'] : '';

if ( ! $name ) {
	return;
}

$initials = '';
foreach ( preg_split( '/\s+/', trim( $name ) ) as $word ) {
	$initials .= mb_strtoupper( mb_substr( $word, 0, 1 ) );
}
?>
<div class="swiper-slide">
	<div class="review-card" data-tilt>
		<div class="review-header">
			<?php if ( $image ) : ?>
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $name ); ?>" class="review-avatar">
			<?php else : ?>
				<div class="review-avatar"><?php echo esc_html( $initials ); ?></div>
			<?php endif; ?>
			<div class="review-meta">
				<strong class="review-author"><?php echo esc_html( $name ); ?></strong>
				<?php if ( $role ) : ?>
					<span class="review-role"><?php echo esc_html( $role ); ?></span>
				<?php endif; ?>
			</div>
			<?php if ( $verified ) : ?>
				<div class="review-verified"><i class="fas fa-check-circle"></i> Verified</div>
			<?php endif; ?>
		</div>
		<?php if ( $stars > 0 ) : ?>
			<div class="review-stars">
				<?php aether_render_component( 'commerce/rating', array( 'stars' => $stars ) ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $title ) : ?>
			<h4 class="review-title"><?php echo esc_html( $title ); ?></h4>
		<?php endif; ?>
		<?php if ( $quote ) : ?>
			<p class="review-text"><?php echo esc_html( $quote ); ?></p>
		<?php endif; ?>
		<?php if ( $date ) : ?>
			<span class="review-date"><?php echo esc_html( $date ); ?></span>
		<?php endif; ?>
	</div>
</div>
