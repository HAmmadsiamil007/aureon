<?php
/**
 * HeroSlider — accessible carousel of hero slides with pager/arrows.
 *
 * Expected data: slides (list of hero props: title, text, eyebrow, media,
 * media_alt, actions).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-hero-slider" data-phantom-hero-slider>
	<div class="phantom-hero-slider__viewport">
		<?php foreach ( (array) $view->prop( 'slides' ) as $index => $slide ) : ?>
			<?php
			$slide = is_array( $slide ) ? $slide : array();
			?>
			<div class="phantom-hero-slider__slide<?php echo 0 === $index ? ' is-active' : ''; ?>" data-phantom-slide>
				<?php if ( isset( $slide['media'] ) && $slide['media'] ) : ?>
					<img class="phantom-hero-slider__media" src="<?php echo $view->url( (string) $slide['media'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="<?php echo $view->attr( isset( $slide['media_alt'] ) ? (string) $slide['media_alt'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" loading="lazy" />
				<?php endif; ?>

				<div class="phantom-hero-slider__content">
					<?php if ( ! empty( $slide['eyebrow'] ) ) : ?>
						<p class="phantom-hero-slider__eyebrow"><?php echo $view->e( (string) $slide['eyebrow'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $slide['title'] ) ) : ?>
						<h2 class="phantom-hero-slider__title"><?php echo $view->e( (string) $slide['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
					<?php endif; ?>
					<?php if ( ! empty( $slide['text'] ) ) : ?>
						<p class="phantom-hero-slider__text"><?php echo $view->e( (string) $slide['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="phantom-hero-slider__controls">
		<button class="phantom-hero-slider__arrow phantom-hero-slider__arrow--prev" type="button" aria-label="<?php echo $view->attr( 'Previous slide' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-phantom-slider-prev>&lsaquo;</button>
		<button class="phantom-hero-slider__arrow phantom-hero-slider__arrow--next" type="button" aria-label="<?php echo $view->attr( 'Next slide' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-phantom-slider-next>&rsaquo;</button>
	</div>
</div>
