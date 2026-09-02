<?php
/**
 * Filter bar — category filter button row for shop grids.
 *
 * Key:    'section/filter-bar'
 * Source: shop.html `.filter-bar`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `array $buttons  Filter button schema (label/url/active). Default [].`
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

$buttons = isset( $componentData['buttons'] ) ? (array) $componentData['buttons'] : array();

if ( empty( $buttons ) ) {
	return;
}
?>
<section class="filter-bar">
	<div class="container">
		<div class="filter-buttons">
			<?php foreach ( $buttons as $button ) : ?>
				<?php
				$label  = isset( $button['label'] ) ? $button['label'] : '';
				$url    = isset( $button['url'] ) ? $button['url'] : '#';
				$active = ! empty( $button['active'] );
				?>
				<a href="<?php echo esc_url( $url ); ?>" class="filter-btn<?php echo $active ? ' active' : ''; ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
