<?php
/**
 * Content page — page body rendered from sanitized content.
 *
 * Key:    'content/page'
 * Source: engine-native (the_content)
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $content  Sanitized page HTML. Default ''.`
 * - `string $style    Content style class. Default ''.`
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
$style   = isset( $componentData['style'] ) ? $componentData['style'] : '';

if ( ! $content ) {
	return;
}
?>
<section class="content-section section<?php echo $style ? ' content-section--' . esc_attr( sanitize_key( $style ) ) : ''; ?>">
	<div class="container">
		<div class="legal-content">
			<?php echo wp_kses_post( $content ); ?>
		</div>
	</div>
</section>