<?php
/**
 * AnnouncementBar — slim promo strip above the header; optional dismiss.
 *
 * Expected data: message, link, dismissible.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-announcement-bar" data-lumina-announcement>
	<?php if ( $view->prop( 'link' ) ) : ?>
		<a class="lumina-announcement-bar__link" href="<?php echo $view->url( $view->prop( 'link' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
			<?php echo $view->e( $view->prop( 'message' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
		</a>
	<?php else : ?>
		<p class="lumina-announcement-bar__message"><?php echo $view->e( $view->prop( 'message' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>

	<?php if ( $view->prop( 'dismissible' ) ) : ?>
		<button class="lumina-announcement-bar__dismiss" type="button" aria-label="<?php echo $view->attr( 'Dismiss' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-lumina-announcement-dismiss>
			<span aria-hidden="true">&times;</span>
		</button>
	<?php endif; ?>
</div>
