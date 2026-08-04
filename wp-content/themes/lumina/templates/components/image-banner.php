<?php
/**
 * ImageBanner — full-bleed banner with background image and overlay content.
 *
 * Expected data: title, text, image, image_alt, link, actions (slot HTML).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<section class="lumina-image-banner" data-lumina-anim="reveal">
	<?php if ( $view->prop( 'image' ) ) : ?>
		<img class="lumina-image-banner__bg" src="<?php echo $view->url( $view->prop( 'image' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="<?php echo $view->attr( $view->prop( 'image_alt', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" loading="lazy" />
	<?php endif; ?>

	<div class="lumina-image-banner__content">
		<?php if ( $view->prop( 'title' ) ) : ?>
			<h2 class="lumina-image-banner__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
		<?php endif; ?>
		<?php if ( $view->prop( 'text' ) ) : ?>
			<p class="lumina-image-banner__text"><?php echo $view->e( $view->prop( 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
		<?php endif; ?>
		<?php if ( $view->prop( 'actions' ) ) : ?>
			<div class="lumina-image-banner__actions"><?php echo $view->prop( 'actions' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?></div>
		<?php endif; ?>
	</div>
</section>
