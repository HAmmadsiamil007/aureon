<?php
/**
 * Template Name: AETHER Coming Soon
 * Template Post Type: page
 *
 * AETHER-styled Coming Soon / Launch page.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="coming-soon" id="comingSoon">
	<div class="coming-soon-bg" aria-hidden="true">
		<div id="foglayer_01" class="fog">
			<div class="image01"></div>
			<div class="image02"></div>
		</div>
		<div id="foglayer_02" class="fog">
			<div class="image01"></div>
			<div class="image02"></div>
		</div>
	</div>
	<div class="container">
		<div class="coming-soon-content" data-reveal>
			<span class="section-label" data-motion-text="words">Coming Soon</span>
			<h1 class="coming-soon-title" data-motion-text="words">Something Extraordinary Is On The Way</h1>
			<p class="coming-soon-text">We're crafting the next evolution of performance footwear. Be the first to experience it.</p>

			<!-- Countdown -->
			<div class="coming-soon-countdown" id="countdown">
				<div class="countdown-item">
					<span class="countdown-number" id="days">00</span>
					<span class="countdown-label">Days</span>
				</div>
				<div class="countdown-item">
					<span class="countdown-number" id="hours">00</span>
					<span class="countdown-label">Hours</span>
				</div>
				<div class="countdown-item">
					<span class="countdown-number" id="minutes">00</span>
					<span class="countdown-label">Minutes</span>
				</div>
				<div class="countdown-item">
					<span class="countdown-number" id="seconds">00</span>
					<span class="countdown-label">Seconds</span>
				</div>
			</div>

			<!-- Newsletter Signup -->
			<div class="coming-soon-signup">
				<form class="newsletter-form" id="comingSoonForm">
					<div class="newsletter-input-wrap">
						<input type="email" placeholder="Enter your email for early access" required class="newsletter-input" aria-label="Email address">
						<button type="submit" class="newsletter-btn">
							<span class="newsletter-btn-text">Get Notified</span>
							<i class="fas fa-arrow-right newsletter-btn-icon"></i>
						</button>
					</div>
					<p class="newsletter-note"><i class="fas fa-lock"></i> We respect your privacy. No spam, ever.</p>
					<input type="hidden" name="aether_nonce" value="<?php echo esc_attr( wp_create_nonce( 'aether_nonce' ) ); ?>">
				</form>
				<div class="newsletter-success" id="comingSoonSuccess">
					<i class="fas fa-check-circle"></i>
					<p>You're on the list. We'll be in touch.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
// Hide footer on coming soon page.
