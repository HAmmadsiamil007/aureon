<?php
/**
 * Contact form — mailto-free contact form with validation and nonce.
 *
 * Key:    'forms/contact'
 * Source: contact.html `.contact-form`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `array $fields    Input schema (name/type/required). Default [].`
 * - `string $action    Form handler URL. Default ''.`
 * - `string $nonce     WP nonce. Default ''.`
 * - `array $behavior  Behavior whitelist. Default [].`
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

$fields   = isset( $componentData['fields'] ) ? (array) $componentData['fields'] : array();
$action   = isset( $componentData['action'] ) ? $componentData['action'] : '';
$nonce    = isset( $componentData['nonce'] ) ? $componentData['nonce'] : '';
$behavior = isset( $componentData['behavior'] ) ? (array) $componentData['behavior'] : array();

if ( empty( $fields ) ) {
	return;
}
?>
<form id="contactForm" class="aether-contact-form" action="<?php echo esc_url( $action ); ?>" method="post" aria-label="Contact us form" data-magnetic="0.12">
	<input type="hidden" name="action" value="aether_contact_submit">
	<?php if ( $nonce ) : ?>
		<input type="hidden" name="aether_contact_nonce" value="<?php echo esc_attr( $nonce ); ?>">
	<?php endif; ?>
	<?php foreach ( $fields as $field ) : ?>
		<?php
		$name     = isset( $field['name'] ) ? $field['name'] : '';
		$label    = isset( $field['label'] ) ? $field['label'] : '';
		$type     = isset( $field['type'] ) ? $field['type'] : 'text';
		$required = ! empty( $field['required'] );
		$options  = isset( $field['options'] ) ? (array) $field['options'] : array();
		?>
		<div class="form-group">
			<label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
			<?php if ( 'select' === $type && ! empty( $options ) ) : ?>
				<select id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo $required ? 'required' : ''; // phpcs:ignore ?>>
					<option value="" disabled selected><?php esc_html_e( 'Select a subject', 'aureon' ); ?></option>
					<?php foreach ( $options as $value => $option_label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $option_label ); ?></option>
					<?php endforeach; ?>
				</select>
			<?php elseif ( 'textarea' === $type ) : ?>
				<textarea id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" placeholder="<?php echo esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' ); ?>" <?php echo $required ? 'required' : ''; // phpcs:ignore ?>></textarea>
			<?php else : ?>
				<input type="<?php echo esc_attr( 'email' === $type ? 'email' : 'text' ); ?>" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" placeholder="<?php echo esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' ); ?>" <?php echo $required ? 'required' : ''; // phpcs:ignore ?>>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
	<button type="submit" class="submit-btn" data-magnetic="0.12"><?php esc_html_e( 'Send Message', 'aureon' ); ?></button>
	<p class="aether-form-status" role="status" aria-live="polite" style="display:none;margin-top:12px;font-size:0.85rem;"></p>
</form>