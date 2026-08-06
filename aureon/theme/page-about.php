<?php
/**
 * Template Name: AETHER About
 * Template Post Type: page
 *
 * AETHER-styled About page.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$aether = get_template_directory_uri() . '/assets/aether';
?>

<section class="about-page" id="aboutPage">
	<!-- Hero -->
	<div class="about-hero">
		<div class="container">
			<div class="about-hero-content" data-reveal>
				<span class="section-label" data-motion-text="words">Our Story</span>
				<h1 class="about-hero-title" data-motion-text="words">Born From The Silence Between Stars</h1>
				<p class="about-hero-text">We don't make shoes. We engineer the future of human movement.</p>
			</div>
		</div>
	</div>

	<!-- Mission -->
	<div class="container">
		<div class="about-mission" data-reveal>
			<div class="about-mission-text">
				<span class="section-label">Our Mission</span>
				<h2 class="about-section-title">Performance Without Compromise</h2>
				<p>AETHER was founded on a single belief: the human body is capable of extraordinary things when given the right tools. Every shoe we create is a precision instrument, designed to amplify natural biomechanics and push the boundaries of what's possible.</p>
				<p>From aerospace-grade carbon fiber to adaptive cushioning that learns your stride, we engineer at the intersection of science and art.</p>
			</div>
			<div class="about-mission-image" data-reveal>
				<img src="<?php echo esc_url( $aether ); ?>/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg" alt="AETHER engineering" loading="lazy">
			</div>
		</div>
	</div>

	<!-- Values -->
	<div class="about-values">
		<div class="container">
			<div class="section-header">
				<span class="section-label" data-motion-text="words">Our Values</span>
				<h2 class="section-title" data-motion-text="words">What Drives Us</h2>
			</div>
			<div class="about-values-grid" data-reveal-group>
				<div class="about-value-card" data-reveal-item>
					<div class="about-value-icon"><i class="fas fa-atom"></i></div>
					<h3>Relentless Innovation</h3>
					<p>We spend 18 months on every product. We test 47 compounds before selecting one. Good enough is never enough.</p>
				</div>
				<div class="about-value-card" data-reveal-item>
					<div class="about-value-icon"><i class="fas fa-leaf"></i></div>
					<h3>Sustainability First</h3>
					<p>75% recycled materials. 100% recyclable packaging. 100% carbon offset. We don't compromise the planet for performance.</p>
				</div>
				<div class="about-value-card" data-reveal-item>
					<div class="about-value-icon"><i class="fas fa-users"></i></div>
					<h3>Athlete-Centered</h3>
					<p>Every decision starts and ends with the athlete. Our team includes former Olympians, biomechanics PhDs, and material scientists.</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Stats -->
	<div class="about-stats">
		<div class="container">
			<div class="about-stats-grid" data-reveal-group>
				<div class="about-stat" data-reveal-item>
					<span class="about-stat-number">40%</span>
					<span class="about-stat-label">More Energy Return</span>
				</div>
				<div class="about-stat" data-reveal-item>
					<span class="about-stat-number">280g</span>
					<span class="about-stat-label">Ultra-Light Weight</span>
				</div>
				<div class="about-stat" data-reveal-item>
					<span class="about-stat-number">75%</span>
					<span class="about-stat-label">Recycled Materials</span>
				</div>
				<div class="about-stat" data-reveal-item>
					<span class="about-stat-number">2yr</span>
					<span class="about-stat-label">Performance Warranty</span>
				</div>
			</div>
		</div>
	</div>

	<!-- CTA -->
	<div class="container">
		<div class="about-cta" data-reveal>
			<h2>Ready to Experience the Difference?</h2>
			<p>Step into the void and discover what your body is truly capable of.</p>
			<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>" class="btn btn-primary btn-lg">Shop Collection</a>
		</div>
	</div>
</section>

<?php
get_footer();
