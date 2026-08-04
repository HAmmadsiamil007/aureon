<?php
/**
 * Author box — post author bio.
 *
 * Expected data: name, avatar, bio, url.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<aside class="lumina-author-box" aria-label="About the author">
	<?php if ( $view->prop( 'avatar' ) ) : ?>
		<img class="lumina-author-box__avatar" src="<?php echo $view->url( $view->prop( 'avatar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="" loading="lazy" width="64" height="64" />
	<?php endif; ?>
	<div class="lumina-author-box__body">
		<h3 class="lumina-author-box__name">
			<?php if ( $view->prop( 'url' ) ) : ?>
				<a href="<?php echo $view->url( $view->prop( 'url' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( $view->prop( 'name', 'Author' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
			<?php else : ?>
				<?php echo $view->e( $view->prop( 'name', 'Author' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
			<?php endif; ?>
		</h3>
		<?php if ( $view->prop( 'bio' ) ) : ?>
			<p class="lumina-author-box__bio"><?php echo $view->e( $view->prop( 'bio' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
		<?php endif; ?>
	</div>
</aside>
