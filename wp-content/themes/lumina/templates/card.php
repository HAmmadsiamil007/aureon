<?php
/**
 * Card — minimal presentational fixture used to validate the Phase 4 Render
 * Engine (and reused by the Component Registry in Phase 5).
 *
 * Consumes a ViewContext ($view): every field is escaped through the context
 * helpers, so no raw user data can reach output. Presentational only — no
 * business logic, no WordPress globals (plan §Phase 4: renderless engine).
 *
 * Expected data: title, link, excerpt.
 *
 * @package Lumina\Core\Render
 */

declare( strict_types=1 );
?>
<article class="lumina-card" data-lumina-card>
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h3 class="lumina-card__title">
			<a href="<?php echo $view->url( $view->prop( 'link' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
				<?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
			</a>
		</h3>
	<?php endif; ?>

	<?php if ( $view->prop( 'excerpt' ) ) : ?>
		<p class="lumina-card__excerpt"><?php echo $view->e( $view->prop( 'excerpt' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
</article>
