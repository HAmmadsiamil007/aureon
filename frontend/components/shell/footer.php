<?php
/**
 * Site footer — brand, link columns, newsletter, legal and payment badges.
 *
 * Key:    'shell/footer'
 * Source: engine-native (global chrome — all 21 source pages)
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $brand       Wordmark. Default ''.`
 * - `string $brand_url   Home link. Default ''.`
 * - `string $tagline     Strapline. Default ''.`
 * - `array $socials      Social link schema. Default [].`
 * - `array $columns      Footer link column schema (title/links). Default [].`
 * - `array $newsletter   Newsletter section schema (title/subtitle). Default [].`
 * - `string $copyright   Copyright line. Default ''.`
 * - `array $legal        Legal link schema. Default [].`
 * - `array $payments     Payment badge schema. Default [].`
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

$brand     = isset( $componentData['brand'] ) ? $componentData['brand'] : '';
$brand_url = isset( $componentData['brand_url'] ) ? $componentData['brand_url'] : '';
$tagline   = isset( $componentData['tagline'] ) ? $componentData['tagline'] : '';
$socials   = isset( $componentData['socials'] ) ? (array) $componentData['socials'] : array();
$columns   = isset( $componentData['columns'] ) ? (array) $componentData['columns'] : array();
$newsletter = isset( $componentData['newsletter'] ) ? (array) $componentData['newsletter'] : array();
$copyright = isset( $componentData['copyright'] ) ? $componentData['copyright'] : '';
$legal     = isset( $componentData['legal'] ) ? (array) $componentData['legal'] : array();
$payments  = isset( $componentData['payments'] ) ? (array) $componentData['payments'] : array();
?>
<footer class="footer" id="footer" role="contentinfo" aria-label="Site footer" data-phantom-menu="footer">
	<div class="container">
		<div class="footer-top">
			<div class="footer-brand">
				<a href="<?php echo esc_url( $brand_url ); ?>" class="footer-logo"><?php echo esc_html( $brand ); ?></a>
				<p class="footer-tagline"><?php echo esc_html( $tagline ); ?></p>
				<div class="footer-social" role="group" aria-label="Social media links">
					<?php foreach ( $socials as $social ) : ?>
						<a href="<?php echo esc_url( isset( $social['url'] ) ? $social['url'] : '#' ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( isset( $social['label'] ) ? $social['label'] : '' ); ?>"><i class="<?php echo esc_attr( isset( $social['icon'] ) ? $social['icon'] : '' ); ?>"></i></a>
					<?php endforeach; ?>
				</div>
			</div>

			<?php foreach ( $columns as $column ) : ?>
				<div class="footer-links">
					<h4 class="footer-heading"><?php echo esc_html( isset( $column['heading'] ) ? $column['heading'] : '' ); ?></h4>
					<ul>
						<?php
						$links = isset( $column['links'] ) ? (array) $column['links'] : array();
						foreach ( $links as $link ) :
							?>
							<li><a href="<?php echo esc_url( isset( $link['url'] ) ? $link['url'] : '#' ); ?>"><?php echo esc_html( isset( $link['label'] ) ? $link['label'] : '' ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>

			<div class="footer-newsletter">
				<h4 class="footer-heading"><?php echo esc_html( isset( $newsletter['heading'] ) ? $newsletter['heading'] : 'Stay in the Loop' ); ?></h4>
				<p><?php echo esc_html( isset( $newsletter['text'] ) ? $newsletter['text'] : '' ); ?></p>
				<form class="footer-newsletter-form" aria-label="Newsletter subscription" id="footerNewsletterForm">
					<input type="email" placeholder="Your email" required aria-label="Email address">
					<button type="submit" aria-label="Subscribe"><i class="fas fa-arrow-right"></i></button>
				</form>
			</div>
		</div>

		<div class="footer-bottom">
			<div class="footer-legal">
				<span><?php echo wp_kses_post( $copyright ); ?></span>
				<?php foreach ( $legal as $link ) : ?>
					<a href="<?php echo esc_url( isset( $link['url'] ) ? $link['url'] : '#' ); ?>"><?php echo esc_html( isset( $link['label'] ) ? $link['label'] : '' ); ?></a>
				<?php endforeach; ?>
			</div>
			<div class="footer-payments">
				<?php foreach ( $payments as $icon ) : ?>
					<i class="<?php echo esc_attr( $icon ); ?>"></i>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</footer>
