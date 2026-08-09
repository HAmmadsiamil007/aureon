<?php
/**
 * AETHER Frontend design tokens.
 *
 * Maps the AETHER palette + layout scale to Aureon option defaults
 * so everything is Customizer-driven. Never hardcode values in CSS.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_filter( 'aureon_option_defaults', 'aether_frontend_defaults' );
/**
 * Register frontend framework defaults on the theme settings bucket.
 *
 * @param array $defaults Existing defaults.
 * @return array Merged defaults.
 */
function aether_frontend_defaults( $defaults ) {
	return array_merge( $defaults, array(
		// Motion toggles.
		'aether_motion_enabled'      => true,
		'aether_motion_reveal'       => true,
		'aether_motion_tilt'         => true,
		'aether_motion_parallax'     => true,
		'aether_motion_text'         => true,
		// Shell toggles.
		'aether_preloader_enabled'   => true,
		'aether_fog_enabled'         => true,
		'aether_announcement_enabled' => true,
		'aether_announcement_text'   => __( 'Free shipping on orders over $150 — Step into the void.', 'aureon' ),
		'aether_announcement_url'    => '',
		'aether_announcement_items'  => array(
			array(
				'icon' => '',
				'text' => __( 'Worldwide Shipping', 'aureon' ),
			),
			array(
				'icon' => '',
				'text' => __( 'Join the Chronicle for 10% Off Your First Order', 'aureon' ),
			),
			array(
				'icon' => '',
				'text' => __( '30-Day Free Returns', 'aureon' ),
			),
			array(
				'icon' => '',
				'text' => __( 'Complimentary Gift Wrapping', 'aureon' ),
			),
		),
		'aether_newsletter_enabled'  => true,
		'aether_newsletter_text'     => __( 'Stay Connected', 'aureon' ),
		'aether_newsletter_subtitle' => __( 'Get 10% off your first order. No spam, ever.', 'aureon' ),
		// Editable shell copy (defaults = current premium design strings).
		'aether_categories_label'    => __( 'Shop by Category', 'aureon' ),
		'aether_categories_title'    => __( 'Find Your Fit', 'aureon' ),
		'aether_categories_subtitle' => '',
		'aether_contact_address'     => array(
			__( '123 Innovation Drive', 'aureon' ),
			__( 'San Francisco, CA 94102', 'aureon' ),
		),
		'aether_contact_hours'       => __( 'Mon—Fri 9am—6pm PST', 'aureon' ),
		'aether_footer_columns'      => array(
			array(
				'heading' => 'Shop',
				'links'   => array(
					array( 'label' => 'Men', 'url' => '' ),
					array( 'label' => 'Women', 'url' => '' ),
					array( 'label' => 'Kids', 'url' => '' ),
					array( 'label' => 'New Arrivals', 'url' => '' ),
					array( 'label' => 'Bestsellers', 'url' => '' ),
				),
			),
			array(
				'heading' => 'Support',
				'links'   => array(
					array( 'label' => 'FAQ', 'url' => '' ),
					array( 'label' => 'Contact Us', 'url' => '' ),
					array( 'label' => 'Shipping Info', 'url' => '' ),
					array( 'label' => 'Returns & Exchanges', 'url' => '' ),
					array( 'label' => 'Size Guide', 'url' => '' ),
				),
			),
			array(
				'heading' => 'Company',
				'links'   => array(
					array( 'label' => 'About Us', 'url' => '' ),
					array( 'label' => 'Blog', 'url' => '' ),
					array( 'label' => 'Careers', 'url' => '' ),
					array( 'label' => 'Press', 'url' => '' ),
				),
			),
		),
		// Layout tokens.
		'aether_container_max'       => '1200px',
		'aether_section_padding'     => '100px 0',
		'aether_announcement_height' => '40px',
		'aether_header_height'       => '80px',
		'aether_grid_gap'            => '24px',
		'aether_shop_per_page'       => 9,
		// Radii.
		'aether_radius_sm'           => '8px',
		'aether_radius_md'           => '12px',
		'aether_radius_lg'           => '24px',
		'aether_radius_pill'         => '999px',
		// Google OAuth (server-side only — empty = feature hidden).
		'aether_google_client_id'    => '',
		'aether_google_client_secret' => '',
		// Front page sections visibility.
		'aether_section_hero'        => true,
		'aether_section_categories'  => true,
		'aether_section_bestsellers' => true,
		'aether_section_reviews'     => true,
		'aether_section_faq'         => true,
		'aether_section_newsletter'  => true,
		// Static page sections visibility (about / team / contact / accounts).
		'aether_section_mission'     => true,
		'aether_section_features'    => true,
		'aether_section_story'       => true,
		'aether_section_stats'       => true,
		'aether_section_team'        => true,
		'aether_section_values'      => true,
		'aether_section_contact'     => true,
		'aether_section_auth'        => true,
		'aether_section_wishlist'    => true,
		'aether_section_coming_soon' => true,
		// Hero slides (default = three static slides; paths resolve via content_url()).
		'aether_hero_slides'         => array(
			array(
				'label'    => 'Void Series',
				'title'    => 'Step into the void',
				'accent'   => 'Void Series',
				'subtitle' => 'Precision-cut garments engineered in the dark.',
				'image'    => 'frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg',
				'cta'      => 'Shop the collection',
				'url'      => '',
			),
			array(
				'label'    => 'Shadow Drop',
				'title'    => 'Forged in the dark',
				'accent'   => 'Shadow Drop',
				'subtitle' => 'Limited silhouettes, built for the bold.',
				'image'    => 'frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg',
				'cta'      => 'Explore the drop',
				'url'      => '',
			),
			array(
				'label'    => 'Midnight Run',
				'title'    => 'Engineered to move',
				'accent'   => 'Midnight Run',
				'subtitle' => 'Responsive comfort that disappears underfoot.',
				'image'    => 'frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg',
				'cta'      => 'Find your fit',
				'url'      => '',
			),
		),
		// Master switch for demo fallback content. TRUE keeps the store visually
		// populated before real products/CPTs exist (default, preserves the
		// current out-of-the-box appearance). Set FALSE in production to show
		// only real data — sections then render their graceful empty states.
		'aether_demo_content'        => true,
		// Demo content fallbacks — used ONLY when the store has no real
		// categories/products/CPT posts yet AND aether_demo_content is true.
		// Mirrors the source demo content so the design is pixel-visible
		// before content exists. Real data always wins.
		'aether_category_items'      => array(
			array(
				'name'     => 'Men',
				'count'    => '24 Products',
				'image'    => 'frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg',
				'url'      => '',
				'modifier' => 'large',
			),
			array(
				'name'     => 'Women',
				'count'    => '18 Products',
				'image'    => 'frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg',
				'url'      => '',
			),
			array(
				'name'     => 'Kids',
				'count'    => '12 Products',
				'image'    => 'frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg',
				'url'      => '',
			),
			array(
				'name'     => 'New Arrivals',
				'count'    => 'Just Dropped',
				'image'    => 'frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg',
				'url'      => '',
				'modifier' => 'accent',
			),
		),
		'aether_product_items'       => array(
			array(
				'name'     => 'AETHER Void Runner',
				'tagline'  => 'Carbon fiber · 40mm cushion',
				'price'    => '$449',
				'rating'   => 5,
				'reviews'  => 128,
				'badge'    => 'Bestseller',
				'image'    => 'frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg',
				'url'      => '',
			),
			array(
				'name'     => 'AETHER Cloud Stride',
				'tagline'  => 'Ultra-light · Zero gravity',
				'price'    => '$99',
				'rating'   => 4.5,
				'reviews'  => 86,
				'badge'    => 'New',
				'image'    => 'frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg',
				'url'      => '',
			),
			array(
				'name'     => 'AETHER Midnight Edition',
				'tagline'  => 'Stealth black · Reflective',
				'price'    => '$479',
				'rating'   => 5,
				'reviews'  => 64,
				'badge'    => 'Limited',
				'image'    => 'frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg',
				'url'      => '',
			),
			array(
				'name'     => 'AETHER Aero Sprint',
				'tagline'  => 'Race-ready · Featherweight',
				'price'    => '$69',
				'rating'   => 4,
				'reviews'  => 42,
				'badge'    => '',
				'image'    => 'frontend/assets/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg',
				'url'      => '',
			),
		),
		'aether_faq_items'           => array(
			array(
				'question' => 'What makes AETHER different?',
				'answer'   => 'AETHER combines carbon fiber plate technology with our proprietary Void Air cushioning system. Every shoe is engineered with aerospace-grade materials, delivering 40% more energy return than traditional running shoes while weighing only 280g.',
			),
			array(
				'question' => 'What is the return policy?',
				'answer'   => 'We offer a 30-day no-questions-asked return policy. If you are not satisfied with your AETHER shoes, return them in original condition for a full refund. We also cover return shipping on all orders.',
			),
			array(
				'question' => 'How do I find my size?',
				'answer'   => 'Each product page includes our interactive size guide. We recommend measuring your foot length and comparing it to our chart. Our adaptive lacing system accommodates a range of fits within each size.',
			),
			array(
				'question' => 'How long does shipping take?',
				'answer'   => 'Standard shipping is free on orders over $200 and takes 3-5 business days. Express shipping (1-2 days) is available for 5. International shipping is available to 40+ countries with delivery in 5-10 business days.',
			),
			array(
				'question' => 'Are AETHER shoes sustainable?',
				'answer'   => 'Sustainability is core to AETHER. Our upper mesh uses 75% recycled materials, our packaging is 100% recyclable, and we offset 100% of carbon emissions from shipping. We are working toward a fully circular product by 2027.',
			),
			array(
				'question' => 'Do you offer warranty?',
				'answer'   => 'Every AETHER shoe comes with a 2-year performance warranty. If the cushioning, sole, or upper fails under normal use within 24 months, we will replace them free of charge. No questions asked.',
			),
		),
		'aether_testimonial_items'   => array(
			array(
				'name'      => 'Marcus Chen',
				'role'      => 'Professional Runner',
				'verified'  => true,
				'stars'     => 5,
				'title'     => 'Game changer for marathon training',
				'quote'     => 'The carbon fiber plate gives insane energy return. I shaved 3 minutes off my half marathon time within the first month. The void cushioning makes it feel like you are running on clouds.',
				'date'      => '2 weeks ago',
			),
			array(
				'name'      => 'Sarah Kim',
				'role'      => 'Sneaker Collector',
				'verified'  => true,
				'stars'     => 5,
				'title'     => 'Premium feel from box to street',
				'quote'     => 'The design is unlike anything I have seen. The unboxing experience alone is worth it. These are functional art. Get compliments every time I wear them.',
				'date'      => '1 month ago',
			),
			array(
				'name'      => 'James Wright',
				'role'      => 'Marathon Coach',
				'verified'  => true,
				'stars'     => 5,
				'title'     => 'My athletes love these',
				'quote'     => 'I have put all my competitive runners in AETHER. The performance difference is measurable. Lightweight, responsive, and the adaptive fit system means zero hot spots during long runs.',
				'date'      => '3 weeks ago',
			),
			array(
				'name'      => 'Aisha Lawson',
				'role'      => 'Fitness Enthusiast',
				'verified'  => true,
				'stars'     => 4.5,
				'title'     => 'Best running shoe I have owned',
				'quote'     => 'From daily jogs to race day — these handle everything. The breathability is incredible and they still look brand new after 200+ miles. Worth every penny.',
				'date'      => '5 days ago',
			),
		),
		'aether_team_items'          => array(
			array(
				'name'  => 'Amara Voss',
				'role'  => 'Co-founder · Head of Biomechanics',
				'bio'   => 'Former Olympic performance analyst. Amara leads material research and is the architect of the Void Air cushioning system.',
				'image' => '',
			),
			array(
				'name'  => 'Kai Nakamura',
				'role'  => 'Co-founder · Lead Designer',
				'bio'   => 'KAi shapes every silhouette. His philosophy: strip the noise, keep the motion. Form follows stride.',
				'image' => '',
			),
			array(
				'name'  => 'Leila Haddad',
				'role'  => 'Head of Engineering',
				'bio'   => 'Leila leads the carbon fiber plate program and adaptive lacing systems, obsessed with gram-level precision.',
				'image' => '',
			),
			array(
				'name'  => 'Dmitri Petrov',
				'role'  => 'Performance Testing Lead',
				'bio'   => 'Dmitri has logged 10,000+ miles across every prototype, from treadmill labs to the streets of four continents.',
				'image' => '',
			),
		),
		'aether_reviews_score'       => 4.9,
		'aether_reviews_count'       => 312,
		// Single product (Stage 5) demo fallbacks — real WC data always wins.
		'aether_product_colors'      => array(
			array( 'name' => 'Obsidian', 'hex' => '#09090B' ),
			array( 'name' => 'Chrome',   'hex' => '#A8B5C0' ),
			array( 'name' => 'Gold',     'hex' => '#C8956C' ),
			array( 'name' => 'Phantom',  'hex' => '#2D3436' ),
		),
		'aether_product_sizes'       => array( 'US 7', 'US 7.5', 'US 8', 'US 8.5', 'US 9', 'US 9.5', 'US 10', 'US 10.5', 'US 11', 'US 11.5', 'US 12', 'US 13' ),
		'aether_size_table'          => array(
			array( 'us' => '7',   'eu' => '40',   'uk' => '6',   'cm' => '25.0' ),
			array( 'us' => '7.5', 'eu' => '40.5', 'uk' => '6.5', 'cm' => '25.5' ),
			array( 'us' => '8',   'eu' => '41',   'uk' => '7',   'cm' => '26.0' ),
			array( 'us' => '8.5', 'eu' => '42',   'uk' => '7.5', 'cm' => '26.5' ),
			array( 'us' => '9',   'eu' => '42.5', 'uk' => '8',   'cm' => '27.0' ),
			array( 'us' => '9.5', 'eu' => '43',   'uk' => '8.5', 'cm' => '27.5' ),
			array( 'us' => '10',  'eu' => '44',   'uk' => '9',   'cm' => '28.0' ),
			array( 'us' => '10.5','eu' => '44.5', 'uk' => '9.5', 'cm' => '28.5' ),
			array( 'us' => '11',  'eu' => '45',   'uk' => '10',  'cm' => '29.0' ),
			array( 'us' => '11.5','eu' => '45.5', 'uk' => '10.5','cm' => '29.5' ),
			array( 'us' => '12',  'eu' => '46',   'uk' => '11',  'cm' => '30.0' ),
			array( 'us' => '13',  'eu' => '48',   'uk' => '12',  'cm' => '31.0' ),
		),
		'aether_spec_items'          => array(
			array(
				'icon'  => 'fa-layer-group',
				'title' => 'Materials',
				'body'  => 'Engineered mesh upper with DWR water-resistant treatment. Full-length carbon fiber plate for explosive energy return. Void Air cushioning midsole (40mm stack height). High-abrasion rubber outsole with multi-directional traction pattern.',
			),
			array(
				'icon'  => 'fa-ruler-horizontal',
				'title' => 'Fit & Sizing',
				'body'  => 'True to size with medium width. Features an adaptive lacing system for micro-adjust fit throughout your run. Heel-to-toe drop: 8mm. Recommended for neutral runners and those who need mild stability.',
			),
			array(
				'icon'  => 'fa-tachometer-alt',
				'title' => 'Performance',
				'body'  => 'Weight: 245g (Men\'s US 10). Carbon fiber plate provides 15% more energy return vs traditional foam. Void Air cushioning maintains performance across temperatures. Breathability rating: 8/10.',
			),
			array(
				'icon'  => 'fa-spa',
				'title' => 'Care',
				'body'  => 'Spot clean with mild soap and lukewarm water. Air dry at room temperature — do not use direct heat. Do not machine wash or tumble dry. Remove insoles after wet runs. Store in a cool, dry place away from direct sunlight.',
			),
		),
		'aether_product_trust'       => array(
			array( 'icon' => 'fa-truck',      'label' => 'Free Shipping over $200' ),
			array( 'icon' => 'fa-undo',       'label' => '30-Day Free Returns' ),
			array( 'icon' => 'fa-shield-alt', 'label' => '2-Year Warranty' ),
		),
		'aether_product_score'       => 4.8,
		'aether_product_score_count' => 128,
		'aether_product_score_bars'  => array(
			array( 'star' => 5, 'percent' => 78, 'count' => 100 ),
			array( 'star' => 4, 'percent' => 15, 'count' => 19 ),
			array( 'star' => 3, 'percent' => 5,  'count' => 6 ),
			array( 'star' => 2, 'percent' => 2,  'count' => 2 ),
			array( 'star' => 1, 'percent' => 1,  'count' => 1 ),
		),
		'aether_product_reviews'     => array(
			array(
				'initials' => 'MC',
				'name'     => 'Marcus Chen',
				'meta'     => 'Verified — 2 weeks ago',
				'stars'    => 5,
				'title'    => 'Game changer for marathon training',
				'text'     => 'The carbon fiber plate gives insane energy return. I shaved 3 minutes off my half marathon time within the first month. The void cushioning makes it feel like you\'re running on clouds.',
			),
			array(
				'initials' => 'SK',
				'name'     => 'Sarah Kim',
				'meta'     => 'Verified — 1 month ago',
				'stars'    => 5,
				'title'    => 'Premium feel from box to street',
				'text'     => 'The design is unlike anything I\'ve seen. The unboxing experience alone is worth it. These are functional art. Get compliments every time I wear them.',
			),
			array(
				'initials' => 'JW',
				'name'     => 'James Wright',
				'meta'     => 'Verified — 3 weeks ago',
				'stars'    => 4.5,
				'title'    => 'My athletes love these',
				'text'     => 'I\'ve put all my competitive runners in AETHER. The performance difference is measurable. Lightweight, responsive, and the adaptive fit system means zero hot spots during long runs.',
			),
		),
		// AETHER palette — registered here too so aureon_get_option() resolves
		// them from the single settings bucket (aureon_color_option_defaults is
		// a separate bridge used by the customizer/WC color mapping).
		'aether_color_bg'           => '#09090B',
		'aether_color_surface'      => '#141416',
		'aether_color_surface_2'    => '#1a1a1d',
		'aether_color_surface_3'    => '#232327',
		'aether_color_text'         => '#FFFFFF',
		'aether_color_muted'        => '#A8B5C0',
		'aether_color_accent'       => '#C8956C',
		'aether_color_accent_hover' => '#D4A574',
		'aether_color_border'       => '#1A1A1A',
		'aether_color_error'        => '#CC4444',
		'aether_color_success'      => '#4CAF50',
		// Font stacks.
		'aether_font_heading'       => 'Cabinet Grotesk',
		'aether_font_body'          => 'Satoshi',
	) );
}

add_filter( 'aureon_color_option_defaults', 'aether_frontend_color_defaults' );
/**
 * Register AETHER palette on the color defaults bridge (used by WC + customizer).
 *
 * @param array $defaults Color defaults.
 * @return array Merged defaults.
 */
function aether_frontend_color_defaults( $defaults ) {
	return array_merge( $defaults, array(
		'aether_color_bg'      => '#09090B',
		'aether_color_surface' => '#141416',
		'aether_color_surface_2' => '#1a1a1d',
		'aether_color_surface_3' => '#232327',
		'aether_color_text'    => '#FFFFFF',
		'aether_color_muted'   => '#A8B5C0',
		'aether_color_accent'  => '#C8956C',
		'aether_color_accent_hover' => '#D4A574',
		'aether_color_border'  => 'rgba(255,255,255,0.08)',
		'aether_color_error'   => '#e5484d',
		'aether_color_success' => '#46a758',
	) );
}

add_filter( 'aureon_font_option_defaults', 'aether_frontend_font_defaults' );
/**
 * Register AETHER font stack defaults on the font bridge.
 *
 * @param array $defaults Font defaults.
 * @return array Merged defaults.
 */
function aether_frontend_font_defaults( $defaults ) {
	return array_merge( $defaults, array(
		'aether_font_heading' => 'Cabinet Grotesk',
		'aether_font_body'    => 'Satoshi',
	) );
}
