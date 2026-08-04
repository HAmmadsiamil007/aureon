<?php
/**
 * Comments — comment list shell (rendering delegated to WP hooks).
 *
 * Expected data: title, items (author, avatar, date, text).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-comments">
	<h2 class="phantom-comments__title"><?php echo $view->e( $view->prop( 'title', 'Comments' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<ul class="phantom-comments__list">
		<?php foreach ( (array) $view->prop( 'items', array() ) as $comment ) : ?>
			<li class="phantom-comments__item">
				<?php if ( ! empty( $comment['avatar'] ) ) : ?>
					<img class="phantom-comments__avatar" src="<?php echo $view->url( $comment['avatar'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="" loading="lazy" width="48" height="48" />
				<?php endif; ?>
				<div class="phantom-comments__body">
					<header class="phantom-comments__meta">
						<span class="phantom-comments__author"><?php echo $view->e( $comment['author'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
						<?php if ( ! empty( $comment['date'] ) ) : ?>
							<time class="phantom-comments__date" datetime="<?php echo $view->attr( $comment['date'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"><?php echo $view->e( $comment['date'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></time>
						<?php endif; ?>
					</header>
					<p class="phantom-comments__text"><?php echo $view->e( $comment['text'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
