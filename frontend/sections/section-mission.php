<?php
/**
 * Mission section — about page mission grid (image + text).
 *
 * Source: about.html .mission-section
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'mission', array(
	'template' => 'sections/section-mission.php',
	'adapter'  => 'adapter-about.php',
	'behavior' => array( 'reveal' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$mission  = isset( $sectionData['mission'] ) ? (array) $sectionData['mission'] : array();
$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();

if ( empty( $mission ) ) {
	return;
}
?>
<section class="mission-section section" id="mission" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="container">
		<div class="mission-grid" data-reveal-group>
			<?php if ( ! empty( $mission['image'] ) ) : ?>
				<div class="mission-image" data-reveal-item>
					<img loading="lazy" src="<?php echo esc_url( aether_viewmodel_resolve_image( $mission['image'] ) ); ?>" alt="<?php echo esc_attr( isset( $mission['alt'] ) ? $mission['alt'] : '' ); ?>" data-phantom-alt="mission_image">
				</div>
			<?php endif; ?>
			<div class="mission-content" data-reveal-item>
				<?php if ( ! empty( $mission['label'] ) ) : ?>
					<span class="section-label" data-phantom="section_label"><?php echo esc_html( $mission['label'] ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $mission['title'] ) ) : ?>
					<h2 class="section-title" data-phantom="section_title"><?php echo esc_html( $mission['title'] ); ?></h2>
				<?php endif; ?>
				<?php foreach ( (array) $mission['text'] as $paragraph ) : ?>
					<?php if ( $paragraph ) : ?>
						<p><?php echo esc_html( $paragraph ); ?></p>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>