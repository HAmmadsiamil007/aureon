<?php
/**
 * Filters — collapsible filtering panel driven by ViewModel data.
 *
 * Expected data: title, groups (label + options), submit_label, reset_label.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-filters" aria-label="<?php echo $view->attr( $view->prop( 'title', 'Filters' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
	<div class="phantom-filters__header">
		<h3 class="phantom-filters__title"><?php echo $view->e( $view->prop( 'title', 'Filters' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h3>
	</div>
	<form class="phantom-filters__form" method="get">
		<?php foreach ( (array) $view->prop( 'groups', array() ) as $group ) : ?>
			<fieldset class="phantom-filters__group">
				<legend class="phantom-filters__group-label"><?php echo $view->e( $group['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></legend>
				<?php foreach ( (array) ( $group['options'] ?? array() ) as $option ) : ?>
					<label class="phantom-filters__option">
						<input type="checkbox" name="<?php echo $view->attr( $option['name'] ?? 'filter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" value="<?php echo $view->attr( $option['value'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" />
						<span><?php echo $view->e( $option['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					</label>
				<?php endforeach; ?>
			</fieldset>
		<?php endforeach; ?>
		<div class="phantom-filters__actions">
			<button type="submit" class="phantom-btn phantom-btn--primary"><?php echo $view->e( $view->prop( 'submit_label', 'Apply' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></button>
			<button type="reset" class="phantom-btn phantom-btn--ghost"><?php echo $view->e( $view->prop( 'reset_label', 'Reset' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></button>
		</div>
	</form>
</section>
