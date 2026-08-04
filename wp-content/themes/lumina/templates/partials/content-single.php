<?php
/**
 * Content single — minimal presentational partial.
 *
 * Phase 6 (Template System) fixture: every field is escaped via the
 * ViewContext helpers; pure presentation, no business logic.
 *
 * Expected data: title, excerpt (both optional).
 *
 * @package Lumina\Core\Templates
 */

declare( strict_types=1 );
?>
<article class="lumina-entry" data-lumina-entry>
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h1 class="lumina-entry__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h1>
	<?php endif; ?>

	<?php if ( $view->prop( 'excerpt' ) ) : ?>
		<p class="lumina-entry__excerpt"><?php echo $view->e( $view->prop( 'excerpt' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
</article>
