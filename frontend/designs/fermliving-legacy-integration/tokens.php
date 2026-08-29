<?php
/**
 * Ferm Living Design Pack — Token Defaults
 *
 * Returns an array of token overrides merged onto aureon_option_defaults at priority 20.
 * All content sourced from frozen fermliving.com reference (SiteOne-Crawler).
 *
 * @package Aureon\Designs\FermLiving
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pack_url = aether_pack_url();
$assets   = $pack_url . 'assets';

return array(

	/* ── Brand ──────────────────────────────────────────────── */
	'aether_logo_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 28" fill="none" stroke="currentColor" stroke-width="1.2">
		<text x="0" y="22" font-family="Fraunces,Georgia,serif" font-size="22" font-weight="400" fill="currentColor" stroke="none">ferm living</text></svg>',

	/* ── Announcement bar ───────────────────────────────────── */
	'ferm_announcement_items' => array(
		array( 'text' => 'Welcome, Sign up here and take 10% off', 'url' => home_url( '/newsletter/' ) ),
		array( 'text' => 'Free shipping on EU orders above €,€150*', 'url' => home_url( '/shipping/' ) ),
		array( 'text' => 'Worldwide delivery', 'url' => '' ),
		array( 'text' => 'EU delivery within 2-5 days*', 'url' => home_url( '/shipping/' ) ),
	),
	'ferm_announcement_speed' => 4000,

	/* ── Hero — 2-up full-height panels ────────────────────── */
	'aether_hero_slides' => array(
		array(
			'id'           => 'slide_fl_001',
			'visible'      => true,
			'headline'     => 'Ferm Living Bestsellers',
			'accent'       => '',
			'subline'      => '',
			'badge'        => '',
			'image'        => $assets . '/hero/bestsellers.webp',
			'mobile_image' => '',
			'image_alt'    => 'Ferm Living Bestsellers collection',
			'overlay'      => '',
			'primary_cta'  => array( 'label' => 'Shop Now', 'url' => home_url( '/shop/' ) ),
			'secondary_cta'=> array( 'label' => '', 'url' => '' ),
		),
		array(
			'id'           => 'slide_fl_002',
			'visible'      => true,
			'headline'     => 'Made for Gathering',
			'accent'       => '',
			'subline'      => '',
			'badge'        => '',
			'image'        => $assets . '/hero/dining.webp',
			'mobile_image' => '',
			'image_alt'    => 'Ferm Living dining room collection',
			'overlay'      => '',
			'primary_cta'  => array( 'label' => 'Discover the Dining Room', 'url' => home_url( '/shop/' ) ),
			'secondary_cta'=> array( 'label' => '', 'url' => '' ),
		),
	),

	/* ── Categories — 7 real Ferm Living product categories ── */
	'aether_category_items' => array(
		array(
			'name'     => 'Furniture',
			'count'    => 'Sofas, Tables & Storage',
			'image'    => $assets . '/categories/furniture.webp',
			'url'      => home_url( '/shop/furniture/' ),
		),
		array(
			'name'     => 'Lighting',
			'count'    => 'Lamps & Pendants',
			'image'    => $assets . '/categories/lighting.webp',
			'url'      => home_url( '/shop/lighting/' ),
			'modifier' => 'accent',
		),
		array(
			'name'     => 'Accessories',
			'count'    => 'Vases, Mirrors & Decor',
			'image'    => $assets . '/categories/accessories.webp',
			'url'      => home_url( '/shop/accessories/' ),
		),
		array(
			'name'     => 'Kids',
			'count'    => 'Toys, Textiles & Furniture',
			'image'    => $assets . '/categories/kids.webp',
			'url'      => home_url( '/shop/kids/' ),
		),
		array(
			'name'     => 'Textiles',
			'count'    => 'Cushions, Bedspreads & Throws',
			'image'    => $assets . '/categories/textiles.webp',
			'url'      => home_url( '/shop/textiles/' ),
		),
		array(
			'name'     => 'Kitchen',
			'count'    => 'Glasses, Plates & Serveware',
			'image'    => $assets . '/categories/kitchen.webp',
			'url'      => home_url( '/shop/kitchen/' ),
		),
		array(
			'name'     => 'Outdoor Living',
			'count'    => 'Outdoor Accessories & Seating',
			'image'    => $assets . '/categories/outdoor.webp',
			'url'      => home_url( '/shop/outdoor/' ),
		),
	),

	/* ── Products — 8 homepage products ────────────────────── */
	'aether_product_items' => array(
		array(
			'name'    => 'Donkey Soft Toy',
			'tagline' => 'Undyed/Dark Sand · OCS Blended Certified',
			'price'   => '€35',
			'rating'  => 5,
			'reviews' => 0,
			'badge'   => 'Certified',
			'image'   => $assets . '/products/donkey-soft-toy.png',
			'url'     => home_url( '/shop/' ),
		),
		array(
			'name'    => 'Pear Braided Storage',
			'tagline' => 'Natural · Braided paper fibre',
			'price'   => 'From €49',
			'rating'  => 5,
			'reviews' => 0,
			'badge'   => '',
			'image'   => $assets . '/products/pear-braided-storage.png',
			'url'     => home_url( '/shop/' ),
		),
		array(
			'name'    => 'Swif Bird Garland',
			'tagline' => 'Undyed · OCS Blended Certified',
			'price'   => '€39',
			'rating'  => 5,
			'reviews' => 0,
			'badge'   => 'Certified',
			'image'   => $assets . '/products/swif-bird-garland.png',
			'url'     => home_url( '/shop/' ),
		),
		array(
			'name'    => 'Willora Braided Storage',
			'tagline' => 'Set of 2 · Natural',
			'price'   => '€299',
			'rating'  => 5,
			'reviews' => 0,
			'badge'   => '',
			'image'   => $assets . '/products/willora-braided-storage.png',
			'url'     => home_url( '/shop/' ),
		),
		array(
			'name'    => 'Parcel Hallway Cabinet',
			'tagline' => 'Tall · Dark Stained Oak',
			'price'   => '€1,085',
			'rating'  => 5,
			'reviews' => 0,
			'badge'   => 'New',
			'image'   => $assets . '/products/parcel-hallway-cabinet.png',
			'url'     => home_url( '/shop/' ),
		),
		array(
			'name'    => 'Paper Pulp Box',
			'tagline' => 'Set of 2 · Brown · Certified',
			'price'   => 'From €39',
			'rating'  => 5,
			'reviews' => 0,
			'badge'   => 'Certified',
			'image'   => $assets . '/products/paper-pulp-box.png',
			'url'     => home_url( '/shop/' ),
		),
		array(
			'name'    => 'Kona Bookcase',
			'tagline' => '3×6 · Dark Stained Oak · Certified',
			'price'   => '€2,715',
			'rating'  => 5,
			'reviews' => 0,
			'badge'   => 'Certified',
			'image'   => $assets . '/products/kona-bookcase.png',
			'url'     => home_url( '/shop/' ),
		),
		array(
			'name'    => 'Haze Wall Cabinet',
			'tagline' => 'Reeded Glass · Cashmere',
			'price'   => '€339',
			'rating'  => 5,
			'reviews' => 0,
			'badge'   => '',
			'image'   => $assets . '/products/haze-wall-cabinet.png',
			'url'     => home_url( '/shop/' ),
		),
	),

	/* ── Rooms — 6 room inspiration cards ──────────────────── */
	'aether_room_items' => array(
		array(
			'name'  => "The Kids' Room",
			'image' => $assets . '/rooms/kids.webp',
			'url'   => home_url( '/shop/kids/' ),
			'tags'  => 'Kids Toys · Kids Accessories · Kids Furniture · Kids Lamps',
		),
		array(
			'name'  => 'The Green Space',
			'image' => $assets . '/rooms/green-space.webp',
			'url'   => home_url( '/shop/outdoor/' ),
			'tags'  => 'Outdoor Accessories · Outdoor Textiles · Outdoor Pots · Outdoor Seating',
		),
		array(
			'name'  => 'The Living Room',
			'image' => $assets . '/rooms/living-room.webp',
			'url'   => home_url( '/shop/furniture/' ),
			'tags'  => 'Sofas · Lounge Chairs · Lighting · Rugs',
		),
		array(
			'name'  => 'The Kitchen',
			'image' => $assets . '/rooms/kitchen.webp',
			'url'   => home_url( '/shop/kitchen/' ),
			'tags'  => 'Kitchen Textiles · Glasses · Plates · Serveware',
		),
		array(
			'name'  => 'The Bedroom',
			'image' => $assets . '/rooms/bedroom.webp',
			'url'   => home_url( '/shop/textiles/' ),
			'tags'  => 'Storage · Cushions · Bedspreads · Candle Holders',
		),
		array(
			'name'  => 'The Hallway',
			'image' => $assets . '/rooms/hallway.webp',
			'url'   => home_url( '/shop/accessories/' ),
			'tags'  => 'Mirrors · Table Lamps · Runners · Vases',
		),
	),

	/* ── Editorial splits — homepage text+image bands ──────── */
	'aether_editorial_items' => array(
		array(
			'title'     => 'Bestsellers for Kids',
			'text'      => 'Soft toys, garlands, and storage — designed to spark imagination and bring warmth to any children\'s space.',
			'image'     => $assets . '/editorial/kids-bestsellers.webp',
			'image_alt' => 'Ferm Living kids bestsellers collection',
			'cta_label' => 'Shop Kids',
			'cta_url'   => home_url( '/shop/kids/' ),
			'reverse'   => false,
		),
	),

	/* ── Newsletter ─────────────────────────────────────────── */
	'aether_newsletter_heading' => 'Ferm Living news',
	'aether_newsletter_text'    => 'Get exclusive drops, early access, and Ferm Living news.',

	/* ── Footer USPs ────────────────────────────────────────── */
	'aether_footer_usp_items' => array(
		array(
			'title' => 'Free shipping',
			'text'  => 'on EU orders above EUR 150*',
			'url'   => home_url( '/shop/' ),
		),
		array(
			'title' => 'Sign up and get 10% off',
			'text'  => 'of your first purchase',
			'url'   => home_url( '/shop/' ),
		),
		array(
			'title' => 'We would love to help you',
			'text'  => 'Call us at +45 7022 7523',
			'url'   => '',
		),
		array(
			'title' => 'Enjoy fast EU delivery',
			'text'  => 'within 2-5 business days*',
			'url'   => home_url( '/shop/' ),
		),
	),

	/* ── Footer columns ─────────────────────────────────────── */
	'aether_footer_columns' => array(
		array(
			'heading' => 'Customer Service',
			'links'   => array(
				array( 'label' => 'Contact',          'url' => home_url( '/contact/' ) ),
				array( 'label' => 'Find a retailer',  'url' => home_url( '/shop/' ) ),
				array( 'label' => 'FAQ',              'url' => home_url( '/faq/' ) ),
				array( 'label' => 'Shipping',         'url' => home_url( '/shop/' ) ),
				array( 'label' => 'Returns',          'url' => home_url( '/shop/' ) ),
				array( 'label' => 'Claim Form',       'url' => home_url( '/contact/' ) ),
				array( 'label' => 'Gift Card',        'url' => home_url( '/shop/' ) ),
			),
		),
		array(
			'heading' => 'Information',
			'links'   => array(
				array( 'label' => 'About Us',             'url' => home_url( '/about/' ) ),
				array( 'label' => 'Career',               'url' => home_url( '/about/' ) ),
				array( 'label' => 'Responsibility',       'url' => home_url( '/about/' ) ),
				array( 'label' => 'Ferm Living Boutique', 'url' => home_url( '/about/' ) ),
				array( 'label' => 'Styling Sessions',     'url' => home_url( '/about/' ) ),
				array( 'label' => 'Care and Maintenance', 'url' => home_url( '/about/' ) ),
				array( 'label' => 'Fabric Overview',      'url' => home_url( '/about/' ) ),
			),
		),
		array(
			'heading' => 'Professionals',
			'links'   => array(
				array( 'label' => 'B2B Login',           'url' => '#' ),
				array( 'label' => 'Image Bank',          'url' => '#' ),
				array( 'label' => 'Showrooms',           'url' => home_url( '/about/' ) ),
				array( 'label' => 'Catalogues',          'url' => home_url( '/about/' ) ),
				array( 'label' => 'Contract Projects',   'url' => home_url( '/about/' ) ),
				array( 'label' => 'Company Information', 'url' => home_url( '/about/' ) ),
			),
		),
	),

	/* ── Footer bottom ──────────────────────────────────────── */
	'aether_footer_company'     => 'Ferm Living ApS CVR No. 30070186',
	'aether_footer_payment_icon'=> $assets . '/common/card-icons.png',

	/* ── Search ─────────────────────────────────────────────── */
	'aether_search_placeholder' => 'Search Ferm Living...',

	/* ── About page ─────────────────────────────────────────── */
	'aether_about_heading'  => 'About Ferm Living',
	'aether_about_body'     => 'Life is full of contrasts. As we navigate expectations and dreams in search of meaning and comfort, we long for a balanced life with room to be ourselves. A place where we can realise the true value of things and feel at home. Driven by a love for authentic design and a commitment to responsible choices, we craft honest products and calm environments that help you balance life\'s contrasts.

From our home in Copenhagen, we collaborate with artisans around the world, fusing our Scandinavian mindset with global skills and traditions.

Our collections are defined by soft forms, rich textures, and curious details, allowing you to create composed atmospheres with a touch of the unexpected. From materials and processes to production and delivery, we challenge ourselves to help shape a sustainable future, making it easier for you to make responsible choices.',
	'aether_about_features' => array(
		array( 'title' => 'Scandinavian Mindset', 'text' => 'Rooted in Copenhagen, we fuse Nordic simplicity with global craftsmanship.' ),
		array( 'title' => 'Responsible Choices', 'text' => 'From materials to delivery, we challenge ourselves to shape a sustainable future.' ),
		array( 'title' => 'Authentic Design',    'text' => 'Soft forms, rich textures, and curious details define our collections.' ),
	),
	'aether_about_values'   => array(
		array( 'title' => 'Honest Products',      'text' => 'We craft products that help you create space to feel comfortably you.' ),
		array( 'title' => 'Calm Environments',    'text' => 'Our collections create composed atmospheres with a touch of the unexpected.' ),
		array( 'title' => 'Global Collaboration', 'text' => 'We collaborate with artisans around the world, blending skills and traditions.' ),
	),
	'aether_about_stats'    => array(
		array( 'value' => 'Copenhagen', 'label' => 'Headquarters' ),
		array( 'value' => '2005',       'label' => 'Founded' ),
		array( 'value' => '50+',        'label' => 'Countries' ),
	),

	/* ── FAQ ────────────────────────────────────────────────── */
	'aether_faq_items' => array(
		array( 'q' => 'Where does Ferm Living ship to?',             'a' => 'We ship to over 50 countries worldwide. Free shipping is offered on EU orders above EUR 150.' ),
		array( 'q' => 'What is the return policy?',                   'a' => 'You can return items within 30 days of delivery. Items must be unused and in original packaging.' ),
		array( 'q' => 'How do I care for my Ferm Living products?',   'a' => 'Visit our Care and Maintenance page for detailed guides on each material and product type.' ),
		array( 'q' => 'Do you offer interior styling sessions?',      'a' => 'Yes, visit our Ferm Living Boutique in Copenhagen for personalised styling sessions.' ),
		array( 'q' => 'Are Ferm Living products sustainable?',        'a' => 'We are committed to responsible choices. Look for the Certified badge on products meeting our sustainability standards.' ),
	),

	/* ── Social links ───────────────────────────────────────── */
	'aether_social_items' => array(
		array( 'label' => 'Instagram', 'url' => 'https://www.instagram.com/fermliving/' ),
		array( 'label' => 'Facebook',  'url' => 'https://www.facebook.com/fermLIVING/' ),
		array( 'label' => 'Pinterest', 'url' => 'https://pinterest.com/fermliving/' ),
		array( 'label' => 'TikTok',    'url' => 'https://www.tiktok.com/@fermliving_official' ),
		array( 'label' => 'YouTube',   'url' => 'https://www.youtube.com/user/fermliving' ),
	),

	/* ── Trust ──────────────────────────────────────────────── */
	'aether_trust_items' => array(
		array( 'icon' => '', 'title' => 'Free EU Shipping',   'text' => 'On orders above EUR 150' ),
		array( 'icon' => '', 'title' => '30-Day Returns',     'text' => 'Easy and hassle-free' ),
		array( 'icon' => '', 'title' => 'Certified Products', 'text' => 'Meets sustainability standards' ),
		array( 'icon' => '', 'title' => 'Secure Payment',     'text' => 'Encrypted checkout' ),
	),
);
