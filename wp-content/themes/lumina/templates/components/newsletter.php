<?php
/**
 * Newsletter — accessible email capture form.
 *
 * Expected data: title, text, placeholder, submit_label, success, action.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<section class="lumina-newsletter">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="lumina-newsletter__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<?php if ( $view->prop( 'text' ) ) : ?>
		<p class="lumina-newsletter__text"><?php echo $view->e( $view->prop( 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
	<form class="lumina-newsletter__form" method="post" action="<?php echo $view->url( $view->prop( 'action', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
		<label class="screen-reader-text" for="lumina-newsletter-email"><?php echo $view->e( $view->prop( 'placeholder', 'Email address' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></label>
		<input
			id="lumina-newsletter-email"
			class="lumina-newsletter__input"
			type="email"
			name="email"
			placeholder="<?php echo $view->attr( $view->prop( 'placeholder', 'Email address' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
			required
		/>
		<button type="submit" class="lumina-btn lumina-btn--primary"><?php echo $view->e( $view->prop( 'submit_label', 'Subscribe' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></button>
	</form>
	<?php if ( $view->prop( 'success' ) ) : ?>
		<p class="lumina-newsletter__success" role="status"><?php echo $view->e( $view->prop( 'success' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
</section>
