<?php
/**
 * Shop filter bar — category/Sale buttons.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'shop-filter', array(
	'template' => 'sections/section-shop-filter.php',
	'adapter'  => 'adapter-wc-filter.php',
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$buttons = isset( $sectionData['buttons'] ) ? (array) $sectionData['buttons'] : array();

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
