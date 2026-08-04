<?php
/**
 * RenderException — thrown by the Render Engine on unrecoverable render errors.
 *
 * Phase 4 (Render Engine): raised when a view cannot be resolved or a template
 * engine fails mid-render. The Renderer never lets this escape to the
 * WordPress surface unhandled; callers may catch it and fall back (the plan
 * requires a graceful fallback block in production, never `die`).
 *
 * @package Phantom\Core\Render
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Render;

/**
 * Render engine failure.
 */
class RenderException extends \RuntimeException {}
