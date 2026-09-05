<?php
/**
 * Team section — member cards.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'team', array(
	'template' => 'sections/section-team.php',
	'adapter'  => 'adapter-team.php',
	'behavior' => array( 'reveal-group' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$items    = isset( $sectionData['items'] ) ? (array) $sectionData['items'] : array();
$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();

if ( empty( $items ) ) {
	return;
}
?>
<section class="team-section section" id="team" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="container">
		<?php
		aether_render_component( 'section/header', array(
			'label'    => isset( $sectionData['label'] ) ? $sectionData['label'] : __( 'The Collective', 'aureon' ),
			'title'    => isset( $sectionData['title'] ) ? $sectionData['title'] : __( 'Meet the Team', 'aureon' ),
			'subtitle' => isset( $sectionData['subtitle'] ) ? $sectionData['subtitle'] : '',
			'behavior' => $behavior,
		) );
		?>

		<div class="team-grid" data-reveal-group>
			<?php foreach ( $items as $member ) : ?>
				<?php aether_render_component( 'card/team', $member ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
