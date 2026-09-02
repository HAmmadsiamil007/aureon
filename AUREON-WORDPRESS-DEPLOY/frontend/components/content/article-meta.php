<?php
/**
 * Article meta — author/date/read metadata line.
 *
 * Key:    'content/article-meta'
 * Source: single-blog.html `.article-meta`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $author     Author display. Default ''.`
 * - `string $date       Date label. Default ''.`
 * - `string $read_time  Reading time. Default ''.`
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

$author     = isset( $componentData['author'] ) ? $componentData['author'] : '';
$date       = isset( $componentData['date'] ) ? $componentData['date'] : '';
$read_time  = isset( $componentData['read_time'] ) ? $componentData['read_time'] : '';

if ( ! $author && ! $date && ! $read_time ) {
	return;
}
?>
<div class="article-meta" data-phantom="article_meta">
	<?php if ( $author ) : ?>
		<span class="article-author" data-phantom="article_author"><?php echo esc_html( $author ); ?></span>
		<span class="article-separator">&mdash;</span>
	<?php endif; ?>
	<?php if ( $date ) : ?>
		<span class="article-date" data-phantom="article_date"><?php echo esc_html( $date ); ?></span>
		<?php if ( $read_time ) : ?>
			<span class="article-separator">&mdash;</span>
		<?php endif; ?>
	<?php endif; ?>
	<?php if ( $read_time ) : ?>
		<span class="article-read-time" data-phantom="article_read_time"><?php echo esc_html( $read_time ); ?> <?php esc_html_e( 'min read', 'aureon' ); ?></span>
	<?php endif; ?>
</div>