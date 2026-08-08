<?php
/**
 * Article body — sanitized article body wrapper.
 *
 * Key:    'content/article-body'
 * Source: engine-native (the_content)
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $content  Sanitized article HTML. Default ''.`
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

$content = isset( $componentData['content'] ) ? $componentData['content'] : '';

if ( ! $content ) {
	return;
}
?>
<div class="article-body" data-phantom="article_body">
	<?php echo wp_kses_post( $content ); ?>
</div>