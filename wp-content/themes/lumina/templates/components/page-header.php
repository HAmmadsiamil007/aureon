<?php
/**
 * PageHeader — page hero header with title, description, optional image and
 * breadcrumb integration.
 *
 * Expected data: title, description, image, image_alt.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<header class="lumina-page-header">
	<?php if ( $view->prop( 'image' ) ) : ?>
		<img class="lumina-page-header__bg" src="<?php echo $view->url( $view->prop( 'image' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="<?php echo $view->attr( $view->prop( 'image_alt', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" loading="lazy" />
	<?php endif; ?>

	<div class="lumina-page-header__inner">
		<?php if ( $view->prop( 'title' ) ) : ?>
			<h1 class="lumina-page-header__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h1>
		<?php endif; ?>
		<?php if ( $view->prop( 'description' ) ) : ?>
			<p class="lumina-page-header__description"><?php echo $view->e( $view->prop( 'description' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
		<?php endif; ?>
	</div>
</header>
