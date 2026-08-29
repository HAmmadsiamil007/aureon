<?php
/**
 * Contact Page Template — Ferm Living
 *
 * Displays the contact page with title-column-text + FAQ accordion.
 * Maps frozen source: pages/contact.html
 *
 * @package Aureon\Designs\FermLiving
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );

$page_id   = get_the_ID();
$page_data = apply_filters( 'aether_adapter_contact_data', array() );

$contact_title   = isset( $page_data['title'] ) ? $page_data['title'] : get_the_title();
$contact_content = isset( $page_data['content'] ) ? $page_data['content'] : '';
$faq_items       = isset( $page_data['faq'] ) ? $page_data['faq'] : array();

// Fallback: get FAQ from page meta if not from adapter
if ( empty( $faq_items ) ) {
	$faq_items = get_post_meta( $page_id, '_ferm_faq_items', true );
	if ( is_string( $faq_items ) ) {
		$faq_items = json_decode( $faq_items, true );
	}
}
if ( ! is_array( $faq_items ) ) {
	$faq_items = array();
}
?>

<main class="content" id="main-content">
	<div class="headspace">

		<div class="section-title-column-text limit mb-8 mt-8 block tab_p:grid-12 tab_p:mb-20 tab_p:mt-14">
			<div class="relative col-end-[span_4]">
				<h1 class="sticky top-[100px] mb-4 mt-0 hyphens-auto break-words font-primary text-2xl text-[32px] font-medium leading-[1.15] tab_p:mb-0 tab_p:text-[80px]">
					<?php echo esc_html( $contact_title ); ?>
				</h1>
			</div>
			<div class="title-column-text__right col-start-7 col-end-[span_6] flex flex-col gap-8 tab_p:gap-10 [&_a]:underline [&_h1]:my-8 [&_strong]:font-medium">
				<div>
					<?php
					if ( $contact_content ) {
						echo wp_kses_post( $contact_content );
					} else {
						the_content();
					}
					?>
				</div>
			</div>
		</div>

		<?php if ( ! empty( $faq_items ) ) : ?>
		<div class="limit mb-8 mt-8 block tab_p:grid-12 tab_p:mb-20 tab_p:mt-14">
			<div class="relative col-end-[span_4]">
				<h1 class="sticky top-[100px] mb-4 mt-0 hyphens-auto break-words font-primary text-2xl text-[32px] font-medium leading-[36px] tab_p:mb-0 tab_p:text-[80px] tab_p:leading-[72px]">
				</h1>
			</div>

			<div
				class="col-start-7 col-end-[span_6] flex flex-col gap-8 tab_p:gap-10"
				data-component="faqAccordion"
			>
				<div class="space-y-0">
					<?php foreach ( $faq_items as $index => $faq ) :
						$faq_id     = isset( $faq['id'] ) ? $faq['id'] : 'faq-' . $index;
						$faq_title  = isset( $faq['title'] ) ? $faq['title'] : '';
						$faq_answer = isset( $faq['answer'] ) ? $faq['answer'] : '';
					?>
					<div class="border-b border-black-05 pb-4 pt-6">
						<button
							class="flex w-full items-center justify-between pb-2 text-left font-secondary text-lg font-medium focus-visible:outline focus-visible:outline-2 focus-visible:outline-black"
							data-faq-question="<?php echo esc_attr( $faq_id ); ?>"
							aria-expanded="false"
							aria-controls="faq-answer-<?php echo esc_attr( $faq_id ); ?>"
						>
							<?php echo esc_html( $faq_title ); ?>

							<span class="mx-5" data-plus>
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<path d="M17.5 10C17.5 10.1658 17.4342 10.3247 17.3169 10.4419C17.1997 10.5592 17.0408 10.625 16.875 10.625H10.625V16.875C10.625 17.0408 10.5592 17.1997 10.4419 17.3169C10.3247 17.4342 10.1658 17.5 10 17.5C9.83424 17.5 9.67527 17.4342 9.55806 17.3169C9.44085 17.1997 9.375 17.0408 9.375 16.875V10.625H3.125C2.95924 10.625 2.80027 10.5592 2.68306 10.4419C2.56585 10.3247 2.5 10.1658 2.5 10C2.5 9.83424 2.56585 9.67527 2.68306 9.55806C2.80027 9.44085 2.95924 9.375 3.125 9.375H9.375V3.125C9.375 2.95924 9.44085 2.80027 9.55806 2.68306C9.67527 2.56585 9.83424 2.5 10 2.5C10.1658 2.5 10.3247 2.56585 10.4419 2.68306C10.5592 2.80027 10.625 2.95924 10.625 3.125V9.375H16.875C17.0408 9.375 17.1997 9.44085 17.3169 9.55806C17.4342 9.67527 17.5 9.83424 17.5 10Z" fill="currentColor"/>
								</svg>
							</span>
							<span class="mx-5" data-minus style="display:none">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<path d="M17.5 10C17.5 10.1658 17.4342 10.3247 17.3169 10.4419C17.1997 10.5592 17.0408 10.625 16.875 10.625H3.125C2.95924 10.625 2.80027 10.5592 2.68306 10.4419C2.56585 10.3247 2.5 10.1658 2.5 10C2.5 9.83424 2.56585 9.67527 2.68306 9.55806C2.80027 9.44085 2.95924 9.375 3.125 9.375H16.875C17.0408 9.375 17.1997 9.44085 17.3169 9.55806C17.4342 9.67527 17.5 9.83424 17.5 10Z" fill="currentColor"/>
								</svg>
							</span>
						</button>

						<div
							class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out"
							data-faq-answer="<?php echo esc_attr( $faq_id ); ?>"
							id="faq-answer-<?php echo esc_attr( $faq_id ); ?>"
							aria-hidden="true"
						>
							<div class="faq-answer my-2 [&>p]:my-2 [&>p]:text-base [&>p]:tab_l:text-sm [&>p_a]:!underline">
								<?php echo wp_kses_post( $faq_answer ); ?>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php endif; ?>

	</div>
</main>

<?php get_footer( 'shop' ); ?>
