<?php
/**
 * DialogManager — accessible dialog state contract.
 *
 * Phase 14 (Accessibility Engineering): the server-side contract for
 * accessible modals — declares the attributes a dialog must carry
 * (`role="dialog"`, `aria-modal`, `tabindex="-1"`, focusable title) and
 * validates rendered dialog markup. The runtime focus trap lives in the
 * Phase-11 behaviors entry (`components.ts`); this class is the PHP-side
 * guarantee + audit seam (plan §Phase 14: `A11y\\DialogManager` focus trap
 * impl).
 *
 * @package Lumina\Core\A11y
 * @since 0.14.0
 */

declare( strict_types=1 );

namespace Lumina\Core\A11y;

/**
 * Dialog attribute contract + validation.
 */
final class DialogManager {

	/**
	 * Required attributes for an accessible dialog element.
	 *
	 * @var list<string>
	 */
	private const REQUIRED_ATTRIBUTES = array(
		'role="dialog"',
		'aria-modal="true"',
		'tabindex="-1"',
	);

	/**
	 * The attribute set a dialog element must carry.
	 *
	 * @return list<string>
	 */
	public function required_attributes(): array {
		return self::REQUIRED_ATTRIBUTES;
	}

	/**
	 * Validate a rendered dialog element against the contract.
	 *
	 * @param string $element The `<div role="dialog">…</div>` element HTML.
	 * @return list<string> Missing attributes (empty = compliant).
	 */
	public function validate( string $element ): array {
		$missing = array();

		foreach ( self::REQUIRED_ATTRIBUTES as $attribute ) {
			if ( ! str_contains( $element, $attribute ) ) {
				$missing[] = $attribute;
			}
		}

		if ( ! preg_match( '/aria-labelledby=["\'][^"\']+["\']/', $element ) ) {
			$missing[] = 'aria-labelledby';
		}

		return $missing;
	}
}
