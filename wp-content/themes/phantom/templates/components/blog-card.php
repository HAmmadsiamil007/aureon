<?php
/**
 * Blog card — single post preview card.
 *
 * Expected data: image, category, title, url, excerpt, date, author.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<article class="phantom-card phantom-card--post">
	<?php if ( $view->prop( 'image' ) ) : ?>
		<a class="phantom-card__media" href="<?php echo $view->url( $view->prop( 'url', '#' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" tabindex="-1" aria-hidden="true">
			<img src="<?php echo $view->url( $view->prop( 'image' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="" loading="lazy" />
		</a>
	<?php endif; ?>
	<div class="phantom-card__body">
		<?php if ( $view->prop( 'category' ) ) : ?>
			<span class="phantom-card__category"><?php echo $view->e( $view->prop( 'category' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
		<?php endif; ?>
		<h3 class="phantom-card__title">
			<a href="<?php echo $view->url( $view->prop( 'url', '#' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( $view->prop( 'title', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
		</h3>
		<?php if ( $view->prop( 'excerpt' ) ) : ?>
			<p class="phantom-card__excerpt"><?php echo $view->e( $view->prop( 'excerpt' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
		<?php endif; ?>
		<footer class="phantom-card__meta">
			<?php if ( $view->prop( 'date' ) ) : ?>
				<time class="phantom-card__date" datetime="<?php echo $view->attr( $view->prop( 'date' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"><?php echo $view->e( $view->prop( 'date' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></time>
			<?php endif; ?>
			<?php if ( $view->prop( 'author' ) ) : ?>
				<span class="phantom-card__author"><?php echo $view->e( $view->prop( 'author' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
			<?php endif; ?>
		</footer>
	</div>
</article>
