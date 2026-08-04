<?php
/**
 * DataAdapterInterface — normalizes vendor/WP data into ViewModels.
 *
 * Phase 4 (Render Engine): the contract between the data layer and the render
 * path. Adapters isolate WordPress/plugin specifics (plan: "data adapters
 * deliver data in normalized DTO shape") so components never inspect WP
 * globals directly. Every adapter must remain safe to instantiate and call in
 * WP-free CLI/smoke contexts: capability detection is done via function/
 * class guards inside each implementation.
 *
 * @package Lumina\Core\Data
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Data;

use Lumina\Core\Render\ViewModel;

/**
 * Converts a source (post, term, user, menu, query, …) into a ViewModel.
 */
interface DataAdapterInterface {

	/**
	 * Whether this adapter can normalize the given source.
	 *
	 * @param mixed $source Source value.
	 * @return bool
	 */
	public function supports( mixed $source ): bool;

	/**
	 * Normalize the source into a ViewModel.
	 *
	 * @param mixed                $source  Source value.
	 * @param array<string, mixed> $options Adapter options (e.g. taxonomy name).
	 * @return ViewModel
	 */
	public function adapt( mixed $source, array $options = array() ): ViewModel;
}
