<?php
/**
 * Story section — single full-width story/quote band.
 *
 * Key:    'content/story'
 * Source: about.html `.story`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $quote     Story text. Default ''.`
 * - `array $behavior  Behavior whitelist. Default [].`
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

$quote    = isset( $componentData['quote'] ) ? $componentData['quote'] : '';
$behavior = isset( $componentData['behavior'] ) ? (array) $componentData['behavior'] : array();

if ( ! $quote ) {
	return;
}
?>
<section class="story-section" data-parallax-section <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="story-overlay">
		<div class="container">
			<blockquote class="story-quote" data-phantom="story_quote"><?php echo esc_html( $quote ); ?></blockquote>
		</div>
	</div>
</section>