<?php
/**
 * VideoBanner — banner with a lazy-loaded <video> background (muted,
 * playsInline, reduced-motion friendly via poster fallback).
 *
 * Expected data: title, text, video, poster, link, actions (slot HTML).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-video-banner" data-phantom-anim="reveal">
	<?php if ( $view->prop( 'video' ) ) : ?>
		<video class="phantom-video-banner__video" autoplay muted loop playsinline poster="<?php echo $view->url( $view->prop( 'poster', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" aria-hidden="true">
			<source src="<?php echo $view->url( $view->prop( 'video' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" type="video/mp4" />
		</video>
	<?php endif; ?>

	<div class="phantom-video-banner__content">
		<?php if ( $view->prop( 'title' ) ) : ?>
			<h2 class="phantom-video-banner__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
		<?php endif; ?>
		<?php if ( $view->prop( 'text' ) ) : ?>
			<p class="phantom-video-banner__text"><?php echo $view->e( $view->prop( 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
		<?php endif; ?>
		<?php if ( $view->prop( 'actions' ) ) : ?>
			<div class="phantom-video-banner__actions"><?php echo $view->prop( 'actions' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?></div>
		<?php endif; ?>
	</div>
</section>
