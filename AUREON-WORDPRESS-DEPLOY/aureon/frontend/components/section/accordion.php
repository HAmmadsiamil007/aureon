<?php
/**
 * Accordion item — single expandable question/answer pair.
 *
 * Key:    'section/accordion'
 * Source: faq.html `.faq-accordion`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $question  Question text. Default ''.`
 * - `string $answer    Answer text. Default ''.`
 * - `bool $open       Expanded by default. Default false.`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$question = isset( $componentData['question'] ) ? $componentData['question'] : '';
$answer   = isset( $componentData['answer'] ) ? $componentData['answer'] : '';
$open     = ! empty( $componentData['open'] );

if ( ! $question ) {
	return;
}
?>
<div class="faq-item<?php echo $open ? ' active' : ''; ?>">
	<button class="faq-question" <?php echo $open ? 'aria-expanded="true"' : 'aria-expanded="false"'; ?>>
		<span><?php echo esc_html( $question ); ?></span>
		<i class="fas <?php echo $open ? 'fa-minus' : 'fa-plus'; ?>"></i>
	</button>
	<div class="faq-answer">
		<p><?php echo esc_html( $answer ); ?></p>
	</div>
</div>
