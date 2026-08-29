<?php
/**
 * About Page Template — Ferm Living
 *
 * Displays the about page with hero-with-cta and title-column-text sections.
 * Maps frozen source: pages/about-ferm-living.html
 *
 * @package Aureon\Designs\FermLiving
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );

$page_id   = get_the_ID();
$page_data = apply_filters( 'aether_adapter_about_data', array() );

$hero_title    = isset( $page_data['hero_title'] ) ? $page_data['hero_title'] : get_the_title();
$hero_subtitle = isset( $page_data['hero_subtitle'] ) ? $page_data['hero_subtitle'] : '';
$hero_image    = isset( $page_data['hero_image'] ) ? $page_data['hero_image'] : '';
$hero_cta      = isset( $page_data['hero_cta'] ) ? $page_data['hero_cta'] : array();

$intro_title    = isset( $page_data['intro_title'] ) ? $page_data['intro_title'] : '';
$intro_content  = isset( $page_data['intro_content'] ) ? $page_data['intro_content'] : '';
?>

<main class="content" id="main-content">
	<div class="headspace">

		<?php if ( $hero_image ) : ?>
		<div class="about-hero limit mb-8 mt-8 tab_p:mb-20 tab_p:mt-14">
			<div class="relative aspect-[16/7] overflow-hidden">
				<img
					src="<?php echo esc_url( $hero_image ); ?>"
					alt="<?php echo esc_attr( $hero_title ); ?>"
					class="absolute left-0 top-0 h-full w-full object-cover"
					loading="lazy"
				>
				<?php if ( $hero_title || $hero_subtitle || ! empty( $hero_cta ) ) : ?>
				<div class="absolute inset-0 flex items-center justify-center">
					<div class="text-center text-cream">
						<?php if ( $hero_subtitle ) : ?>
						<p class="mb-4 text-xs uppercase"><?php echo esc_html( $hero_subtitle ); ?></p>
						<?php endif; ?>
						<?php if ( $hero_title ) : ?>
						<h2 class="mb-6 font-primary text-4xl font-medium tab_p:text-6xl">
							<?php echo esc_html( $hero_title ); ?>
						</h2>
						<?php endif; ?>
						<?php if ( ! empty( $hero_cta['url'] ) && ! empty( $hero_cta['label'] ) ) : ?>
						<a
							href="<?php echo esc_url( $hero_cta['url'] ); ?>"
							class="inline-block border border-cream bg-transparent px-8 py-3 text-sm font-medium text-cream transition-colors hover:bg-cream hover:text-black"
						>
							<?php echo esc_html( $hero_cta['label'] ); ?>
						</a>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>

		<?php if ( $intro_title || $intro_content ) : ?>
		<div class="section-title-column-text limit mb-8 mt-8 block tab_p:grid-12 tab_p:mb-20 tab_p:mt-14">
			<div class="relative col-end-[span_4]">
				<h1 class="sticky top-[100px] mb-4 mt-0 hyphens-auto break-words font-primary text-2xl text-[32px] font-medium leading-[1.15] tab_p:mb-0 tab_p:text-[80px]">
					<?php echo esc_html( $intro_title ? $intro_title : get_the_title() ); ?>
				</h1>
			</div>
			<div class="title-column-text__right col-start-7 col-end-[span_6] flex flex-col gap-8 tab_p:gap-10 [&_a]:underline [&_h1]:my-8 [&_strong]:font-medium">
				<div>
					<?php echo wp_kses_post( $intro_content ); ?>
				</div>
			</div>
		</div>
		<?php else : ?>
		<div class="section-title-column-text limit mb-8 mt-8 block tab_p:grid-12 tab_p:mb-20 tab_p:mt-14">
			<div class="relative col-end-[span_4]">
				<h1 class="sticky top-[100px] mb-4 mt-0 hyphens-auto break-words font-primary text-2xl text-[32px] font-medium leading-[1.15] tab_p:mb-0 tab_p:text-[80px]">
					<?php echo esc_html( get_the_title() ); ?>
				</h1>
			</div>
			<div class="title-column-text__right col-start-7 col-end-[span_6] flex flex-col gap-8 tab_p:gap-10 [&_a]:underline [&_h1]:my-8 [&_strong]:font-medium">
				<div>
					<?php
					$content = get_the_content();
					if ( $content ) {
						the_content();
					}
					?>
				</div>
			</div>
		</div>
		<?php endif; ?>

	</div>
</main>

<?php get_footer( 'shop' ); ?>
