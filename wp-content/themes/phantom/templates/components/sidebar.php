<?php
/**
 * Sidebar — aside region with optional title and content slot.
 *
 * Expected data: title, content (slot HTML).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<aside class="phantom-sidebar">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="phantom-sidebar__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<div class="phantom-sidebar__content">
		<?php echo $view->prop( 'content' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?>
	</div>
</aside>
