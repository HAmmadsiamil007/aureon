<?php
/**
 * ArchiveHeader — archive/collection title block (wraps PageHeader content).
 *
 * Expected data: title, description.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<header class="phantom-archive-header">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h1 class="phantom-archive-header__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h1>
	<?php endif; ?>
	<?php if ( $view->prop( 'description' ) ) : ?>
		<p class="phantom-archive-header__description"><?php echo $view->e( $view->prop( 'description' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
</header>
