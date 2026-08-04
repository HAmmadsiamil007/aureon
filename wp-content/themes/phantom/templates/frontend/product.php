<?php
/**
 * Product — single product template (Phase 12).
 *
 * Thin WordPress template: composition is delegated to the registry. Product
 * data arrives via the WooCommerce Bridge (never direct `wc_` calls here).
 *
 * @package Phantom\Core\Templates
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- composition HTML is escaped at the leaf via ViewContext helpers.
echo \Phantom\Core\Templates\View::compose( 'product', apply_filters( 'phantom_template_data', array(), 'product' ) );
