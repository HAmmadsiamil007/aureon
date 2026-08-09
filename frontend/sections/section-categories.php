<?php
/**
 * Categories section — WC product category grid.
 *
 * Production-ready with:
 * - Schema.org ItemList markup for SEO
 * - Proper heading hierarchy
 * - Accessible links with descriptive text
 * - Customizer-driven title/subtitle
 * - Real WooCommerce categories with product images
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'categories', array(
	'template' => 'sections/section-categories.php',
	'adapter'  => 'adapter-wc-categories.php',
	'adapter_args' => array(
		'aether_categories_label'    => 'Shop by Category',
		'aether_categories_title'    => 'Find Your Fit',
		'aether_categories_subtitle' => '',
	),
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

$has_more           = ! empty( $sectionData['has_more'] );
$all_categories_url = isset( $sectionData['all_categories_url'] ) ? $sectionData['all_categories_url'] : '';
if ( empty( $all_categories_url ) && function_exists( 'wc_get_page_permalink' ) ) {
	$all_categories_url = wc_get_page_permalink( 'shop' );
}

// Section header — customizable via Customizer or section data.
$label    = isset( $sectionData['aether_categories_label'] ) ? $sectionData['aether_categories_label'] : __( 'Shop by Category', 'aureon' );
$title    = isset( $sectionData['aether_categories_title'] ) ? $sectionData['aether_categories_title'] : __( 'Find Your Fit', 'aureon' );
$subtitle = isset( $sectionData['aether_categories_subtitle'] ) ? $sectionData['aether_categories_subtitle'] : '';
?>
<section class="categories" id="categories" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="container">
		<?php
		aether_render_component( 'section/header', array(
			'label'    => $label,
			'title'    => $title,
			'subtitle' => $subtitle,
			'behavior' => $behavior,
		) );
		?>

		<div class="category-grid" data-reveal-group itemscope itemtype="https://schema.org/ItemList">
			<?php foreach ( $items as $index => $category ) : ?>
				<?php
				// Schema markup for each category item.
				$item_meta = '';
				if ( ! empty( $category['url'] ) && strpos( $category['url'], 'http' ) === 0 ) {
					$item_meta = sprintf(
						'<meta itemprop="url" content="%s">',
						esc_url( $category['url'] )
					);
				}

				// Grid placement lives on the wrapper (it is the grid item),
				// so the large card must stretch its wrapper.
				$item_class = 'category-grid-item';
				if ( ! empty( $category['modifier'] ) && 'large' === $category['modifier'] ) {
					$item_class .= ' category-grid-item--large';
				}
				?>
				<div class="<?php echo esc_attr( $item_class ); ?>" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<meta itemprop="position" content="<?php echo esc_attr( $index + 1 ); ?>">
					<?php aether_render_component( 'card/category', $category ); ?>
					<?php echo $item_meta; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- safe meta tag. ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $has_more && $all_categories_url ) : ?>
			<div class="category-grid-footer" data-reveal-item>
				<a href="<?php echo esc_url( $all_categories_url ); ?>" class="btn btn-outline categories-view-all">
					<?php esc_html_e( 'View All Categories', 'aureon' ); ?>
					<i class="fas fa-arrow-right" aria-hidden="true"></i>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
