<?php
/**
 * Lumen design pack token defaults (M4 mechanism).
 *
 * Merged onto aureon_option_defaults at priority 20 — after engine defaults,
 * before nothing: saved Customizer values always win because defaults only
 * apply when an option is unset. Lumen maps a light editorial identity onto
 * the generic contract token names so every base component/CSS keeps working.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	'aether_color_bg'           => '#FAFAF7',
	'aether_color_surface'      => '#FFFFFF',
	'aether_color_surface_2'    => '#F3F0EA',
	'aether_color_surface_3'    => '#EAE6DD',
	'aether_color_text'         => '#1C1917',
	'aether_color_muted'        => '#6E675E',
	'aether_color_accent'       => '#0F766E',
	'aether_color_accent_hover' => '#115E59',
	'aether_color_border'       => '#E6E1D7',
	'aether_color_error'        => '#B3261E',
	'aether_color_success'      => '#2E7D32',
	'aether_font_heading'       => 'Fraunces, Georgia, serif',
	'aether_font_body'          => 'Inter, system-ui, sans-serif',
);