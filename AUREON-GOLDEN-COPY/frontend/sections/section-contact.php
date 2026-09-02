<?php
/**
 * Contact section — form (col-lg-7) + info cards (col-lg-5) + map placeholder.
 *
 * Source: contact.html .contact-section
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'contact', array(
	'template' => 'sections/section-contact.php',
	'adapter'  => 'adapter-contact.php',
	'behavior' => array( 'reveal-group' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$fields    = isset( $sectionData['fields'] ) ? (array) $sectionData['fields'] : array();
$action    = isset( $sectionData['action'] ) ? $sectionData['action'] : '';
$nonce     = isset( $sectionData['nonce'] ) ? $sectionData['nonce'] : '';
$info      = isset( $sectionData['info'] ) ? (array) $sectionData['info'] : array();
$socials   = isset( $sectionData['socials'] ) ? (array) $sectionData['socials'] : array();
$behavior  = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();
?>
<section class="contact-section section" id="contact" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="container">
		<div class="row">
			<div class="col-lg-7">
				<div class="contact-form-wrap">
					<h2 data-motion-text="words"><?php echo esc_html( isset( $sectionData['form_title'] ) ? $sectionData['form_title'] : __( 'Send Us a Message', 'aureon' ) ); ?></h2>
					<?php aether_render_component( 'form/contact', array(
						'fields'   => $fields,
						'action'   => $action,
						'nonce'    => $nonce,
						'behavior' => $behavior,
					) ); ?>
				</div>
			</div>
			<div class="col-lg-5">
				<div class="contact-info-cards">
					<?php foreach ( $info as $card ) : ?>
						<div class="info-card" data-tilt>
							<div class="info-card-icon"><i class="fas <?php echo esc_attr( $card['icon'] ); ?>"></i></div>
							<h3><?php echo esc_html( $card['title'] ); ?></h3>
							<p>
								<?php foreach ( $card['lines'] as $i => $line ) : ?>
									<?php if ( $i > 0 ) { echo '<br>'; } ?>
									<?php if ( ! empty( $card['href'] ) && 0 === $i ) : ?>
										<a href="<?php echo esc_url( $card['href'] ); ?>"><?php echo esc_html( $line ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $line ); ?>
									<?php endif; ?>
								<?php endforeach; ?>
							</p>
						</div>
					<?php endforeach; ?>
					<div class="info-card" data-tilt>
						<div class="info-card-icon"><i class="fas fa-share-nodes"></i></div>
						<h3><?php esc_html_e( 'Follow Us', 'aureon' ); ?></h3>
						<div class="social-links">
							<?php foreach ( $socials as $social ) : ?>
								<?php if ( ! empty( $social['url'] ) ) : ?>
									<a href="<?php echo esc_url( $social['url'] ); ?>" class="social-link" aria-label="<?php echo esc_attr( $social['label'] ); ?>"><i class="fab <?php echo esc_attr( $social['icon'] ); ?>"></i></a>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="map-placeholder">
			<i class="fas fa-map-location-dot"></i>
			<span><?php esc_html_e( 'Map Coming Soon', 'aureon' ); ?></span>
		</div>
	</div>
</section>