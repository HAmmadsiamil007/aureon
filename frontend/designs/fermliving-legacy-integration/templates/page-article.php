<?php
/**
 * Article (Single Post) Template — Ferm Living
 *
 * Displays a single blog article with hero image, metadata, and rich content.
 * Maps frozen source: blogs/stories/meet-our-design-studio.html
 *
 * @package Aureon\Designs\FermLiving
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );

$page_data = apply_filters( 'aether_adapter_article_data', array() );

$article_title    = get_the_title();
$article_content  = get_the_content();
$article_date     = get_the_date( 'F Y' );
$article_image    = get_the_post_thumbnail_url( get_the_ID(), 'full' );
$article_excerpt  = get_the_excerpt();
$categories       = get_the_category();
$primary_category = ! empty( $categories ) ? $categories[0]->name : '';
$author_name      = get_the_author();
$permalink        = get_the_permalink();
?>

<main class="content" id="main-content">
	<div class="headspace">

		<article
			class="article"
			data-section-type="article"
			data-component="article"
		>
			<div class="limit">
				<a
					href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>"
					class="mt-2 mb-4 flex items-center gap-2 hover:underline tab_l:gap-4"
				>
					<svg
						version="1.1"
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 370.814 370.814"
						class="w-3 h-3"
					>
						<g>
							<polygon points="292.92,24.848 268.781,0 77.895,185.401 268.781,370.814 292.92,345.961 127.638,185.401"/>
						</g>
					</svg>
					All stories
				</a>
			</div>

			<div class="tab_l:limit relative">
				<?php if ( $article_image ) : ?>
				<div
					class="group relative w-full relative h-[260px] tab_l:h-[1012px]"
					data-component="media"
				>
					<img
						src="<?php echo esc_url( $article_image ); ?>"
						alt="<?php echo esc_attr( $article_title ); ?>"
						class="absolute left-0 top-0 h-full w-full object-cover"
						loading="lazy"
					>
				</div>
				<?php endif; ?>

				<div class="relative mx-auto -mt-[58px] w-[85%] max-w-[1197px] bg-cream p-[30px] text-center tab_l:absolute tab_l:-top-[58px] tab_l:left-[50%] tab_l:m-0 tab_l:w-[75%] tab_l:-translate-x-[50%] tab_l:p-[50px]">
					<?php if ( $primary_category ) : ?>
					<p class="m-0 text-xs font-[11px] uppercase leading-[11px]">
						<?php echo esc_html( $primary_category ); ?>
					</p>
					<?php endif; ?>
					<h1 class="mb-[30px] mt-2.5">
						<?php echo esc_html( $article_title ); ?>
					</h1>
				</div>
			</div>

			<div class="limit relative mt-[96px]">
				<div class="limit mx-auto text-center tab_l:absolute tab_l:left-0 tab_l:top-0">
					<div class="tagline text-xs uppercase leading-[18px]">
						<?php echo esc_html( $article_date ); ?>
					</div>
					<div class="mb-5 mt-6 flex justify-center gap-4">
						<a
							href="https://pinterest.com/pin/create/link/?url=<?php echo urlencode( $permalink ); ?>&description=<?php echo urlencode( $article_title ); ?>"
							target="_blank"
							rel="noopener noreferrer"
						>
							<svg height="24px" viewBox="0 0 18 24" width="18px" xmlns="http://www.w3.org/2000/svg">
								<g fill-rule="evenodd" fill="none" stroke-width="1" stroke="none">
									<g fill="#221E20" transform="translate(-4.000000, -6.000000)">
										<g transform="translate(0.000000, 5.000000)">
											<path d="M7.86038145,22.9739751 C7.92637896,23.537219 8.04706477,24.3930356 8.04898766,24.4109282 C8.96728183,23.2771726 10.1976057,21.012073 10.5724389,19.6192812 C10.7493448,18.9620764 11.4495376,16.2723441 11.4495376,16.2723441 C11.9086195,17.1478784 13.2499169,17.8890385 14.6765386,17.8890385 C18.9230625,17.8890385 21.982609,13.9839417 21.982609,9.1316096 C21.982609,4.35000075 18.838716,1 13.3027801,1 C7.22690318,1 4,5.07871098 4,9.52009861 C4,11.5851524 5.09933918,14.1559915 6.8579038,14.9745235 C7.12463149,15.0985662 7.26728388,15.0439757 7.32871859,14.7862106 C7.37535683,14.5905321 7.61288266,13.634269 7.71978232,13.1894947 C7.75400325,13.0472986 7.73708834,12.925081 7.62210602,12.7857203 C7.0404806,12.0803455 6.57432637,10.7827205 6.57432637,9.57263588 C6.57432637,6.46729848 8.92576043,3.46253801 12.9317601,3.46253801 C16.3905182,3.46253801 18.8128385,5.8195126 18.8128385,9.19056737 C18.8128385,12.9993567 16.889329,15.6379206 14.3868666,15.6379206 C13.0046995,15.6379206 11.9702498,14.4951372 12.301932,13.0937739 C12.6988948,11.4199468 13.4680509,9.61392914 13.4680509,8.40596296 C13.4680509,7.32451644 12.8877292,6.42264831 11.6865746,6.42264831 C10.2737065,6.42264831 9.13897311,7.88424046 9.13897311,9.8419383 C9.13897311,11.0889163 9.56018385,11.9323156 9.56018385,11.9323156 C9.56018385,11.9323156 8.16494772,17.831971 7.90920331,18.9307561 C7.63657658,20.1016985 7.73001601,21.8614039 7.86038145,22.9739751" id="PinterestIconFillSVG"></path>
										</g>
									</g>
								</g>
							</svg>
						</a>
						<a
							href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( $permalink ); ?>"
							target="_blank"
							rel="noopener noreferrer"
						>
							<svg height="23px" viewBox="0 0 12 23" width="12px" xmlns="http://www.w3.org/2000/svg">
								<g fill-rule="evenodd" fill="none" stroke-width="1" stroke="none">
									<g fill="#221E20" transform="translate(-47.000000, -6.000000)">
										<g transform="translate(41.000000, 5.000000)">
											<path d="M17.5,4.67971396 L15.4251797,4.68067275 C13.7881666,4.68067275 13.4711668,5.45861052 13.4711668,6.60016918 L13.4711668,9.11747081 L17.3849053,9.11747081 L16.8752688,13.0698006 L13.4711668,13.0698006 L13.4711668,23.1673313 L9.39135804,23.1673313 L9.39135804,13.0698006 L6,13.0698006 L6,9.11747081 L9.39135804,9.11747081 L9.39135804,6.20890319 C9.39135804,2.83636139 11.4509576,1 14.4593991,1 C15.9004196,1 17.1389758,1.10726457 17.5,1.15520404 L17.5,4.67971396 Z" id="FacebookIconFillSVG"></path>
										</g>
									</g>
								</g>
							</svg>
						</a>
						<a
							href="https://twitter.com/intent/tweet?url=<?php echo urlencode( $permalink ); ?>"
							target="_blank"
							rel="noopener noreferrer"
						>
							<svg height="18px" viewBox="0 0 22 18" width="22px" xmlns="http://www.w3.org/2000/svg">
								<defs>
									<polygon id="path-twitter" points="8.8e-06 0 22 0 22 17.876144 8.8e-06 17.876144"></polygon>
								</defs>
								<g fill-rule="evenodd" fill="none" stroke-width="1" stroke="none">
									<g transform="translate(-84.000000, -8.000000)">
										<g transform="translate(82.000000, 5.000000)">
											<g transform="translate(2.000000, 3.000000)">
												<mask fill="#fffefa" id="mask-twitter">
													<use xlink:href="#path-twitter"></use>
												</mask>
												<path d="M6.9185688,17.876144 C15.2204888,17.876144 19.7612008,10.998064 19.7612008,5.033424 C19.7612008,4.838064 19.7612008,4.643584 19.7480888,4.449984 C20.6314328,3.811016 21.3939528,3.019896 22.0000088,2.113584 C21.1762408,2.478608 20.3023128,2.717968 19.4075288,2.823744 C20.3497448,2.259664 21.0550648,1.372448 21.3919288,0.327184 C20.5058568,0.852984 19.5366248,1.223464 18.5256808,1.422784 C16.8163688,-0.394856 13.9570728,-0.48268 12.1394328,1.226808 C10.9671848,2.329184 10.4698088,3.971968 10.8336888,5.539424 C7.2044808,5.357528 3.8231688,3.643288 1.5312088,0.823504 C0.3331768,2.885872 0.9451288,5.524376 2.9286488,6.848864 C2.2103048,6.827568 1.5077128,6.633792 0.8800088,6.283904 L0.8800088,6.341104 C0.8806248,8.489712 2.3951048,10.340264 4.5012088,10.765744 C3.8367208,10.947024 3.1394968,10.973512 2.4631288,10.843184 C3.0544008,12.681944 4.7490168,13.941488 6.6800888,13.977744 C5.0817448,15.233944 3.1072888,15.915856 1.0744888,15.913744 C0.7153608,15.91304 0.3565848,15.891304 8.8e-06,15.848624 C2.0641368,17.173288 4.4659208,17.87588 6.9185688,17.872624" fill="#221E20" id="TwitterIconFillSVG" mask="url(#mask-twitter)"></path>
											</g>
										</g>
									</g>
								</g>
							</svg>
						</a>
					</div>
				</div>

				<div class="article__rte mx-auto max-w-[1499px]">
					<?php
					if ( $article_content ) {
						the_content();
					}
					?>
				</div>
			</div>
		</article>

	</div>
</main>

<?php get_footer( 'shop' ); ?>
