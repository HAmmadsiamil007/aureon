<?php
/**
 * Wishlist section — saved-items grid with login/empty states.
 *
 * Source: wishlist.html .wishlist-section
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'wishlist', array(
	'template' => 'sections/section-wishlist.php',
	'adapter'  => 'adapter-wishlist.php',
	'behavior' => array( 'reveal-group' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$items       = isset( $sectionData['items'] ) ? (array) $sectionData['items'] : array();
$status      = isset( $sectionData['status'] ) ? $sectionData['status'] : 'empty';
$behavior    = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();
$shop_url    = isset( $sectionData['shop_url'] ) ? $sectionData['shop_url'] : home_url( '/' );
$account_url = isset( $sectionData['account_url'] ) ? $sectionData['account_url'] : wp_login_url();

$title   = __( 'Your wishlist', 'aureon' );
$message = __( 'Save the pieces you love and find them here.', 'aureon' );
$cta     = array( 'label' => __( 'Shop the Collection', 'aureon' ), 'url' => $shop_url );

if ( 'logged_out' === $status ) {
	$title   = __( 'Your wishlist awaits', 'aureon' );
	$message = __( 'Sign in to see the items you have saved.', 'aureon' );
	$cta     = array( 'label' => __( 'Sign In', 'aureon' ), 'url' => $account_url );
} elseif ( 'empty' === $status ) {
	$title   = __( 'Your wishlist is empty', 'aureon' );
	$message = __( 'Save the pieces you love and find them here.', 'aureon' );
}
?>
<section class="wishlist-section section" id="wishlist" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="container">
		<?php if ( 'ready' === $status && ! empty( $items ) ) : ?>
			<div class="wishlist-grid" data-reveal-group data-wishlist-grid>
				<?php foreach ( $items as $product ) : ?>
					<?php aether_render_component( 'card/wishlist', $product ); ?>
				<?php endforeach; ?>
			</div>
			<div class="wishlist-empty" data-wishlist-empty hidden>
				<h2 class="wishlist-empty-title"><?php echo esc_html( $title ); ?></h2>
				<p class="wishlist-empty-message"><?php echo esc_html( $message ); ?></p>
				<a href="<?php echo esc_url( $cta['url'] ); ?>" class="btn btn-primary" data-magnetic="0.12"><?php echo esc_html( $cta['label'] ); ?></a>
			</div>
		<?php else : ?>
			<div class="wishlist-empty">
				<h2 class="wishlist-empty-title"><?php echo esc_html( $title ); ?></h2>
				<p class="wishlist-empty-message"><?php echo esc_html( $message ); ?></p>
				<a href="<?php echo esc_url( $cta['url'] ); ?>" class="btn btn-primary" data-magnetic="0.12"><?php echo esc_html( $cta['label'] ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>
