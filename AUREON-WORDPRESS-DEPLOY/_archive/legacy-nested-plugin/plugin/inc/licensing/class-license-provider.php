<?php
/**
 * License provider interface.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Contract for license providers.
 *
 * The null provider ships by default: the product is always unlocked and
 * requires no activation. Implement this interface in a future commercial
 * provider and return it via the `aureon_studio_license_provider` filter.
 */
interface Aureon_Pro_License_Provider {
	/**
	 * Whether the product is licensed / unlocked.
	 *
	 * @return bool
	 */
	public function is_active();

	/**
	 * The current license status string.
	 *
	 * @return string
	 */
	public function get_status();
}

/**
 * Default license provider: everything unlocked, no phone-home.
 */
class Aureon_Pro_Null_License_Provider implements Aureon_Pro_License_Provider {
	/**
	 * {@inheritdoc}
	 */
	public function is_active() {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_status() {
		return 'valid';
	}
}

/**
 * Get the active license provider.
 *
 * Swap in a commercial provider:
 * add_filter( 'aureon_studio_license_provider', fn() => new My_Provider() );
 *
 * @return Aureon_Pro_License_Provider
 */
function aureon_premium_get_license_provider() {
	return apply_filters( 'aureon_studio_license_provider', new Aureon_Pro_Null_License_Provider() );
}
