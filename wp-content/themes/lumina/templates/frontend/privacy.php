<?php
/**
 * Privacy — privacy policy template (Phase 12).
 *
 * Thin WordPress template: all composition is delegated to the registry via
 * Templates\View::compose('privacy', $data). No markup, no business logic.
 *
 * @package Lumina\Core\Templates
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- composition HTML is escaped at the leaf via ViewContext helpers.
echo \Lumina\Core\Templates\View::compose( 'privacy', apply_filters( 'lumina_template_data', array(), 'privacy' ) );
