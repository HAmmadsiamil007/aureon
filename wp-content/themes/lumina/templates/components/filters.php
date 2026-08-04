<?php
/**
 * Filters — collapsible filtering panel driven by ViewModel data.
 *
 * Expected data: title, groups (label + options), submit_label, reset_label.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<section class="lumina-filters" aria-label="<?php echo $view->attr( $view->prop( 'title', 'Filters' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
	<div class="lumina-filters__header">
		<h3 class="lumina-filters__title"><?php echo $view->e( $view->prop( 'title', 'Filters' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h3>
	</div>
	<form class="lumina-filters__form" method="get">
		<?php foreach ( (array) $view->prop( 'groups', array() ) as $group ) : ?>
			<fieldset class="lumina-filters__group">
				<legend class="lumina-filters__group-label"><?php echo $view->e( $group['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></legend>
				<?php foreach ( (array) ( $group['options'] ?? array() ) as $option ) : ?>
					<label class="lumina-filters__option">
						<input type="checkbox" name="<?php echo $view->attr( $option['name'] ?? 'filter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" value="<?php echo $view->attr( $option['value'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" />
						<span><?php echo $view->e( $option['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					</label>
				<?php endforeach; ?>
			</fieldset>
		<?php endforeach; ?>
		<div class="lumina-filters__actions">
			<button type="submit" class="lumina-btn lumina-btn--primary"><?php echo $view->e( $view->prop( 'submit_label', 'Apply' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></button>
			<button type="reset" class="lumina-btn lumina-btn--ghost"><?php echo $view->e( $view->prop( 'reset_label', 'Reset' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></button>
		</div>
	</form>
</section>
