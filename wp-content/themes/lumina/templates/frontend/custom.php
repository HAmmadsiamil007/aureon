<?php
/**
 * Custom — custom page template (Phase 12).
 *
 * Composes a caller-selected template slug (via the `lumina_template_slug`
 * filter, default 'landing'). Thin WordPress template: all composition is
 * delegated to the registry. No markup, no business logic.
 *
 * @package Lumina\Core\Templates
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * The template slug to compose (filterable for custom page templates).
 *
 * @var string $slug
 */
$slug = (string) apply_filters( 'lumina_template_slug', 'landing' );

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- composition HTML is escaped at the leaf via ViewContext helpers.
echo \Lumina\Core\Templates\View::compose( $slug, apply_filters( 'lumina_template_data', array(), $slug ) );
