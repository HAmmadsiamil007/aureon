<?php
/**
 * Content — generic presentational partial (Phase 6 fallback tier).
 *
 * Escapes every field via the ViewContext helpers; pure presentation.
 *
 * Expected data: title, link, excerpt (all optional).
 *
 * @package Lumina\Core\Templates
 */

declare( strict_types=1 );
?>
<article class="lumina-entry" data-lumina-entry>
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="lumina-entry__title">
			<?php if ( $view->prop( 'link' ) ) : ?>
				<a href="<?php echo $view->url( $view->prop( 'link' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
					<?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
				</a>
			<?php else : ?>
				<?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
			<?php endif; ?>
		</h2>
	<?php endif; ?>

	<?php if ( $view->prop( 'excerpt' ) ) : ?>
		<p class="lumina-entry__excerpt"><?php echo $view->e( $view->prop( 'excerpt' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
</article>
