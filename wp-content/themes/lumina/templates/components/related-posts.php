<?php
/**
 * Related posts — post preview list.
 *
 * Expected data: title, posts (card props).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<aside class="lumina-related-posts" aria-label="<?php echo $view->attr( $view->prop( 'title', 'Related posts' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
	<h2 class="lumina-related-posts__title"><?php echo $view->e( $view->prop( 'title', 'Related posts' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<ul class="lumina-related-posts__list">
		<?php foreach ( (array) $view->prop( 'posts', array() ) as $post ) : ?>
			<li class="lumina-related-posts__item">
				<a href="<?php echo $view->url( $post['url'] ?? '#' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( $post['title'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</aside>
