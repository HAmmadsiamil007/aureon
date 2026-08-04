<?php
/**
 * Social icons — accessible social link strip.
 *
 * Expected data: items (name, url).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<ul class="lumina-social-icons">
	<?php foreach ( (array) $view->prop( 'items', array() ) as $item ) : ?>
		<li class="lumina-social-icons__item">
			<a
				class="lumina-social-icons__link"
				href="<?php echo $view->url( $item['url'] ?? '#' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"
				aria-label="<?php echo $view->attr( $item['name'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
				rel="noopener"
			>
				<?php echo $view->e( $item['name'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
