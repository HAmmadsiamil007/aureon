<?php
/**
 * Template Name: AETHER Contact
 * Template Post Type: page
 *
 * AETHER-styled Contact page.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="contact-page" id="contactPage">
	<div class="container">
		<div class="section-header">
			<span class="section-label" data-motion-text="words">Get In Touch</span>
			<h1 class="section-title" data-motion-text="words">Contact Us</h1>
		</div>

		<div class="contact-layout">
			<!-- Contact Form -->
			<div class="contact-form-wrap" data-reveal>
				<h2 class="contact-form-title">Send Us a Message</h2>
				<form class="contact-form" id="contactForm">
					<div class="form-row form-row-split">
						<div>
							<label class="checkout-label" for="contact_name">Name *</label>
							<input type="text" class="input-text" name="name" id="contact_name" placeholder="Your name" required>
						</div>
						<div>
							<label class="checkout-label" for="contact_email">Email *</label>
							<input type="email" class="input-text" name="email" id="contact_email" placeholder="your@email.com" required>
						</div>
					</div>
					<div class="form-row">
						<label class="checkout-label" for="contact_subject">Subject</label>
						<input type="text" class="input-text" name="subject" id="contact_subject" placeholder="How can we help?">
					</div>
					<div class="form-row">
						<label class="checkout-label" for="contact_message">Message *</label>
						<textarea class="input-text" name="message" id="contact_message" rows="6" placeholder="Tell us what's on your mind..." required></textarea>
					</div>
					<button type="submit" class="btn btn-primary">Send Message <i class="fas fa-arrow-right"></i></button>
				</form>
				<div class="contact-success" id="contactSuccess">
					<i class="fas fa-check-circle"></i>
					<h3>Message Sent!</h3>
					<p>We'll get back to you within 24 hours.</p>
				</div>
			</div>

			<!-- Contact Info -->
			<div class="contact-info" data-reveal>
				<div class="contact-info-card">
					<i class="fas fa-envelope"></i>
					<h3>Email</h3>
					<a href="mailto:hello@aether.com">hello@aether.com</a>
				</div>
				<div class="contact-info-card">
					<i class="fas fa-map-marker-alt"></i>
					<h3>Location</h3>
					<p>San Francisco, CA</p>
				</div>
				<div class="contact-info-card">
					<i class="fas fa-clock"></i>
					<h3>Response Time</h3>
					<p>Within 24 hours</p>
				</div>
				<div class="contact-social">
					<h3>Follow Us</h3>
					<div class="footer-social">
						<a href="<?php echo esc_url( get_theme_mod( 'aether_social_instagram', '#' ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
						<a href="<?php echo esc_url( get_theme_mod( 'aether_social_twitter', '#' ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
						<a href="<?php echo esc_url( get_theme_mod( 'aether_social_tiktok', '#' ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
						<a href="<?php echo esc_url( get_theme_mod( 'aether_social_youtube', '#' ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
