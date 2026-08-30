<?php
/**
 * This file handles the customizer fields for the content.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

Aureon_Customize_Field::add_title(
	'aureon_content_colors_title',
	array(
		'section' => 'aureon_colors_section',
		'title' => __( 'Content', 'aureon' ),
		'choices' => array(
			'toggleId' => 'content-colors',
		),
	)
);

$content_colors = '.separate-containers .inside-article, .separate-containers .comments-area, .separate-containers .page-header, .one-container .container, .separate-containers .paging-navigation, .inside-page-header';

Aureon_Customize_Field::add_field(
	'aureon_settings[content_background_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['content_background_color'],
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Background', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'content-colors',
		),
		'output' => array(
			array(
				'element'  => $content_colors,
				'property' => 'background-color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[content_text_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['content_text_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Text', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'content-colors',
		),
		'output' => array(
			array(
				'element'  => $content_colors,
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_content_link_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'content-colors',
			'items' => array(
				'content_link_color',
				'content_link_hover_color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[content_link_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['content_link_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Link', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'wrapper' => 'content_link_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
			'toggleId' => 'content-colors',
		),
		'output' => array(
			array(
				'element'  => '.inside-article a:not(.button):not(.wp-block-button__link), .inside-article a:not(.button):not(.wp-block-button__link):visited, .paging-navigation a, .paging-navigation a:visited, .comments-area a, .comments-area a:visited, .page-header a, .page-header a:visited',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[content_link_hover_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['content_link_hover_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Link Hover', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'wrapper' => 'content_link_hover_color',
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'toggleId' => 'content-colors',
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => '.inside-article a:not(.button):not(.wp-block-button__link):hover, .paging-navigation a:hover, .comments-area a:hover, .page-header a:hover',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[content_title_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['content_title_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Content Title', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'content-colors',
		),
		'output' => array(
			array(
				'element'  => '.entry-header h1,.page-header h1',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_archive_content_title_link_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'content-colors',
			'items' => array(
				'blog_post_title_color',
				'blog_post_title_hover_color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[blog_post_title_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['blog_post_title_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Archive Content Title', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'wrapper' => 'blog_post_title_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
			'toggleId' => 'content-colors',
		),
		'output' => array(
			array(
				'element'  => '.entry-title a,.entry-title a:visited',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[blog_post_title_hover_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['blog_post_title_hover_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Archive Content Title Hover', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'wrapper' => 'blog_post_title_hover_color',
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'toggleId' => 'content-colors',
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => '.entry-title a:hover',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[entry_meta_text_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['entry_meta_text_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Entry Meta Text', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'content-colors',
		),
		'output' => array(
			array(
				'element'  => '.entry-meta',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_entry_meta_link_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'content-colors',
			'items' => array(
				'entry_meta_link_color',
				'entry_meta_link_color_hover',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[entry_meta_link_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['entry_meta_link_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Entry Meta Links', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'wrapper' => 'entry_meta_link_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
			'toggleId' => 'content-colors',
		),
		'output' => array(
			array(
				'element'  => '.entry-meta a',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[entry_meta_link_color_hover]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['entry_meta_link_color_hover'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Entry Meta Links Hover', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'wrapper' => 'entry_meta_link_color_hover',
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'toggleId' => 'content-colors',
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => '.entry-meta a:hover',
				'property' => 'color',
			),
		),
	)
);

$headings = array(
	array(
		'slug' => 'h1_color',
		'label' => __( 'Heading 1 (H1) Color', 'aureon' ),
		'selector' => 'h1',
	),
	array(
		'slug' => 'h2_color',
		'label' => __( 'Heading 2 (H2) Color', 'aureon' ),
		'selector' => 'h2',
	),
	array(
		'slug' => 'h3_color',
		'label' => __( 'Heading 3 (H3) Color', 'aureon' ),
		'selector' => 'h3',
	),
	array(
		'slug' => 'h4_color',
		'label' => __( 'Heading 4 (H4) Color', 'aureon' ),
		'selector' => 'h4',
	),
	array(
		'slug' => 'h5_color',
		'label' => __( 'Heading 5 (H5) Color', 'aureon' ),
		'selector' => 'h5',
	),
	array(
		'slug' => 'h6_color',
		'label' => __( 'Heading 6 (H6) Color', 'aureon' ),
		'selector' => 'h6',
	),
);

foreach ( $headings as $heading ) {
	Aureon_Customize_Field::add_field(
		'aureon_settings[' . $heading['slug'] . ']',
		'Aureon_Customize_Color_Control',
		array(
			'default' => $color_defaults[ $heading['slug'] ],
			'sanitize_callback' => 'aureon_sanitize_hex_color',
			'transport' => 'postMessage',
		),
		array(
			'label' => $heading['label'],
			'section' => 'aureon_colors_section',
			'choices' => array(
				'toggleId' => 'content-colors',
			),
			'output' => array(
				array(
					'element'  => $heading['selector'],
					'property' => 'color',
				),
			),
		)
	);
}
