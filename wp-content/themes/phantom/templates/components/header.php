<?php
/**
 * Header — site header: logo, nav slot, actions slot, optional sticky state.
 *
 * Expected data: logo, logo_text, home_url, sticky, class (variant-merged),
 * nav (slot HTML), actions (slot HTML).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<header
	class="<?php echo $view->attr( $view->prop( 'class', 'phantom-header' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	<?php echo $view->prop( 'sticky' ) ? 'data-phantom-sticky' : ''; ?>
>
	<div class="phantom-header__inner">
		<?php if ( $view->prop( 'logo' ) || $view->prop( 'logo_text' ) ) : ?>
			<a class="phantom-header__brand" href="<?php echo $view->url( $view->prop( 'home_url', '/' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" rel="home">
				<?php if ( $view->prop( 'logo' ) ) : ?>
					<img class="phantom-header__logo" src="<?php echo $view->url( $view->prop( 'logo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="" loading="eager" />
				<?php else : ?>
					<span class="phantom-header__wordmark"><?php echo $view->e( $view->prop( 'logo_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
				<?php endif; ?>
			</a>
		<?php endif; ?>

		<?php if ( $view->prop( 'nav' ) ) : ?>
			<nav class="phantom-header__nav" aria-label="<?php echo $view->attr( 'Primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
				<?php echo $view->prop( 'nav' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?>
			</nav>
		<?php endif; ?>

		<?php if ( $view->prop( 'actions' ) ) : ?>
			<div class="phantom-header__actions"><?php echo $view->prop( 'actions' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?></div>
		<?php endif; ?>
	</div>
</header>
