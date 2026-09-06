<?php
/**
 * Features section — about page technology grid (4 feature cards).
 *
 * Source: about.html .features
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'features', array(
	'template' => 'sections/section-features.php',
	'adapter'  => 'adapter-about.php',
	'behavior' => array( 'reveal-group' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$features = isset( $sectionData['features'] ) ? (array) $sectionData['features'] : array();
$items    = isset( $features['items'] ) ? (array) $features['items'] : array();
$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();

if ( empty( $items ) ) {
	return;
}
?>
<section class="features section" id="features" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="container">
		<?php
		aether_render_component( 'section/header', array(
			'label'    => isset( $features['label'] ) ? $features['label'] : __( 'Innovation', 'aureon' ),
			'title'    => isset( $features['title'] ) ? $features['title'] : __( 'The Technology Inside', 'aureon' ),
			'subtitle' => isset( $features['subtitle'] ) ? $features['subtitle'] : '',
			'behavior' => $behavior,
		) );
		?>

		<div class="features-grid" data-reveal-group>
			<?php foreach ( $items as $feature ) : ?>
				<div class="feature-card" data-reveal-item>
					<div class="feature-icon">
						<i class="fas <?php echo esc_attr( isset( $feature['icon'] ) ? $feature['icon'] : 'fa-circle' ); ?>"></i>
					</div>
					<h3 class="feature-title"><?php echo esc_html( isset( $feature['title'] ) ? $feature['title'] : '' ); ?></h3>
					<p class="feature-description"><?php echo esc_html( isset( $feature['description'] ) ? $feature['description'] : '' ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>