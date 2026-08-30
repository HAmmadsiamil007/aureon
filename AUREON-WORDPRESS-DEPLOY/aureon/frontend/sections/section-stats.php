<?php
/**
 * Stats section — about page stats grid with count-up numbers.
 *
 * Source: about.html .stats-section
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'stats', array(
	'template' => 'sections/section-stats.php',
	'adapter'  => 'adapter-about.php',
	'behavior' => array( 'reveal-group' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$stats    = isset( $sectionData['stats'] ) ? (array) $sectionData['stats'] : array();
// Canonical stats is a list of {number,label}; legacy wrapper {items:[...]} tolerated.
$items    = isset( $stats['items'] ) && is_array( $stats['items'] ) ? (array) $stats['items'] : $stats;
$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();

if ( empty( $items ) ) {
	return;
}
?>
<section class="stats-section section" id="stats" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="container">
		<div class="stats-grid" data-reveal-group>
			<?php foreach ( $items as $stat ) : ?>
				<div class="stat-item" data-reveal-item>
					<span class="stat-number" data-countup="<?php echo esc_attr( preg_replace( '/[^0-9]/', '', isset( $stat['number'] ) ? $stat['number'] : '' ) ); ?>"><?php echo esc_html( isset( $stat['number'] ) ? $stat['number'] : '' ); ?></span>
					<span class="stat-label"><?php echo esc_html( isset( $stat['label'] ) ? $stat['label'] : '' ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>