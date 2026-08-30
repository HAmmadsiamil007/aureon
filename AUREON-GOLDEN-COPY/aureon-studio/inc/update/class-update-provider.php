<?php
/**
 * Update provider interface.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Contract for update providers.
 *
 * The null provider ships by default: updates rely on standard WordPress
 * behavior. Implement this interface for a future commercial update server
 * and return it via the `aureon_studio_update_provider` filter.
 */
interface Aureon_Pro_Update_Provider {
	/**
	 * Hook up update checks.
	 *
	 * @return void
	 */
	public function init();
}

/**
 * Default update provider: no custom update checks.
 */
class Aureon_Pro_Null_Update_Provider implements Aureon_Pro_Update_Provider {
	/**
	 * {@inheritdoc}
	 */
	public function init() {
		// Updates rely on the standard WordPress plugin update process.
	}
}

/**
 * Get the active update provider.
 *
 * Swap in a commercial provider:
 * add_filter( 'aureon_studio_update_provider', fn() => new My_Provider() );
 *
 * @return Aureon_Pro_Update_Provider
 */
function aureon_premium_get_update_provider() {
	return apply_filters( 'aureon_studio_update_provider', new Aureon_Pro_Null_Update_Provider() );
}
