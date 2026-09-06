<?php
/**
 * Ferm Living site footer — USP row, newsletter, 3 link columns, legal.
 *
 * Key:    'shell/footer' (override)
 * Source: fermliving.com footer structure
 * Props:  brand, brand_url, columns, newsletter, legal, payments, usp_items.
 * Contract: keeps footer#footer, .footer-newsletter-form (#footerNewsletterForm),
 *           .footer-legal, .footer-payments — platform newsletter JS operates unchanged.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$brand      = isset( $componentData['brand'] ) ? $componentData['brand'] : '';
$brand_url  = isset( $componentData['brand_url'] ) ? $componentData['brand_url'] : '';
$columns    = isset( $componentData['columns'] ) ? (array) $componentData['columns'] : array();
$newsletter = isset( $componentData['newsletter'] ) ? (array) $componentData['newsletter'] : array();
$legal      = aureon_get_option( 'aether_footer_legal', isset( $componentData['legal'] ) ? (array) $componentData['legal'] : array() );
$payments   = aureon_get_option( 'aether_footer_payments', isset( $componentData['payments'] ) ? (array) $componentData['payments'] : array() );
$usp_items  = isset( $componentData['usp_items'] ) ? (array) $componentData['usp_items'] : array();
$socials    = aureon_get_option( 'aether_social_items', isset( $componentData['socials'] ) ? (array) $componentData['socials'] : array() );

$newsletter_heading = aureon_get_option( 'aether_newsletter_heading', 'Stay Updated' );
$newsletter_text    = aureon_get_option( 'aether_newsletter_text', 'Sign up for news, offers and inspiration. No spam, ever.' );
$newsletter_url     = isset( $newsletter['url'] ) ? $newsletter['url'] : '#';
?>
<footer class="footer" id="footer" role="contentinfo" aria-label="Site footer">

	<?php /* USP Row */ ?>
	<?php if ( ! empty( $usp_items ) ) : ?>
	<div class="footer-usps">
		<div class="footer-usps-grid">
			<?php foreach ( $usp_items as $item ) :
				$usp_title = isset( $item['title'] ) ? $item['title'] : '';
				$usp_text  = isset( $item['text'] ) ? $item['text'] : '';
				$usp_url   = isset( $item['url'] ) ? $item['url'] : '';
				?>
				<div class="footer-usp">
					<?php if ( $usp_url ) : ?>
						<a href="<?php echo esc_url( $usp_url ); ?>" class="footer-usp-link">
					<?php endif; ?>
						<div class="footer-usp-title"><?php echo esc_html( $usp_title ); ?></div>
						<div class="footer-usp-text"><?php echo esc_html( $usp_text ); ?></div>
					<?php if ( $usp_url ) : ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php endif; ?>

	<?php /* Main Footer: Newsletter + Link Columns */ ?>
	<div class="footer-main">
		<div class="footer-columns">
			<?php /* Newsletter Column */ ?>
			<div class="footer-column footer-newsletter-column">
				<div class="footer-newsletter">
					<div class="footer-newsletter-form" aria-label="Newsletter signup">
						<?php if ( $newsletter_heading ) : ?>
							<h4 class="footer-newsletter-heading"><?php echo esc_html( $newsletter_heading ); ?></h4>
						<?php endif; ?>
						<?php if ( $newsletter_text ) : ?>
							<p class="footer-newsletter-text"><?php echo esc_html( $newsletter_text ); ?></p>
						<?php endif; ?>
						<form class="footer-newsletter-form-inner" aria-label="Newsletter subscription" id="footerNewsletterForm" method="post">
							<input type="email"
								   name="email"
								   placeholder="Your email"
								   required
								   aria-label="Email address">
							<button type="submit" aria-label="Subscribe" class="btn btn-sm">
								<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>
								</svg>
							</button>
						</form>
					</div>
				</div>
			</div>

			<?php /* Link Columns */ ?>
			<?php foreach ( $columns as $column ) :
				$heading = isset( $column['heading'] ) ? $column['heading'] : '';
				$links   = isset( $column['links'] ) ? (array) $column['links'] : array();
				if ( empty( $heading ) && empty( $links ) ) {
					continue;
				}
				?>
				<div class="footer-column">
					<h4><?php echo esc_html( $heading ); ?></h4>
					<?php if ( ! empty( $links ) ) : ?>
						<ul>
							<?php foreach ( $links as $link ) :
								$link_label = isset( $link['label'] ) ? $link['label'] : '';
								$link_url   = isset( $link['url'] ) ? $link['url'] : '#';
								if ( empty( $link_label ) ) {
									continue;
								}
								?>
								<li>
									<a href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $link_label ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<?php /* Bottom Bar: Legal + Company Info + Social + Payments */ ?>
	<div class="footer-bottom">
		<div class="footer-bottom-inner">
			<div class="footer-legal">
				<?php if ( ! empty( $legal ) ) : ?>
					<?php foreach ( $legal as $link ) :
						$link_label = isset( $link['label'] ) ? $link['label'] : '';
						$link_url   = isset( $link['url'] ) ? $link['url'] : '#';
						if ( empty( $link_label ) ) {
							continue;
						}
						?>
						<a href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $link_label ); ?></a>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<div class="footer-company">
				<address><?php echo esc_html( 'Ferm Living ApS CVR No. 30070186' ); ?></address>
			</div>

			<?php if ( ! empty( $socials ) ) : ?>
			<div class="footer-socials">
				<?php foreach ( $socials as $social ) :
					$social_url   = isset( $social['url'] ) ? $social['url'] : '#';
					$social_label = isset( $social['label'] ) ? $social['label'] : '';
					$social_icon  = isset( $social['icon'] ) ? $social['icon'] : '';
					if ( empty( $social_url ) || '#' === $social_url ) {
						continue;
					}
					?>
					<a href="<?php echo esc_url( $social_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $social_label ); ?>">
						<i class="<?php echo esc_attr( $social_icon ); ?>" aria-hidden="true"></i>
					</a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

		<?php if ( ! empty( $payments ) ) : ?>
			<div class="footer-payments">
				<?php foreach ( $payments as $icon ) : ?>
					<i class="fa <?php echo esc_attr( $icon ); ?>" aria-hidden="true" title="Payment method"></i>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		</div>
	</div>
</footer>
