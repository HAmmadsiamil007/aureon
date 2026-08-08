<?php
/**
 * Reviews section — score summary + swiper testimonial carousel.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'reviews', array(
	'template' => 'sections/section-reviews.php',
	'adapter'  => 'adapter-testimonials.php',
	'behavior' => array( 'reveal-group' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$items    = isset( $sectionData['items'] ) ? (array) $sectionData['items'] : array();
$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();

if ( empty( $items ) ) {
	return;
}

$score = isset( $sectionData['score'] ) ? (float) $sectionData['score'] : 0;
$count = isset( $sectionData['count'] ) ? (int) $sectionData['count'] : 0;
?>
<section class="reviews" id="reviews" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="container">
		<div class="section-header">
			<?php
			aether_render_component( 'section/header', array(
				'label'    => isset( $sectionData['label'] ) ? $sectionData['label'] : __( 'Reviews', 'aureon' ),
				'title'    => isset( $sectionData['title'] ) ? $sectionData['title'] : __( 'What Athletes Say', 'aureon' ),
				'subtitle' => '',
				'behavior' => $behavior,
			) );
			?>
			<?php if ( $score > 0 ) : ?>
				<div class="reviews-summary">
					<div class="reviews-score">
						<span class="score-number"><?php echo esc_html( number_format_i18n( $score, 1 ) ); ?></span>
						<div class="score-stars">
							<?php aether_render_component( 'commerce/rating', array( 'stars' => $score ) ); ?>
						</div>
						<?php if ( $count > 0 ) : ?>
							<span class="score-count"><?php echo esc_html( sprintf( __( 'Based on %d reviews', 'aureon' ), $count ) ); ?></span>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<div class="swiper reviews-swiper">
			<div class="swiper-wrapper">
				<?php foreach ( $items as $review ) : ?>
					<?php aether_render_component( 'card/review', $review ); ?>
				<?php endforeach; ?>
			</div>
			<div class="reviews-pagination"></div>
		</div>
	</div>
</section>
