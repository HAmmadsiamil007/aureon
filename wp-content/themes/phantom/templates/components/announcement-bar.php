<?php
/**
 * AnnouncementBar — slim promo strip above the header; optional dismiss.
 *
 * Expected data: message, link, dismissible.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-announcement-bar" data-phantom-announcement>
	<?php if ( $view->prop( 'link' ) ) : ?>
		<a class="phantom-announcement-bar__link" href="<?php echo $view->url( $view->prop( 'link' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
			<?php echo $view->e( $view->prop( 'message' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
		</a>
	<?php else : ?>
		<p class="phantom-announcement-bar__message"><?php echo $view->e( $view->prop( 'message' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>

	<?php if ( $view->prop( 'dismissible' ) ) : ?>
		<button class="phantom-announcement-bar__dismiss" type="button" aria-label="<?php echo $view->attr( 'Dismiss' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-phantom-announcement-dismiss>
			<span aria-hidden="true">&times;</span>
		</button>
	<?php endif; ?>
</div>
