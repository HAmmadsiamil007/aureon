<?php
/**
 * StarRating — accessible rating display (value out of max, optional count).
 *
 * Expected data: value, count, max.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<?php
$value = (float) $view->prop( 'value', 0.0 );
$max   = max( 1, (int) $view->prop( 'max', 5 ) );
$full  = (int) round( $value );
?>
<div class="phantom-star-rating" role="img" aria-label="<?php echo $view->attr( sprintf( 'Rated %s out of %d', number_format( $value, 1 ), $max ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-phantom-star-rating>
	<span class="phantom-star-rating__stars" aria-hidden="true">
		<?php for ( $i = 1; $i <= $max; $i++ ) : ?>
			<span class="phantom-star-rating__star<?php echo $i <= $full ? ' is-filled' : ''; ?>">&#9733;</span>
		<?php endfor; ?>
	</span>
	<?php if ( (int) $view->prop( 'count', 0 ) > 0 ) : ?>
		<span class="phantom-star-rating__count">(<?php echo $view->e( (string) $view->prop( 'count' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>)</span>
	<?php endif; ?>
</div>
