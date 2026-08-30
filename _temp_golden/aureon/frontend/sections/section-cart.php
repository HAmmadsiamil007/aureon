<?php
/**
 * Cart section — page banner, cart items and order summary.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

aether_register_section( 'cart', array(
	'template'     => 'sections/section-cart.php',
	'adapter'      => 'adapter-cart.php',
	'adapter_args' => array(
		'context' => 'cart',
	),
	'behavior'     => array( 'parallax-section' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();
$is_empty = ! empty( $sectionData['is_empty'] );
?>
<?php
aether_render_component( 'hero/page-banner', array(
	'title'    => isset( $sectionData['title'] ) ? $sectionData['title'] : '',
	'crumbs'   => isset( $sectionData['crumbs'] ) ? $sectionData['crumbs'] : array(),
	'behavior' => $behavior,
) );
?>
<section class="cart-section" data-phantom-bg="hero">
	<div class="container">
		<div class="row">
			<div class="col-lg-8">
				<?php if ( $is_empty ) : ?>
					<div class="empty-cart">
						<i class="fas fa-shopping-bag"></i>
						<h2><?php esc_html_e( 'Your cart is empty', 'aureon' ); ?></h2>
						<p><?php esc_html_e( 'Explore the collection and find your next piece.', 'aureon' ); ?></p>
						<a href="<?php echo esc_url( isset( $sectionData['shop_url'] ) ? $sectionData['shop_url'] : home_url( '/' ) ); ?>" class="checkout-btn" style="display:inline-block;width:auto;padding:14px 40px;"><?php esc_html_e( 'Continue Shopping', 'aureon' ); ?></a>
					</div>
				<?php else : ?>
					<?php
					aether_render_component( 'cart/items', array(
						'items'    => isset( $sectionData['items'] ) ? $sectionData['items'] : array(),
						'cart_url' => isset( $sectionData['cart_url'] ) ? $sectionData['cart_url'] : '#',
						'shop_url' => isset( $sectionData['shop_url'] ) ? $sectionData['shop_url'] : home_url( '/' ),
					) );
					?>
				<?php endif; ?>
			</div>
			<div class="col-lg-4">
				<?php if ( ! $is_empty ) : ?>
					<?php
					aether_render_component( 'cart/summary', array(
						'subtotal'     => isset( $sectionData['subtotal'] ) ? $sectionData['subtotal'] : '',
						'shipping'     => isset( $sectionData['shipping'] ) ? $sectionData['shipping'] : '',
						'total'        => isset( $sectionData['total'] ) ? $sectionData['total'] : '',
						'checkout_url' => isset( $sectionData['checkout_url'] ) ? $sectionData['checkout_url'] : '#',
						'shop_url'     => isset( $sectionData['shop_url'] ) ? $sectionData['shop_url'] : home_url( '/' ),
					) );
					?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<script>
( function() {
	// Cart quantity +/- — post the real WooCommerce cart form (with the
	// update_cart marker WC_Form_Handler expects) and swap in the fresh
	// form + summary from the response. Delegated so it survives swaps.
	document.addEventListener( 'click', function( e ) {
		var btn = e.target.closest ? e.target.closest( '.aether-qty-btn' ) : null;
		if ( ! btn ) {
			return;
		}
		var form = btn.closest( '.woocommerce-cart-form' );
		var input = btn.parentElement.querySelector( '.qty-value' );
		if ( ! form || ! input ) {
			return;
		}
		var next = parseInt( input.value, 10 ) + parseInt( btn.getAttribute( 'data-dir' ) || '0', 10 );
		if ( next < 1 ) {
			next = 1;
		}
		input.value = next;
		var data = new FormData( form );
		data.append( 'update_cart', 'Update Cart' );
		fetch( form.getAttribute( 'action' ), { method: 'POST', body: data } )
			.then( function( res ) { return res.text(); } )
			.then( function( html ) {
				var doc = new DOMParser().parseFromString( html, 'text/html' );
				var fresh = doc.querySelector( '.woocommerce-cart-form' );
				if ( fresh ) {
					form.replaceWith( fresh );
				}
				var oldSummary = document.querySelector( '.cart-summary' );
				var freshSummary = doc.querySelector( '.cart-summary' );
				if ( oldSummary && freshSummary ) {
					oldSummary.replaceWith( freshSummary );
				}
			} )
			.catch( function() { window.location.reload(); } );
	} );
}() );
</script>
