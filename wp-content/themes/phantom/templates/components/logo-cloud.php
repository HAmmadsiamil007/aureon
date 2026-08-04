<?php
/**
 * Logo cloud — brand trust strip.
 *
 * Expected data: title, logos (src, alt, url).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-logo-cloud">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="phantom-logo-cloud__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<div class="phantom-logo-cloud__list">
		<?php foreach ( (array) $view->prop( 'logos', array() ) as $logo ) : ?>
			<?php if ( ! empty( $logo['url'] ) ) : ?>
				<a class="phantom-logo-cloud__item" href="<?php echo $view->url( $logo['url'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" aria-label="<?php echo $view->attr( $logo['alt'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
					<img src="<?php echo $view->url( $logo['src'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="<?php echo $view->attr( $logo['alt'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" loading="lazy" />
				</a>
			<?php else : ?>
				<img class="phantom-logo-cloud__item" src="<?php echo $view->url( $logo['src'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="<?php echo $view->attr( $logo['alt'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" loading="lazy" />
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</section>
