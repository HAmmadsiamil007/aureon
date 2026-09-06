<?php
/**
 * Product breadcrumb — breadcrumb trail under product header.
 *
 * Key:    'product/breadcrumb'
 * Source: product-detail.html `.pd-crumbs`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `array $crumbs  Crumb schema (label/url). Default [].`
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

$crumbs = isset( $componentData['crumbs'] ) ? (array) $componentData['crumbs'] : array();

if ( empty( $crumbs ) ) {
	return;
}
?>
<div class="pd-breadcrumb">
	<div class="container">
		<nav aria-label="Breadcrumb">
			<?php foreach ( $crumbs as $i => $crumb ) : ?>
				<?php if ( 0 !== $i ) : ?>
					<span class="pd-breadcrumb-sep">/</span>
				<?php endif; ?>
				<?php
				$label = isset( $crumb['label'] ) ? $crumb['label'] : '';
				$url   = isset( $crumb['url'] ) ? $crumb['url'] : '';
				?>
				<?php if ( $url ) : ?>
					<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php else : ?>
					<span class="pd-breadcrumb-current"><?php echo esc_html( $label ); ?></span>
				<?php endif; ?>
			<?php endforeach; ?>
		</nav>
	</div>
</div>
