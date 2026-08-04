<?php
/**
 * Copyright — footer legal line.
 *
 * Expected data: text, links (label + url).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-copyright">
	<?php if ( $view->prop( 'text' ) ) : ?>
		<p class="lumina-copyright__text"><?php echo $view->e( $view->prop( 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
	<?php if ( $view->prop( 'links' ) ) : ?>
		<ul class="lumina-copyright__links">
			<?php foreach ( (array) $view->prop( 'links', array() ) as $link ) : ?>
				<li class="lumina-copyright__link">
					<a href="<?php echo $view->url( $link['url'] ?? '#' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( $link['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
