<?php
/**
 * Card — slot-aware presentational component used to validate the Phase 5
 * Component Registry (slots, variants, dependencies).
 *
 * Consumes a ViewContext ($view): text/attribute fields are escaped through
 * the context helpers. The `actions` slot is rendered by the Registry from
 * child components (each escaped at the leaf) and arrives as trusted HTML —
 * echoed raw by design (see Registry::render()).
 *
 * Expected data: title, link, excerpt, accent, actions (slot HTML).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<article class="lumina-card<?php echo $view->prop( 'accent' ) ? ' lumina-card--accent' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literal class, boolean-gated. ?>" data-lumina-card>
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h3 class="lumina-card__title">
			<?php if ( $view->prop( 'link' ) ) : ?>
				<a href="<?php echo $view->url( $view->prop( 'link' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
					<?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
				</a>
			<?php else : ?>
				<?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
			<?php endif; ?>
		</h3>
	<?php endif; ?>

	<?php if ( $view->prop( 'excerpt' ) ) : ?>
		<p class="lumina-card__excerpt"><?php echo $view->e( $view->prop( 'excerpt' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>

	<?php if ( $view->prop( 'actions' ) ) : ?>
		<div class="lumina-card__actions"><?php echo $view->prop( 'actions' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?></div>
	<?php endif; ?>
</article>
