<?php
/**
 * HeroSlider — accessible carousel of hero slides with pager/arrows.
 *
 * Expected data: slides (list of hero props: title, text, eyebrow, media,
 * media_alt, actions).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-hero-slider" data-lumina-hero-slider>
	<div class="lumina-hero-slider__viewport">
		<?php foreach ( (array) $view->prop( 'slides' ) as $index => $slide ) : ?>
			<?php
			$slide = is_array( $slide ) ? $slide : array();
			?>
			<div class="lumina-hero-slider__slide<?php echo 0 === $index ? ' is-active' : ''; ?>" data-lumina-slide>
				<?php if ( isset( $slide['media'] ) && $slide['media'] ) : ?>
					<img class="lumina-hero-slider__media" src="<?php echo $view->url( (string) $slide['media'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="<?php echo $view->attr( isset( $slide['media_alt'] ) ? (string) $slide['media_alt'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" loading="lazy" />
				<?php endif; ?>

				<div class="lumina-hero-slider__content">
					<?php if ( ! empty( $slide['eyebrow'] ) ) : ?>
						<p class="lumina-hero-slider__eyebrow"><?php echo $view->e( (string) $slide['eyebrow'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $slide['title'] ) ) : ?>
						<h2 class="lumina-hero-slider__title"><?php echo $view->e( (string) $slide['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
					<?php endif; ?>
					<?php if ( ! empty( $slide['text'] ) ) : ?>
						<p class="lumina-hero-slider__text"><?php echo $view->e( (string) $slide['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="lumina-hero-slider__controls">
		<button class="lumina-hero-slider__arrow lumina-hero-slider__arrow--prev" type="button" aria-label="<?php echo $view->attr( 'Previous slide' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-lumina-slider-prev>&lsaquo;</button>
		<button class="lumina-hero-slider__arrow lumina-hero-slider__arrow--next" type="button" aria-label="<?php echo $view->attr( 'Next slide' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-lumina-slider-next>&rsaquo;</button>
	</div>
</div>
