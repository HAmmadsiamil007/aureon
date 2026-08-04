<?php
/**
 * Breadcrumb — semantic breadcrumb trail (nav aria-label + JSON-LD ready
 * structure via <ol>).
 *
 * Expected data: items (list of ['label' => string, 'url' => string]).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<?php
$items = (array) $view->prop( 'items' );
?>
<?php if ( array() !== $items ) : ?>
	<nav class="lumina-breadcrumb" aria-label="<?php echo $view->attr( 'Breadcrumb' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
		<ol class="lumina-breadcrumb__list">
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$item  = is_array( $item ) ? $item : array();
				$label = isset( $item['label'] ) ? (string) $item['label'] : '';
				$url   = isset( $item['url'] ) ? (string) $item['url'] : '';
				$last  = $index === count( $items ) - 1;
				?>
				<?php if ( '' !== $label ) : ?>
					<li class="lumina-breadcrumb__item<?php echo $last ? ' is-current' : ''; ?>">
						<?php if ( ! $last && '' !== $url ) : ?>
							<a class="lumina-breadcrumb__link" href="<?php echo $view->url( $url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
						<?php else : ?>
							<span class="lumina-breadcrumb__current" aria-current="page"><?php echo $view->e( $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
						<?php endif; ?>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ol>
	</nav>
<?php endif; ?>
