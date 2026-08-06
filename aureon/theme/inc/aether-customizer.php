<?php
/**
 * AETHER Customizer Settings.
 *
 * Registers Customizer panel, sections, and controls for the
 * AETHER frontend: hero slides, announcement bar, section labels.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'customize_register', 'aether_customize_register', 50 );

// Enqueue live preview JS in Customizer preview pane.
add_action( 'customize_preview_init', 'aether_customize_preview_init' );
function aether_customize_preview_init() {
	wp_enqueue_script(
		'aether-customize-preview',
		get_template_directory_uri() . '/assets/aether/js/customize-preview.js',
		array( 'customize-preview' ),
		AUREON_VERSION,
		true
	);
}
/**
 * Register AETHER Customizer panel, sections, and controls.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function aether_customize_register( $wp_customize ) {
	// ─── Panel: AETHER Frontend ────────────────────────────────
	$wp_customize->add_panel(
		'aether_panel',
		array(
			'title'    => __( 'AETHER Frontend', 'aureon' ),
			'priority' => 200,
		)
	);

	// ─── Section: Hero Slides ──────────────────────────────────
	$wp_customize->add_section(
		'aether_hero_section',
		array(
			'title' => __( 'Hero Slides', 'aureon' ),
			'panel' => 'aether_panel',
		)
	);

	// Slide 1.
	$wp_customize->add_setting(
		'aether_hero_slide_1_headline',
		array(
			'default'           => 'AETHER',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_hero_slide_1_headline',
		array(
			'label'   => __( 'Slide 1 — Headline', 'aureon' ),
			'section' => 'aether_hero_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aether_hero_slide_1_subline',
		array(
			'default'           => 'Born from the silence between stars.',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_hero_slide_1_subline',
		array(
			'label'   => __( 'Slide 1 — Subline', 'aureon' ),
			'section' => 'aether_hero_section',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'aether_hero_slide_1_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'aether_hero_slide_1_image',
			array(
				'label'   => __( 'Slide 1 — Image', 'aureon' ),
				'section' => 'aether_hero_section',
			)
		)
	);

	// Slide 2.
	$wp_customize->add_setting(
		'aether_hero_slide_2_headline',
		array(
			'default'           => 'Cloud Stride',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_hero_slide_2_headline',
		array(
			'label'   => __( 'Slide 2 — Headline', 'aureon' ),
			'section' => 'aether_hero_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aether_hero_slide_2_subline',
		array(
			'default'           => 'Float above the pavement.',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_hero_slide_2_subline',
		array(
			'label'   => __( 'Slide 2 — Subline', 'aureon' ),
			'section' => 'aether_hero_section',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'aether_hero_slide_2_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'aether_hero_slide_2_image',
			array(
				'label'   => __( 'Slide 2 — Image', 'aureon' ),
				'section' => 'aether_hero_section',
			)
		)
	);

	// Slide 3.
	$wp_customize->add_setting(
		'aether_hero_slide_3_headline',
		array(
			'default'           => 'Midnight Edition',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_hero_slide_3_headline',
		array(
			'label'   => __( 'Slide 3 — Headline', 'aureon' ),
			'section' => 'aether_hero_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aether_hero_slide_3_subline',
		array(
			'default'           => 'Darkness refined.',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_hero_slide_3_subline',
		array(
			'label'   => __( 'Slide 3 — Subline', 'aureon' ),
			'section' => 'aether_hero_section',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'aether_hero_slide_3_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'aether_hero_slide_3_image',
			array(
				'label'   => __( 'Slide 3 — Image', 'aureon' ),
				'section' => 'aether_hero_section',
			)
		)
	);

	// ─── Section: Announcement Bar ─────────────────────────────
	$wp_customize->add_section(
		'aether_announcement_section',
		array(
			'title' => __( 'Announcement Bar', 'aureon' ),
			'panel' => 'aether_panel',
		)
	);

	$wp_customize->add_setting(
		'aether_announcement_text',
		array(
			'default'           => 'Free Shipping On Orders Over $200',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_announcement_text',
		array(
			'label'   => __( 'Announcement Text', 'aureon' ),
			'section' => 'aether_announcement_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aether_announcement_enable',
		array(
			'default'           => true,
			'sanitize_callback' => 'aureon_sanitize_checkbox',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'aether_announcement_enable',
		array(
			'label'   => __( 'Show Announcement Bar', 'aureon' ),
			'section' => 'aether_announcement_section',
			'type'    => 'checkbox',
		)
	);

	// ─── Section: Design Tokens ────────────────────────────────
	$wp_customize->add_section(
		'aether_tokens_section',
		array(
			'title' => __( 'Design Tokens', 'aureon' ),
			'panel' => 'aether_panel',
		)
	);

	// Color tokens.
	$color_tokens = array(
		'aether_color_void'    => array( 'label' => __( 'Background (Void)', 'aureon' ), 'default' => '#09090B' ),
		'aether_color_surface' => array( 'label' => __( 'Surface / Cards', 'aureon' ), 'default' => '#141416' ),
		'aether_color_accent'  => array( 'label' => __( 'Accent (Gold)', 'aureon' ), 'default' => '#C8956C' ),
		'aether_color_text'    => array( 'label' => __( 'Secondary Text (Chrome)', 'aureon' ), 'default' => '#A8B5C0' ),
	);

	foreach ( $color_tokens as $id => $args ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$id,
				array(
					'label'   => $args['label'],
					'section' => 'aether_tokens_section',
				)
			)
		);
	}

	// Typography tokens.
	$wp_customize->add_setting(
		'aether_font_heading',
		array(
			'default'           => "'Cabinet Grotesk', sans-serif",
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'aether_font_heading',
		array(
			'label'   => __( 'Heading Font', 'aureon' ),
			'section' => 'aether_tokens_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aether_font_body',
		array(
			'default'           => "'Satoshi', sans-serif",
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'aether_font_body',
		array(
			'label'   => __( 'Body Font', 'aureon' ),
			'section' => 'aether_tokens_section',
			'type'    => 'text',
		)
	);

	// Spacing tokens.
	$wp_customize->add_setting(
		'aether_container_width',
		array(
			'default'           => '1200',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'aether_container_width',
		array(
			'label'       => __( 'Container Max Width (px)', 'aureon' ),
			'description' => __( 'Maximum width of the main content container.', 'aureon' ),
			'section'     => 'aether_tokens_section',
			'type'        => 'number',
		)
	);

	// ─── Section: Section Labels ───────────────────────────────
	$wp_customize->add_section(
		'aether_labels_section',
		array(
			'title' => __( 'Section Labels', 'aureon' ),
			'panel' => 'aether_panel',
		)
	);

	// Categories section.
	$wp_customize->add_setting(
		'aether_section_label_categories',
		array(
			'default'           => 'Shop by Category',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_section_label_categories',
		array(
			'label'   => __( 'Categories — Label', 'aureon' ),
			'section' => 'aether_labels_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aether_section_title_categories',
		array(
			'default'           => 'Find Your Fit',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_section_title_categories',
		array(
			'label'   => __( 'Categories — Title', 'aureon' ),
			'section' => 'aether_labels_section',
			'type'    => 'text',
		)
	);

	// Bestsellers section.
	$wp_customize->add_setting(
		'aether_section_label_bestsellers',
		array(
			'default'           => 'Bestsellers',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_section_label_bestsellers',
		array(
			'label'   => __( 'Bestsellers — Label', 'aureon' ),
			'section' => 'aether_labels_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aether_section_title_bestsellers',
		array(
			'default'           => 'Most Loved',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_section_title_bestsellers',
		array(
			'label'   => __( 'Bestsellers — Title', 'aureon' ),
			'section' => 'aether_labels_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aether_section_subtitle_bestsellers',
		array(
			'default'           => 'The shoes everyone\'s talking about. Tried, tested, and obsessed over.',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_section_subtitle_bestsellers',
		array(
			'label'   => __( 'Bestsellers — Subtitle', 'aureon' ),
			'section' => 'aether_labels_section',
			'type'    => 'textarea',
		)
	);

	// Reviews section.
	$wp_customize->add_setting(
		'aether_section_label_reviews',
		array(
			'default'           => 'Reviews',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_section_label_reviews',
		array(
			'label'   => __( 'Reviews — Label', 'aureon' ),
			'section' => 'aether_labels_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aether_section_title_reviews',
		array(
			'default'           => 'What Athletes Say',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_section_title_reviews',
		array(
			'label'   => __( 'Reviews — Title', 'aureon' ),
			'section' => 'aether_labels_section',
			'type'    => 'text',
		)
	);

	// FAQ section.
	$wp_customize->add_setting(
		'aether_section_label_faq',
		array(
			'default'           => 'FAQ',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_section_label_faq',
		array(
			'label'   => __( 'FAQ — Label', 'aureon' ),
			'section' => 'aether_labels_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aether_section_title_faq',
		array(
			'default'           => 'Got Questions?',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_section_title_faq',
		array(
			'label'   => __( 'FAQ — Title', 'aureon' ),
			'section' => 'aether_labels_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'aether_section_subtitle_faq',
		array(
			'default'           => 'Everything you need to know about us.',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'aether_section_subtitle_faq',
		array(
			'label'   => __( 'FAQ — Subtitle', 'aureon' ),
			'section' => 'aether_labels_section',
			'type'    => 'textarea',
		)
	);

	// ─── Social Media URLs ────────────────────────────────────
	$wp_customize->add_section(
		'aether_social_section',
		array(
			'title' => __( 'Social Media', 'aureon' ),
			'panel' => 'aether_panel',
		)
	);

	$social_links = array(
		'aether_social_instagram' => array( 'label' => __( 'Instagram URL', 'aureon' ), 'default' => '#' ),
		'aether_social_twitter'   => array( 'label' => __( 'Twitter / X URL', 'aureon' ), 'default' => '#' ),
		'aether_social_tiktok'    => array( 'label' => __( 'TikTok URL', 'aureon' ), 'default' => '#' ),
		'aether_social_youtube'   => array( 'label' => __( 'YouTube URL', 'aureon' ), 'default' => '#' ),
		'aether_social_facebook'  => array( 'label' => __( 'Facebook URL', 'aureon' ), 'default' => '#' ),
	);

	foreach ( $social_links as $id => $args ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$id,
			array(
				'label'   => $args['label'],
				'section' => 'aether_social_section',
				'type'    => 'url',
			)
		);
	}
}
