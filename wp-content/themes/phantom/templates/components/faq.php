<?php
/**
 * FAQ — accessible disclosure list.
 *
 * Expected data: title, items (question, answer).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-faq">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="phantom-faq__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<div class="phantom-faq__list">
		<?php foreach ( (array) $view->prop( 'items', array() ) as $index => $item ) : ?>
			<details class="phantom-faq__item" <?php echo 0 === $index ? 'open' : ''; ?>>
				<summary class="phantom-faq__question"><?php echo $view->e( $item['question'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></summary>
				<div class="phantom-faq__answer"><?php echo $view->e( $item['answer'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></div>
			</details>
		<?php endforeach; ?>
	</div>
</section>
