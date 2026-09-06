<?php
/**
 * Values section — team page "Our Values" feature-card grid.
 *
 * Source: team.html .values-section (same features-grid component pattern
 * as section-features, distinct label/title/i18n).
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'values', array(
	'template' => 'sections/section-values.php',
	'adapter'  => 'adapter-about.php',
	'behavior' => array( 'reveal-group' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$values    = isset( $sectionData['values'] ) ? (array) $sectionData['values'] : array();
$items     = isset( $values['items'] ) ? (array) $values['items'] : array();
$behavior  = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();

if ( empty( $items ) ) {
	return;
}
?>
<section class="values-section" data-parallax-section <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="container">
		<?php
		aether_render_component( 'section/header', array(
			'label'    => isset( $values['label'] ) ? $values['label'] : __( 'Our Values', 'aureon' ),
			'title'    => isset( $values['title'] ) ? $values['title'] : __( 'What Drives Us', 'aureon' ),
			'subtitle' => isset( $values['subtitle'] ) ? $values['subtitle'] : '',
			'behavior' => $behavior,
		) );
		?>

		<div class="features-grid" data-reveal-group>
			<?php foreach ( $items as $value ) : ?>
				<div class="feature-card" data-reveal-item>
					<div class="feature-icon">
						<i class="fas <?php echo esc_attr( isset( $value['icon'] ) ? $value['icon'] : 'fa-circle' ); ?>"></i>
					</div>
					<h3 class="feature-title"><?php echo esc_html( isset( $value['title'] ) ? $value['title'] : '' ); ?></h3>
					<p class="feature-description"><?php echo esc_html( isset( $value['description'] ) ? $value['description'] : '' ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>