<?php
/**
 * Page banner — subpage hero band with breadcrumb trail.
 *
 * Key:    'hero/page-banner'
 * Source: all subpages (e.g. cart.html) `.page-banner`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $title    Banner title. Default ''.`
 * - `array $crumbs   Breadcrumb schema (label/url). Default [].`
 * - `array $behavior  Behavior whitelist. Default [].`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$title   = isset( $componentData['title'] ) ? $componentData['title'] : '';
$crumbs  = isset( $componentData['crumbs'] ) ? (array) $componentData['crumbs'] : array();
$behavior = isset( $componentData['behavior'] ) ? (array) $componentData['behavior'] : array();
?>
<section class="page-hero" data-parallax-section data-phantom-bg="hero" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="hero-fog" aria-hidden="true">
		<div id="hl_01" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
		<div id="hl_02" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
		<div id="hl_03" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
	</div>
	<div class="container">
		<?php if ( $title ) : ?>
			<h1 data-motion-text="words" data-phantom="page_title"><?php echo esc_html( $title ); ?></h1>
		<?php endif; ?>
		<?php if ( ! empty( $crumbs ) ) : ?>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<?php foreach ( $crumbs as $crumb ) : ?>
						<?php
						$label = isset( $crumb['label'] ) ? $crumb['label'] : '';
						$url   = isset( $crumb['url'] ) ? $crumb['url'] : '';
						?>
						<?php if ( $url ) : ?>
							<li class="breadcrumb-item"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
						<?php else : ?>
							<li class="breadcrumb-item active" aria-current="page"><?php echo esc_html( $label ); ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ol>
			</nav>
		<?php endif; ?>
	</div>
</section>
