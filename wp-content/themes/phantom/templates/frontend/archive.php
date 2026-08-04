<?php
/**
 * Archive — generic archive template (Phase 12).
 *
 * Thin WordPress template: all composition is delegated to the registry via
 * Templates\View::compose('archive', $data). No markup, no business logic.
 *
 * @package Phantom\Core\Templates
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- composition HTML is escaped at the leaf via ViewContext helpers.
echo \Phantom\Core\Templates\View::compose( 'archive', apply_filters( 'phantom_template_data', array(), 'archive' ) );
