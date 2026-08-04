<?php
/**
 * UnknownToken — thrown when a token name cannot be resolved.
 *
 * Phase 3 (Design Token Engine): TokenRepository::token() raises this when the
 * requested token does not exist in the merged (default → preset → override)
 * map. Name-validated lookups guarantee this is only reachable for genuinely
 * unknown tokens, not injection attempts.
 *
 * @package Lumina\Core\Tokens
 * @since 0.3.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Tokens;

/**
 * Unknown design token.
 */
final class UnknownToken extends \RuntimeException {
}
