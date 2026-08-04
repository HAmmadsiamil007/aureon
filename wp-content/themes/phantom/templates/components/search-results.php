<?php
/**
 * Search results — result list with query echo.
 *
 * Expected data: query, count, results (title, url, excerpt).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-search-results">
	<header class="phantom-search-results__head">
		<h1 class="phantom-search-results__title">
			<?php
			echo $view->e( $view->prop( 'heading', 'Search results' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e().
			?>
			<?php if ( $view->prop( 'query' ) ) : ?>
				<span class="phantom-search-results__query">“<?php echo $view->e( $view->prop( 'query' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>”</span>
			<?php endif; ?>
		</h1>
		<p class="phantom-search-results__count"><?php echo (int) $view->prop( 'count', 0 ); ?> <?php echo $view->e( $view->prop( 'count_label', 'results' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	</header>
	<ul class="phantom-search-results__list">
		<?php foreach ( (array) $view->prop( 'results', array() ) as $result ) : ?>
			<li class="phantom-search-results__item">
				<h2 class="phantom-search-results__item-title">
					<a href="<?php echo $view->url( $result['url'] ?? '#' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( $result['title'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
				</h2>
				<?php if ( ! empty( $result['excerpt'] ) ) : ?>
					<p class="phantom-search-results__excerpt"><?php echo $view->e( $result['excerpt'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
