<?php
/**
 * Comments — comment list shell (rendering delegated to WP hooks).
 *
 * Expected data: title, items (author, avatar, date, text).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<section class="lumina-comments">
	<h2 class="lumina-comments__title"><?php echo $view->e( $view->prop( 'title', 'Comments' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<ul class="lumina-comments__list">
		<?php foreach ( (array) $view->prop( 'items', array() ) as $comment ) : ?>
			<li class="lumina-comments__item">
				<?php if ( ! empty( $comment['avatar'] ) ) : ?>
					<img class="lumina-comments__avatar" src="<?php echo $view->url( $comment['avatar'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="" loading="lazy" width="48" height="48" />
				<?php endif; ?>
				<div class="lumina-comments__body">
					<header class="lumina-comments__meta">
						<span class="lumina-comments__author"><?php echo $view->e( $comment['author'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
						<?php if ( ! empty( $comment['date'] ) ) : ?>
							<time class="lumina-comments__date" datetime="<?php echo $view->attr( $comment['date'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"><?php echo $view->e( $comment['date'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></time>
						<?php endif; ?>
					</header>
					<p class="lumina-comments__text"><?php echo $view->e( $comment['text'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
