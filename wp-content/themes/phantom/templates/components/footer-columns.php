<?php
/**
 * Footer columns — widget-style column layout.
 *
 * Expected data: columns (title, links list of label+url).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-footer-columns">
	<?php foreach ( (array) $view->prop( 'columns', array() ) as $column ) : ?>
		<div class="phantom-footer-columns__column">
			<?php if ( ! empty( $column['title'] ) ) : ?>
				<h3 class="phantom-footer-columns__title"><?php echo $view->e( $column['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h3>
			<?php endif; ?>
			<ul class="phantom-footer-columns__list">
				<?php foreach ( (array) ( $column['links'] ?? array() ) as $link ) : ?>
					<li class="phantom-footer-columns__item">
						<a href="<?php echo $view->url( $link['url'] ?? '#' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( $link['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</div>
