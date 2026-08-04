<?php
/**
 * Cart — cart page template (Phase 12).
 *
 * Thin WordPress template: composition is delegated to the registry. Cart
 * data arrives via the WooCommerce Bridge (never direct `wc_` calls here).
 *
 * @package Lumina\Core\Templates
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- composition HTML is escaped at the leaf via ViewContext helpers.
echo \Lumina\Core\Templates\View::compose( 'cart', apply_filters( 'lumina_template_data', array(), 'cart' ) );
