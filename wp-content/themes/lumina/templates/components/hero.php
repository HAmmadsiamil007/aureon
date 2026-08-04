<?php
/**
 * Hero — full-width hero section with eyebrow, title, text, optional media,
 * actions slot and reduced-motion-safe reveal animation hook.
 *
 * Expected data: eyebrow, title, text, media, media_alt, height, class
 * (variant-merged), actions (slot HTML).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<section
	class="<?php echo $view->attr( $view->prop( 'class', 'lumina-hero' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	data-lumina-height="<?php echo $view->attr( $view->prop( 'height', 'medium' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	data-lumina-anim="reveal"
>
	<div class="lumina-hero__inner">
		<?php if ( $view->prop( 'eyebrow' ) ) : ?>
			<p class="lumina-hero__eyebrow"><?php echo $view->e( $view->prop( 'eyebrow' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
		<?php endif; ?>

		<?php if ( $view->prop( 'title' ) ) : ?>
			<h1 class="lumina-hero__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h1>
		<?php endif; ?>

		<?php if ( $view->prop( 'text' ) ) : ?>
			<p class="lumina-hero__text"><?php echo $view->e( $view->prop( 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
		<?php endif; ?>

		<?php if ( $view->prop( 'actions' ) ) : ?>
			<div class="lumina-hero__actions"><?php echo $view->prop( 'actions' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?></div>
		<?php endif; ?>
	</div>

	<?php if ( $view->prop( 'media' ) ) : ?>
		<figure class="lumina-hero__media">
			<img
				src="<?php echo $view->url( $view->prop( 'media' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"
				alt="<?php echo $view->attr( $view->prop( 'media_alt', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
				loading="lazy"
			/>
		</figure>
	<?php endif; ?>
</section>
