<?php
/**
 * Blog grid section — recent posts.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'blog-grid', array(
	'template' => 'sections/section-blog-grid.php',
	'adapter'  => 'adapter-blog.php',
	'behavior' => array( 'reveal-group' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$items    = isset( $sectionData['items'] ) ? (array) $sectionData['items'] : array();
$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();
$paged    = isset( $sectionData['paged'] ) ? (array) $sectionData['paged'] : array();
$total    = isset( $paged['total'] ) ? (int) $paged['total'] : 0;

if ( empty( $items ) && 0 === $total ) {
	// Graceful empty state instead of a silent return (F8-1) — search
	// archives get contextual copy when a query was provided.
	aether_render_component( 'utility/empty-state', array(
		'title'       => ! empty( $sectionData['s'] ) ? __( 'Nothing found', 'aureon' ) : __( 'No stories yet', 'aureon' ),
		'description' => ! empty( $sectionData['s'] )
			? __( 'No posts matched your search. Try different keywords.', 'aureon' )
			: __( 'There are no posts here yet. Check back soon — the void has plenty of stories to tell.', 'aureon' ),
		'icon'        => ! empty( $sectionData['s'] ) ? 'fa-search' : 'fa-book-open',
		'behavior'    => $behavior,
	) );
	return;
}
?>
<section class="blog-grid-section section" id="blogGrid" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="container">
		<?php
		aether_render_component( 'section/header', array(
			'label'    => isset( $sectionData['label'] ) ? $sectionData['label'] : __( 'Journal', 'aureon' ),
			'title'    => isset( $sectionData['title'] ) ? $sectionData['title'] : __( 'From the Void', 'aureon' ),
			'subtitle' => isset( $sectionData['subtitle'] ) ? $sectionData['subtitle'] : '',
			'behavior' => $behavior,
		) );
		?>

		<div class="blog-grid" data-reveal-group>
			<?php foreach ( $items as $post ) : ?>
				<?php aether_render_component( 'card/blog', $post ); ?>
			<?php endforeach; ?>
		</div>

		<?php
		$show_pagination = isset( $sectionData['show_pagination'] ) ? (bool) $sectionData['show_pagination'] : true;
		if ( ! empty( $paged ) && $show_pagination ) {
			aether_render_component( 'section/pagination', array(
				'current' => isset( $paged['current'] ) ? $paged['current'] : 1,
				'total'   => isset( $paged['total'] ) ? $paged['total'] : 1,
				'base'    => isset( $paged['base'] ) ? $paged['base'] : '',
			) );
		}
		?>
	</div>
</section>
