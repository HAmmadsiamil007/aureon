<?php
/**
 * Loading skeleton — shimmer placeholder for async content.
 *
 * Expected data: rows, label.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );

$rows = max( 1, (int) $view->prop( 'rows', 3 ) );
?>
<div class="lumina-skeleton" aria-hidden="true" data-lumina-skeleton>
	<span class="screen-reader-text"><?php echo $view->e( $view->prop( 'label', 'Loading' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
	<?php for ( $row = 0; $row < $rows; $row++ ) : ?>
		<span class="lumina-skeleton__line"></span>
	<?php endfor; ?>
</div>
