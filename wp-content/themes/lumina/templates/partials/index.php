<?php
/**
 * Index — last-resort fallback partial for the Phase 6 partial chain.
 *
 * Rendered only when a requested partial and its named fallback both miss.
 * Escapes every field via the ViewContext helpers.
 *
 * @package Lumina\Core\Templates
 */

declare( strict_types=1 );
?>
<section class="lumina-partial lumina-partial--fallback" data-lumina-partial="index">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<p><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
</section>
