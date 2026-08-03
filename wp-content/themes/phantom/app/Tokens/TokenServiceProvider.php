<?php
/**
 * TokenServiceProvider — wire the Design Token Engine into the container.
 *
 * Phase 3 (Design Token Engine): binds the token subsystem services so later
 * phases (Render Engine, Components) resolve tokens via the container/App
 * facade. Registered in Config\config.php 'providers' and booted by the
 * Kernel's providers step (ADR-014).
 *
 * @package Phantom\Core\Tokens
 * @since 0.3.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Tokens;

use Phantom\Core\Container\Container;
use Phantom\Core\Providers\ServiceProviderInterface;
use Phantom\Core\Tokens\Loader\DataProvider;
use Phantom\Core\Tokens\Renderer\CssRenderer;

/**
 * Registers token engine services.
 */
final class TokenServiceProvider implements ServiceProviderInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function register( Container $container ): void {
		$container->singleton( 'tokens.provider', static fn(): DataProvider => new DataProvider() );
		$container->singleton( 'tokens.source', static fn(): TokenSource => new TokenSource() );
		$container->singleton( 'tokens.preced', static fn(): Preced => new Preced() );
		$container->singleton( 'tokens.resolver', static fn(): Resolver => new Resolver() );
		$container->singleton( 'tokens.renderer', static fn(): CssRenderer => new CssRenderer() );
		$container->singleton( 'tokens.invariant', static fn(): Invariant => new Invariant() );

		$container->singleton(
			'tokens.repository',
			static fn( Container $c ): TokenRepository => new TokenRepository(
				$c->get( 'tokens.provider' ),
				$c->get( 'tokens.source' ),
				$c->get( 'tokens.preced' ),
				$c->get( 'tokens.resolver' ),
				$c->get( 'tokens.renderer' ),
				$c->get( 'tokens.invariant' )
			)
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function boot( Container $container ): void {
		// No WordPress hooks needed in Phase 3 — CSS emission is on-demand.
	}
}
