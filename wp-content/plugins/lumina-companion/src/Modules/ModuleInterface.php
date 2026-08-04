<?php
/**
 * ModuleInterface — contract for every Lumina Companion module.
 *
 * All modules are original implementations of premium feature categories.
 * Each module may opt into: register() (WP hooks, guarded), customizer()
 * (settings), css() (inline token-driven CSS), and template_data() (data
 * injection for the Lumina composition pipeline).
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * Module contract.
 */
interface ModuleInterface {

	/**
	 * Module slug (unique, used as the option prefix).
	 *
	 * @return string
	 */
	public function slug(): string;

	/**
	 * Human-readable module label.
	 *
	 * @return string
	 */
	public function label(): string;
}
