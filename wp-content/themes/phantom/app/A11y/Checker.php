<?php
/**
 * Checker — static HTML accessibility audit.
 *
 * Phase 14 (Accessibility Engineering): `run(string $html): array` performs a
 * deterministic, dependency-free WCAG-oriented audit of rendered HTML:
 * heading hierarchy (single h1, no skipped levels), landmarks (header/nav/
 * main/footer), image alt attributes, form labels, link/button accessible
 * names, aria attributes for interactive controls, and sequential focus
 * order. WP-free safe — it is a pure string analyzer, so CI can audit every
 * component/template render without a browser (plan §Phase 14:
 * `A11y\Checker::run(string $html): array`).
 *
 * @package Phantom\Core\A11y
 * @since 0.14.0
 */

declare( strict_types=1 );

namespace Phantom\Core\A11y;

/**
 * Static HTML accessibility audit.
 */
final class Checker {

	/**
	 * Audit rendered HTML, returning findings.
	 *
	 * @param string $html Rendered HTML.
	 * @return array{pass: bool, findings: list<string>}
	 */
	public function run( string $html ): array {
		$findings = array();

		$this->check_headings( $html, $findings );
		$this->check_landmarks( $html, $findings );
		$this->check_images( $html, $findings );
		$this->check_forms( $html, $findings );
		$this->check_interactive( $html, $findings );
		$this->check_focus( $html, $findings );

		return array(
			'pass'     => array() === $findings,
			'findings' => $findings,
		);
	}

	/**
	 * Heading hierarchy: exactly one h1, no level skips.
	 *
	 * @param string $html     HTML.
	 * @param array  $findings Findings collector.
	 * @return void
	 */
	private function check_headings( string $html, array &$findings ): void {
		if ( ! preg_match_all( '/<h([1-6])(?:\s[^>]*)?>/i', $html, $matches ) ) {
			$findings[] = 'no headings found';

			return;
		}

		$levels = array_map( 'intval', $matches[1] );
		$h1     = array_filter( $levels, static fn( int $level ): bool => 1 === $level );

		if ( 0 === count( $h1 ) ) {
			$findings[] = 'no h1 element';
		} elseif ( count( $h1 ) > 1 ) {
			$findings[] = sprintf( 'multiple h1 elements (%d)', count( $h1 ) );
		}

		$previous = 0;

		foreach ( $levels as $level ) {
			if ( $previous > 0 && $level > $previous + 1 ) {
				$findings[] = sprintf( 'heading level skipped from h%d to h%d', $previous, $level );
			}

			$previous = $level;
		}
	}

	/**
	 * Landmarks: header/nav/main/footer present where expected.
	 *
	 * @param string $html     HTML.
	 * @param array  $findings Findings collector.
	 * @return void
	 */
	private function check_landmarks( string $html, array &$findings ): void {
		foreach ( array( 'header', 'main', 'footer' ) as $landmark ) {
			if ( ! preg_match( '/<' . $landmark . '(?:\s|>)/i', $html ) ) {
				$findings[] = "missing {$landmark} landmark";
			}
		}

		if ( ! preg_match( '/<(nav|aside)(?:\s|>)/i', $html ) ) {
			$findings[] = 'no nav or aside landmark';
		}
	}

	/**
	 * Images carry alt attributes.
	 *
	 * @param string $html     HTML.
	 * @param array  $findings Findings collector.
	 * @return void
	 */
	private function check_images( string $html, array &$findings ): void {
		if ( ! preg_match_all( '/<img\b[^>]*>/i', $html, $matches ) ) {
			return;
		}

		foreach ( $matches[0] as $tag ) {
			if ( ! preg_match( '/\balt=/i', $tag ) ) {
				$findings[] = 'img without alt attribute';
			}
		}
	}

	/**
	 * Form controls have labels (for/id or wrapping label).
	 *
	 * @param string $html     HTML.
	 * @param array  $findings Findings collector.
	 * @return void
	 */
	private function check_forms( string $html, array &$findings ): void {
		if ( ! preg_match_all( '/<(input|select|textarea)\b[^>]*>/i', $html, $matches ) ) {
			return;
		}

		// Only label for= references count as associations; the control's own
		// id must be referenced by a label (or carry aria-label/labelledby).
		$ids = array();

		if ( preg_match_all( '/<label\b[^>]*for=["\']([^"\']+)["\']/i', $html, $labels ) ) {
			$ids = $labels[1];
		}

		foreach ( $matches[0] as $tag ) {
			$type = '';

			if ( preg_match( '/\btype=["\']([^"\']+)["\']/i', $tag, $type_match ) ) {
				$type = strtolower( $type_match[1] );
			}

			// Hidden inputs and submit/button/reset controls need no label.
			if ( in_array( $type, array( 'hidden', 'submit', 'button', 'reset' ), true ) ) {
				continue;
			}

			$labeled = (bool) preg_match( '/\b(aria-label|aria-labelledby|title)=/i', $tag );

			if ( ! $labeled && ! preg_match( '/\bid=["\']([^"\']+)["\']/i', $tag, $id_match ) ) {
				$findings[] = 'form control without label or id';

				continue;
			}

			if ( isset( $id_match[1] ) && ! in_array( $id_match[1], $ids, true ) ) {
				$findings[] = 'form control id not referenced by a label';
			}
		}
	}

	/**
	 * Interactive controls carry accessible names.
	 *
	 * @param string $html     HTML.
	 * @param array  $findings Findings collector.
	 * @return void
	 */
	private function check_interactive( string $html, array &$findings ): void {
		foreach ( array( 'a', 'button' ) as $tag ) {
			if ( ! preg_match_all( '/<' . $tag . '\b[^>]*>(.*?)<\/' . $tag . '>/is', $html, $matches ) ) {
				continue;
			}

			foreach ( $matches[0] as $index => $element ) {
				// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags -- WP-free analyzer; PHP's strip_tags() is the portable choice.
				$inner = trim( strip_tags( (string) $matches[1][ $index ] ) );
				// An image with alt text inside the element names the link/button.
				$has_image_name = (bool) preg_match( '/<img\b[^>]*\balt=/i', $element );

				if ( '' === $inner && ! $has_image_name
					&& ! preg_match( '/\b(aria-label|aria-labelledby|title)=/i', $element )
				) {
					$findings[] = "{$tag} without accessible name";
				}
			}
		}
	}

	/**
	 * Focus: no positive tabindex; modals are focusable (tabindex="-1").
	 *
	 * @param string $html     HTML.
	 * @param array  $findings Findings collector.
	 * @return void
	 */
	private function check_focus( string $html, array &$findings ): void {
		if ( preg_match_all( '/\btabindex=["\']([^"\']+)["\']/i', $html, $indexes ) ) {
			foreach ( $indexes[1] as $index ) {
				if ( is_numeric( $index ) && (int) $index > 0 ) {
					$findings[] = 'positive tabindex found';
				}
			}
		}

		// role="dialog" without tabindex="-1" cannot receive focus on open.
		// Match each dialog opening tag, then test tabindex within that same
		// tag (attribute order is not semantically significant in HTML).
		$has_unfocusable_dialog = false;

		if ( preg_match_all( '/<[a-z0-9\-]+\b[^>]*role=["\']dialog["\'][^>]*>/i', $html, $dialogs ) ) {
			foreach ( $dialogs[0] as $dialog ) {
				if ( ! preg_match( '/\btabindex=["\']-1["\']/i', $dialog ) ) {
					$has_unfocusable_dialog = true;

					break;
				}
			}
		}

		if ( $has_unfocusable_dialog ) {
			$findings[] = 'dialog without tabindex="-1"';
		}
	}
}
