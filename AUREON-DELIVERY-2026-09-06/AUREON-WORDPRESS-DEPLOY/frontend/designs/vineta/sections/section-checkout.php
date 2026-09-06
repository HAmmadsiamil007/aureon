<?php
/**
 * Checkout section — page banner, WC billing/payment fields and order summary.
 *
 * All fields carry real WooCommerce names (billing_*, payment_method) so the
 * form posts back through WC_Form_Handler::checkout_action(). Totals and
 * items come from adapter-cart (context: checkout).
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

aether_register_section( 'checkout', array(
	'template'     => 'sections/section-checkout.php',
	'adapter'      => 'adapter-cart.php',
	'adapter_args' => array(
		'context' => 'checkout',
	),
	'behavior'     => array( 'parallax-section' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

if ( ! function_exists( 'aether_checkout_render_field' ) ) {
	/**
	 * Render a single WC checkout field as a source-style form group.
	 *
	 * @param string $key   WC field key (billing_email etc).
	 * @param array  $field WC field config.
	 * @return void
	 */
	function aether_checkout_render_field( $key, $field ) {
		$label = isset( $field['label'] ) ? $field['label'] : '';
		$required = ! empty( $field['required'] );
		$type     = isset( $field['type'] ) ? $field['type'] : 'text';
		$value    = WC()->checkout ? WC()->checkout->get_value( $key ) : '';

		if ( 'checkbox' === $type ) {
			?>
			<div class="form-group">
				<label class="checkbox">
					<input type="checkbox" class="input-checkbox" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( $value, 1 ); ?> />
					<span style="text-transform:none;letter-spacing:0;color:var(--white);"><?php echo wp_kses_post( $label ); ?></span>
				</label>
			</div>
			<?php
			return;
		}

		?>
		<div class="form-group">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?> <?php echo $required ? '<span style="color:var(--gold);">*</span>' : ''; // phpcs:ignore ?></label>
			<input
				type="<?php echo esc_attr( 'email' === $type ? 'email' : ( 'tel' === $type ? 'tel' : 'text' ) ); ?>"
				id="<?php echo esc_attr( $key ); ?>"
				name="<?php echo esc_attr( $key ); ?>"
				class="input-text"
				placeholder="<?php echo esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				autocomplete="<?php echo esc_attr( isset( $field['autocomplete'] ) ? $field['autocomplete'] : 'off' ); ?>"
				<?php echo $required ? 'required' : ''; // phpcs:ignore ?>
			>
		</div>
		<?php
	}
}

$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();
$checkout = WC()->checkout;
$fields   = $checkout ? $checkout->get_checkout_fields() : array();
$billing  = isset( $fields['billing'] ) ? $fields['billing'] : array();
$order_f  = isset( $fields['order'] ) ? $fields['order'] : array();
$terms    = isset( $order_f['terms'] ) ? $order_f['terms'] : array();

$countries = array();
if ( function_exists( 'WC' ) && WC()->countries ) {
	$countries = WC()->countries->get_countries();
}
?>
<?php
aether_render_component( 'hero/page-banner', array(
	'title'    => isset( $sectionData['title'] ) ? $sectionData['title'] : '',
	'crumbs'   => isset( $sectionData['crumbs'] ) ? $sectionData['crumbs'] : array(),
	'behavior' => $behavior,
) );
?>
<section class="checkout-section" data-phantom-bg="hero">
	<div class="container">
		<?php if ( function_exists( 'wc_print_notices' ) ) { wc_print_notices(); } ?>
		<form name="checkout" class="checkout woocommerce-checkout aether-checkout-form" action="<?php echo esc_url( isset( $sectionData['checkout_url'] ) ? $sectionData['checkout_url'] : '#' ); ?>" method="post" enctype="multipart/form-data">
			<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
			<input type="hidden" name="ship_to_different_address" value="0">
			<div class="row g-5">
				<div class="col-lg-7">
					<div class="checkout-form-wrap">
						<h3><?php esc_html_e( 'Contact', 'aureon' ); ?></h3>
						<div class="form-row-custom">
							<?php if ( isset( $billing['billing_email'] ) ) { aether_checkout_render_field( 'billing_email', $billing['billing_email'] ); } ?>
							<?php if ( isset( $billing['billing_phone'] ) ) { aether_checkout_render_field( 'billing_phone', $billing['billing_phone'] ); } ?>
						</div>

						<h3 style="margin-top:32px;"><?php esc_html_e( 'Shipping', 'aureon' ); ?></h3>
						<div class="form-row-custom">
							<?php if ( isset( $billing['billing_first_name'] ) ) { aether_checkout_render_field( 'billing_first_name', $billing['billing_first_name'] ); } ?>
							<?php if ( isset( $billing['billing_last_name'] ) ) { aether_checkout_render_field( 'billing_last_name', $billing['billing_last_name'] ); } ?>
						</div>
						<?php if ( isset( $billing['billing_address_1'] ) ) { aether_checkout_render_field( 'billing_address_1', $billing['billing_address_1'] ); } ?>
						<?php if ( isset( $billing['billing_address_2'] ) ) { aether_checkout_render_field( 'billing_address_2', $billing['billing_address_2'] ); } ?>
						<div class="form-row-custom">
							<?php if ( isset( $billing['billing_city'] ) ) { aether_checkout_render_field( 'billing_city', $billing['billing_city'] ); } ?>
							<?php if ( isset( $billing['billing_state'] ) ) { aether_checkout_render_field( 'billing_state', $billing['billing_state'] ); } ?>
						</div>
						<div class="form-row-custom">
							<?php if ( isset( $billing['billing_postcode'] ) ) { aether_checkout_render_field( 'billing_postcode', $billing['billing_postcode'] ); } ?>
							<?php if ( isset( $billing['billing_country'] ) ) : ?>
								<div class="form-group">
									<label for="billing_country"><?php esc_html_e( 'Country', 'aureon' ); ?> <span style="color:var(--gold);">*</span></label>
									<select id="billing_country" name="billing_country" class="country_to_state" required>
										<?php foreach ( $countries as $code => $name ) : ?>
											<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $code, WC()->checkout->get_value( 'billing_country' ) ); ?>><?php echo esc_html( $name ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							<?php endif; ?>
						</div>

						<h3 style="margin-top:32px;"><?php esc_html_e( 'Payment', 'aureon' ); ?></h3>
						<?php
						$gateways = function_exists( 'WC' ) && WC()->payment_gateways ? WC()->payment_gateways->get_available_payment_gateways() : array();
						if ( ! empty( $gateways ) ) :
							$first = true;
							echo '<div class="payment_methods">';
							foreach ( $gateways as $gateway ) :
								?>
								<div class="form-group">
									<label class="checkbox" style="cursor:pointer;">
										<input type="radio" class="input-radio" id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $first, true ); ?> />
										<span style="text-transform:none;letter-spacing:0;color:var(--white);"><?php echo esc_html( $gateway->get_title() ); ?></span>
									</label>
									<?php
									if ( $gateway->has_fields() || $gateway->get_description() ) {
										echo '<div class="payment_box payment_method_' . esc_attr( $gateway->id ) . '" style="margin:8px 0 20px 0;padding:12px 16px;background:rgba(9,9,11,0.6);border:1px solid rgba(168,181,192,0.15);font-size:0.85rem;color:var(--chrome);">';
										$gateway->payment_fields();
										echo '</div>';
									}
									?>
								</div>
								<?php
								$first = false;
							endforeach;
							echo '</div>';
						endif;
						?>
						<?php if ( ! empty( $terms ) ) { aether_checkout_render_field( 'terms', $terms ); } ?>
					</div>
				</div>
				<div class="col-lg-5">
					<?php
					aether_render_component( 'checkout/order-items', array(
						'items'    => isset( $sectionData['items'] ) ? $sectionData['items'] : array(),
						'subtotal' => isset( $sectionData['subtotal'] ) ? $sectionData['subtotal'] : '',
						'shipping' => isset( $sectionData['shipping'] ) ? $sectionData['shipping'] : '',
						'tax'      => isset( $sectionData['tax'] ) ? $sectionData['tax'] : '',
						'total'    => isset( $sectionData['total'] ) ? $sectionData['total'] : '',
					) );
					?>
				</div>
			</div>
		</form>
	</div>
</section>
