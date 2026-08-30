<?php
/**
 * Size guide — size-guide modal table.
 *
 * Key:    'product/size-guide'
 * Source: product-detail.html `.size-guide`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $title     Modal title. Default 'Size Guide'.`
 * - `string $subtitle  Subtitle. Default ''.`
 * - `array $rows     Row schema (cols). Default [].`
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

$title    = isset( $componentData['title'] ) ? $componentData['title'] : __( 'Size Guide', 'aureon' );
$subtitle = isset( $componentData['subtitle'] ) ? $componentData['subtitle'] : '';
$rows     = isset( $componentData['rows'] ) ? (array) $componentData['rows'] : array();

if ( empty( $rows ) ) {
	return;
}
?>
<div class="pd-modal-overlay" id="sizeGuideModal">
	<div class="pd-modal">
		<button class="pd-modal-close" id="closeSizeGuide" aria-label="Close size guide"><i class="fas fa-times"></i></button>
		<h3 class="pd-modal-title"><?php echo esc_html( $title ); ?></h3>
		<?php if ( $subtitle ) : ?>
			<p class="pd-modal-subtitle"><?php echo esc_html( $subtitle ); ?></p>
		<?php endif; ?>
		<div class="pd-size-table-wrap">
			<table class="pd-size-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'US', 'aureon' ); ?></th>
						<th><?php esc_html_e( 'EU', 'aureon' ); ?></th>
						<th><?php esc_html_e( 'UK', 'aureon' ); ?></th>
						<th><?php esc_html_e( 'CM', 'aureon' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rows as $row ) : ?>
						<tr>
							<td><?php echo esc_html( $row['us'] ); ?></td>
							<td><?php echo esc_html( $row['eu'] ); ?></td>
							<td><?php echo esc_html( $row['uk'] ); ?></td>
							<td><?php echo esc_html( $row['cm'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="pd-modal-tip">
			<i class="fas fa-ruler-horizontal"></i>
			<p><?php esc_html_e( 'Measure your foot from heel to longest toe. If between sizes, we recommend going half size up.', 'aureon' ); ?></p>
		</div>
	</div>
</div>
