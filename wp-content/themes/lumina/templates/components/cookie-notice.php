<?php
/**
 * CookieNotice — consent bar with accept action and optional policy link.
 *
 * Expected data: message, accept_label, policy_url.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-cookie-notice" role="region" aria-label="<?php echo $view->attr( 'Cookie notice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-lumina-cookie-notice>
	<p class="lumina-cookie-notice__message"><?php echo $view->e( $view->prop( 'message' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>

	<div class="lumina-cookie-notice__actions">
		<?php if ( $view->prop( 'policy_url' ) ) : ?>
			<a class="lumina-cookie-notice__policy" href="<?php echo $view->url( $view->prop( 'policy_url' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
				<?php echo $view->e( 'Privacy policy' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
			</a>
		<?php endif; ?>
		<button class="lumina-cookie-notice__accept" type="button" data-lumina-cookie-accept>
			<?php echo $view->e( $view->prop( 'accept_label', 'Accept' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
		</button>
	</div>
</div>
