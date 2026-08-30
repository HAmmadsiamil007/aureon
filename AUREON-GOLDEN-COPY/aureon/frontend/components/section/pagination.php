<?php
/**
 * Pagination — page-number pager.
 *
 * Key:    'section/pagination'
 * Source: shop.html `.pagination`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `int $current  Active page (min 1). Default 1.`
 * - `int $total    Page count. Default 1.`
 * - `string $base    Page URL pattern. Default ''.`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$current = isset( $componentData['current'] ) ? max( 1, (int) $componentData['current'] ) : 1;
$total   = isset( $componentData['total'] ) ? (int) $componentData['total'] : 1;
$base    = isset( $componentData['base'] ) ? $componentData['base'] : '';

if ( $total <= 1 ) {
	return;
}

/**
 * Build a page URL from the base (query-string aware).
 */
$aether_page_url = function ( $page ) use ( $base ) {
	if ( false !== strpos( $base, '?' ) ) {
		return add_query_arg( 'paged', $page, $base );
	}
	return esc_url( trailingslashit( $base ) . $page . '/' );
};

// Numbered window: 1 … c-1 c c+1 … total (dots for gaps).
$pages = array();
if ( $total <= 7 ) {
	$pages = range( 1, $total );
} else {
	$pages = array( 1 );
	if ( $current > 3 ) {
		$pages[] = '…';
	}
	for ( $i = max( 2, $current - 1 ); $i <= min( $total - 1, $current + 1 ); $i++ ) {
		$pages[] = $i;
	}
	if ( $current < $total - 2 ) {
		$pages[] = '…';
	}
	$pages[] = $total;
}
?>
<div class="shop-pagination">
	<?php if ( $current > 1 ) : ?>
		<a href="<?php echo $aether_page_url( $current - 1 ); ?>" class="pagination-btn pagination-prev" aria-label="Previous page"><i class="fas fa-chevron-left"></i></a>
	<?php else : ?>
		<button class="pagination-btn pagination-prev" aria-label="Previous page" disabled><i class="fas fa-chevron-left"></i></button>
	<?php endif; ?>

	<div class="pagination-pages">
		<?php foreach ( $pages as $page ) : ?>
			<?php if ( is_int( $page ) ) : ?>
				<a href="<?php echo $aether_page_url( $page ); ?>" class="pagination-page<?php echo $page === $current ? ' active' : ''; ?>"<?php echo $page === $current ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $page ); ?></a>
			<?php else : ?>
				<span class="pagination-page pagination-gap"><?php echo esc_html( $page ); ?></span>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>

	<?php if ( $current < $total ) : ?>
		<a href="<?php echo $aether_page_url( $current + 1 ); ?>" class="pagination-btn pagination-next" aria-label="Next page"><i class="fas fa-chevron-right"></i></a>
	<?php else : ?>
		<button class="pagination-btn pagination-next" aria-label="Next page" disabled><i class="fas fa-chevron-right"></i></button>
	<?php endif; ?>
</div>
