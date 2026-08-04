<?php
/**
 * Pagination — accessible numbered pagination.
 *
 * Expected data: current, total, base_url, adjacent.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );

$current = max( 1, (int) $view->prop( 'current', 1 ) );
$total   = max( 1, (int) $view->prop( 'total', 1 ) );
?>
<?php if ( $total > 1 ) : ?>
	<nav class="lumina-pagination" aria-label="Pagination">
		<ul class="lumina-pagination__list">
			<?php for ( $page = 1; $page <= $total; $page++ ) : ?>
				<li class="lumina-pagination__item">
					<?php if ( $page === $current ) : ?>
						<span class="lumina-pagination__link is-current" aria-current="page">
							<?php echo (int) $page; ?>
						</span>
					<?php else : ?>
						<?php
						// Per-page URLs win when supplied (data adapter contract);
						// otherwise fall back to the shared base URL.
						$page_urls = $view->prop( 'page_urls', array() );
						$link_url  = is_array( $page_urls ) && isset( $page_urls[ $page ] ) && is_string( $page_urls[ $page ] )
							? $page_urls[ $page ]
							: $view->prop( 'page_url', $view->prop( 'base_url', '#' ) );
						?>
						<a class="lumina-pagination__link" href="<?php echo $view->url( $link_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
							<?php echo (int) $page; ?>
						</a>
					<?php endif; ?>
				</li>
			<?php endfor; ?>
		</ul>
	</nav>
<?php endif; ?>
