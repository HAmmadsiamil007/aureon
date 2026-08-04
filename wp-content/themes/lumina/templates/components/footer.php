<?php
/**
 * Footer — site footer composed from footer-columns and copyright slots.
 *
 * Expected data: columns (footer-columns props), copyright (copyright props).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<footer class="lumina-footer">
	<div class="lumina-footer__inner">
		<?php if ( $view->prop( 'columns' ) ) : ?>
			<div class="lumina-footer__columns"><?php echo $view->prop( 'columns' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?></div>
		<?php endif; ?>
		<?php if ( $view->prop( 'copyright' ) ) : ?>
			<div class="lumina-footer__bottom"><?php echo $view->prop( 'copyright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?></div>
		<?php endif; ?>
	</div>
</footer>
