<?php
defined( 'WPINC' ) or die;

if ( ! function_exists( 'aureon_blog_customize_register' ) ) {
	add_action( 'customize_register', 'aureon_blog_customize_register', 99 );

	function aureon_blog_customize_register( $wp_customize ) {
		// Get our defaults.
		$defaults = aureon_blog_get_defaults();

		// Get our controls.
		require_once AUREON_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		// Add control types so controls can be built using JS.
		if ( method_exists( $wp_customize, 'register_control_type' ) ) {
			$wp_customize->register_control_type( 'Aureon_Title_Customize_Control' );
		}

		// Remove our blog control from the free theme.
		if ( $wp_customize->get_control( 'blog_content_control' ) ) {
			$wp_customize->remove_control( 'blog_content_control' );
		}

		// Register our custom controls.
		if ( method_exists( $wp_customize, 'register_control_type' ) ) {
			$wp_customize->register_control_type( 'Aureon_Refresh_Button_Customize_Control' );
			$wp_customize->register_control_type( 'Aureon_Information_Customize_Control' );
			$wp_customize->register_control_type( 'Aureon_Control_Toggle_Customize_Control' );
		}

		$wp_customize->add_section(
			'aureon_blog_loop_template_section',
			array(
				'title' => __( 'Blog', 'aureon-studio' ),
				'capability' => 'edit_theme_options',
				'panel' => 'aureon_layout_panel',
				'priority' => 40,
				'active_callback' => function() {
					return aureon_has_active_element( 'loop-template', true );
				},
			)
		);

		$wp_customize->add_control(
			new Aureon_Information_Customize_Control(
				$wp_customize,
				'aureon_using_loop_template',
				array(
					'section'     => 'aureon_blog_loop_template_section',
					'description' => sprintf(
						/* translators: URL to the Elements dashboard. */
						__( 'This page is using a <a href="%s">Loop Template Element</a>. Other options can be found within that Element.', 'aureon-studio' ),
						admin_url( 'edit.php?post_type=aureon_elements' )
					),
					'notice' => true,
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'active_callback' => function() {
						return aureon_has_active_element( 'loop-template', true );
					},
					'priority' => 0,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_blog_settings[excerpt_length]', array(
				'default' => $defaults['excerpt_length'],
				'capability' => 'edit_theme_options',
				'type' => 'option',
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			'aureon_loop_template_excerpt_length',
			array(
				'type' => 'number',
				'label' => __( 'Excerpt word count', 'aureon-studio' ),
				'section' => 'aureon_blog_loop_template_section',
				'settings' => 'aureon_blog_settings[excerpt_length]',
			)
		);

		$wp_customize->add_setting(
			'aureon_blog_settings[read_more]',
			array(
				'default' => $defaults['read_more'],
				'capability' => 'edit_theme_options',
				'type' => 'option',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_control(
			'aureon_loop_template_read_more',
			array(
				'type' => 'text',
				'label' => __( 'Read more label', 'aureon-studio' ),
				'section' => 'aureon_blog_loop_template_section',
				'settings' => 'aureon_blog_settings[read_more]',
			)
		);

		// Blog content section.
		$wp_customize->add_section(
			'aureon_blog_section',
			array(
				'title' => __( 'Blog', 'aureon-studio' ),
				'capability' => 'edit_theme_options',
				'panel' => 'aureon_layout_panel',
				'priority' => 40,
				'active_callback' => function() {
					return ! aureon_has_active_element( 'loop-template', true );
				},
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_blog_archives_title',
				array(
					'section' => 'aureon_blog_section',
					'type' => 'aureon-customizer-title',
					'title' => __( 'Content', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Control_Toggle_Customize_Control(
				$wp_customize,
				'aureon_post_meta_toggle',
				array(
					'section' => 'aureon_blog_section',
					'targets' => array(
						'post-meta-archives' => __( 'Archives', 'aureon-studio' ),
						'post-meta-single' => __( 'Single', 'aureon-studio' ),
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_control(
			'aureon_settings[post_content]',
			array(
				'type' => 'select',
				'label' => __( 'Content type', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'choices' => array(
					'full' => __( 'Full Content', 'aureon-studio' ),
					'excerpt' => __( 'Excerpt', 'aureon-studio' ),
				),
				'settings' => 'aureon_settings[post_content]',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[excerpt_length]', array(
				'type' => 'number',
				'label' => __( 'Excerpt word count', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[excerpt_length]',
				'active_callback' => 'aureon_premium_is_excerpt',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[read_more]', array(
				'type' => 'text',
				'label' => __( 'Read more label', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[read_more]',
			)
		);

		$wp_customize->add_setting(
			'aureon_blog_settings[read_more_button]',
			array(
				'default' => $defaults['read_more_button'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[read_more_button]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display read more as button', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[read_more_button]',
			)
		);

		// Post date
		$wp_customize->add_setting(
			'aureon_blog_settings[date]',
			array(
				'default' => $defaults['date'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[date]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display post date', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[date]',
			)
		);

		// Post author
		$wp_customize->add_setting(
			'aureon_blog_settings[author]',
			array(
				'default' => $defaults['author'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[author]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display post author', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[author]',
			)
		);

		// Category links
		$wp_customize->add_setting(
			'aureon_blog_settings[categories]',
			array(
				'default' => $defaults['categories'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[categories]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display post categories', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[categories]',
			)
		);

		// Tag links
		$wp_customize->add_setting(
			'aureon_blog_settings[tags]',
			array(
				'default' => $defaults['tags'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[tags]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display post tags', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[tags]',
			)
		);

		// Comment link
		$wp_customize->add_setting(
			'aureon_blog_settings[comments]',
			array(
				'default' => $defaults['comments'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[comments]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display comment count', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[comments]',
			)
		);

		// Infinite scroll
		$wp_customize->add_setting(
			'aureon_blog_settings[infinite_scroll]',
			array(
				'default' => $defaults['infinite_scroll'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[infinite_scroll]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Use infinite scroll', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[infinite_scroll]',
			)
		);

		// Infinite scroll
		$wp_customize->add_setting(
			'aureon_blog_settings[infinite_scroll_button]',
			array(
				'default' => $defaults['infinite_scroll_button'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[infinite_scroll_button]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Use button to load more posts', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[infinite_scroll_button]',
				'active_callback' => 'aureon_premium_infinite_scroll_active',
			)
		);

		// Load more text
		$wp_customize->add_setting(
			'aureon_blog_settings[masonry_load_more]', array(
				'default' => $defaults['masonry_load_more'],
				'capability' => 'edit_theme_options',
				'type' => 'option',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_control(
			'blog_masonry_load_more_control', array(
				'label' => __( 'Load more label', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[masonry_load_more]',
				'active_callback' => 'aureon_premium_infinite_scroll_button_active',
			)
		);

		// Loading text
		$wp_customize->add_setting(
			'aureon_blog_settings[masonry_loading]', array(
				'default' => $defaults['masonry_loading'],
				'capability' => 'edit_theme_options',
				'type' => 'option',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_control(
			'blog_masonry_loading_control', array(
				'label' => __( 'Loading label', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[masonry_loading]',
				'active_callback' => 'aureon_premium_infinite_scroll_button_active',
			)
		);

		$wp_customize->add_setting(
			'aureon_blog_settings[single_date]',
			array(
				'default' => $defaults['single_date'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[single_date]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display post date', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[single_date]',
			)
		);

		$wp_customize->add_setting(
			'aureon_blog_settings[single_author]',
			array(
				'default' => $defaults['single_author'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[single_author]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display post author', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[single_author]',
			)
		);

		$wp_customize->add_setting(
			'aureon_blog_settings[single_categories]',
			array(
				'default' => $defaults['single_categories'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[single_categories]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display post categories', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[single_categories]',
			)
		);

		$wp_customize->add_setting(
			'aureon_blog_settings[single_tags]',
			array(
				'default' => $defaults['single_tags'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[single_tags]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display post tags', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[single_tags]',
			)
		);

		$wp_customize->add_setting(
			'aureon_blog_settings[single_post_navigation]',
			array(
				'default' => $defaults['single_post_navigation'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[single_post_navigation]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display post navigation', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[single_post_navigation]',
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_blog_featured_images_title',
				array(
					'section' => 'aureon_blog_section',
					'type' => 'aureon-customizer-title',
					'title'	=> __( 'Featured Images', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Control_Toggle_Customize_Control(
				$wp_customize,
				'aureon_featured_image_toggle',
				array(
					'section' => 'aureon_blog_section',
					'targets' => array(
						'featured-image-archives' => __( 'Archives', 'aureon-studio' ),
						'featured-image-single' => __( 'Posts', 'aureon-studio' ),
						'featured-image-page' => __( 'Pages', 'aureon-studio' ),
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		// Show featured images
		$wp_customize->add_setting(
			'aureon_blog_settings[post_image]',
			array(
				'default' => $defaults['post_image'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[post_image]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display featured images', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[post_image]',
			)
		);

		// Padding
		$wp_customize->add_setting(
			'aureon_blog_settings[post_image_padding]',
			array(
				'default' => $defaults['post_image_padding'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[post_image_padding]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display padding around images', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[post_image_padding]',
				'active_callback' => 'aureon_premium_display_image_padding',
			)
		);

		// Location
		$wp_customize->add_setting(
			'aureon_blog_settings[post_image_position]',
			array(
				'default' => $defaults['post_image_position'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[post_image_position]',
			array(
				'type' => 'select',
				'label' => __( 'Location', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'choices' => array(
					'' => __( 'Below Title', 'aureon-studio' ),
					'post-image-above-header' => __( 'Above Title', 'aureon-studio' ),
				),
				'settings' => 'aureon_blog_settings[post_image_position]',
				'active_callback' => 'aureon_premium_featured_image_active',
			)
		);

		// Alignment
		$wp_customize->add_setting(
			'aureon_blog_settings[post_image_alignment]',
			array(
				'default' => $defaults['post_image_alignment'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[post_image_alignment]',
			array(
				'type' => 'select',
				'label' => __( 'Alignment', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'choices' => array(
					'post-image-aligned-center' => __( 'Center', 'aureon-studio' ),
					'post-image-aligned-left' => __( 'Left', 'aureon-studio' ),
					'post-image-aligned-right' => __( 'Right', 'aureon-studio' ),
				),
				'settings' => 'aureon_blog_settings[post_image_alignment]',
				'active_callback' => 'aureon_premium_featured_image_active',
			)
		);

		$wp_customize->add_setting(
			'aureon_blog_settings[post_image_size]',
			array(
				'default' => $defaults['post_image_size'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[post_image_size]',
			array(
				'type' => 'select',
				'label' => __( 'Media Attachment Size', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'choices' => aureon_blog_get_image_sizes(),
				'settings' => 'aureon_blog_settings[post_image_size]',
				'active_callback' => 'aureon_premium_featured_image_active',
			)
		);

		// Width
		$wp_customize->add_setting(
			'aureon_blog_settings[post_image_width]', array(
				'default' => $defaults['post_image_width'],
				'capability' => 'edit_theme_options',
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_empty_absint',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[post_image_width]',
			array(
				'type' => 'number',
				'label' => __( 'Width', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[post_image_width]',
				'active_callback' => 'aureon_premium_featured_image_active',
			)
		);

		// Height
		$wp_customize->add_setting(
			'aureon_blog_settings[post_image_height]', array(
				'default' => $defaults['post_image_height'],
				'capability' => 'edit_theme_options',
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_empty_absint',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[post_image_height]',
			array(
				'type' => 'number',
				'label' => __( 'Height', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[post_image_height]',
				'active_callback' => 'aureon_premium_featured_image_active',
			)
		);

		$wp_customize->add_control(
			new Aureon_Information_Customize_Control(
				$wp_customize,
				'aureon_regenerate_images_notice',
				array(
					'section'     => 'aureon_blog_section',
					'description' => sprintf(
						__( 'We will attempt to serve exact image sizes based on your width/height settings. If that is not possible, we will resize your images using CSS. Learn more about featured image sizing %s.', 'aureon-studio' ),
						'<a href="https://docs.aureonstudio.com/article/adjusting-the-featured-images/" target="_blank" rel="noopener noreferrer">' . __( 'here', 'aureon-studio' ) . '</a>'
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'active_callback' => 'aureon_premium_featured_image_active',
				)
			)
		);

		/*
		 * Single featured images
		 */

		$wp_customize->add_setting(
			'aureon_blog_settings[single_post_image]',
			array(
				'default' => $defaults['single_post_image'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[single_post_image]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display featured images', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[single_post_image]',
			)
		);

		// Padding
		$wp_customize->add_setting(
			'aureon_blog_settings[single_post_image_padding]',
			array(
				'default' => $defaults['single_post_image_padding'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[single_post_image_padding]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display padding around images', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[single_post_image_padding]',
				'active_callback' => 'aureon_premium_display_image_padding_single',
			)
		);

		// Location
		$wp_customize->add_setting(
			'aureon_blog_settings[single_post_image_position]',
			array(
				'default' => $defaults['single_post_image_position'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[single_post_image_position]',
			array(
				'type' => 'select',
				'label' => __( 'Location', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'choices' => array(
					'below-title' => __( 'Below Title', 'aureon-studio' ),
					'inside-content' => __( 'Above Title', 'aureon-studio' ),
					'above-content' => __( 'Above Content Area', 'aureon-studio' ),
				),
				'settings' => 'aureon_blog_settings[single_post_image_position]',
				'active_callback' => 'aureon_premium_single_featured_image_active',
			)
		);

		// Alignment
		$wp_customize->add_setting(
			'aureon_blog_settings[single_post_image_alignment]',
			array(
				'default' => $defaults['single_post_image_alignment'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[single_post_image_alignment]',
			array(
				'type' => 'select',
				'label' => __( 'Alignment', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'choices' => array(
					'center' => __( 'Center', 'aureon-studio' ),
					'left' => __( 'Left', 'aureon-studio' ),
					'right' => __( 'Right', 'aureon-studio' ),
				),
				'settings' => 'aureon_blog_settings[single_post_image_alignment]',
				'active_callback' => 'aureon_premium_single_featured_image_active',
			)
		);

		$wp_customize->add_setting(
			'aureon_blog_settings[single_post_image_size]',
			array(
				'default' => $defaults['single_post_image_size'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[single_post_image_size]',
			array(
				'type' => 'select',
				'label' => __( 'Media Attachment Size', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'choices' => aureon_blog_get_image_sizes(),
				'settings' => 'aureon_blog_settings[single_post_image_size]',
				'active_callback' => 'aureon_premium_single_featured_image_active',
			)
		);

		// Width
		$wp_customize->add_setting(
			'aureon_blog_settings[single_post_image_width]', array(
				'default' => $defaults['single_post_image_width'],
				'capability' => 'edit_theme_options',
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_empty_absint',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[single_post_image_width]',
			array(
				'type' => 'number',
				'label' => __( 'Width', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[single_post_image_width]',
				'active_callback' => 'aureon_premium_single_featured_image_active',
			)
		);

		// Height
		$wp_customize->add_setting(
			'aureon_blog_settings[single_post_image_height]', array(
				'default' => $defaults['single_post_image_height'],
				'capability' => 'edit_theme_options',
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_empty_absint',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[single_post_image_height]',
			array(
				'type' => 'number',
				'label' => __( 'Height', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[single_post_image_height]',
				'active_callback' => 'aureon_premium_single_featured_image_active',
			)
		);

		$wp_customize->add_control(
			new Aureon_Information_Customize_Control(
				$wp_customize,
				'aureon_regenerate_single_post_images_notice',
				array(
					'section'     => 'aureon_blog_section',
					'description' => sprintf(
						__( 'We will attempt to serve exact image sizes based on your width/height settings. If that is not possible, we will resize your images using CSS. Learn more about featured image sizing %s.', 'aureon-studio' ),
						'<a href="https://docs.aureonstudio.com/article/adjusting-the-featured-images/" target="_blank" rel="noopener noreferrer">' . __( 'here', 'aureon-studio' ) . '</a>'
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'active_callback' => 'aureon_premium_single_featured_image_active',
				)
			)
		);

		/*
		 * Page featured images
		 */

		$wp_customize->add_setting(
			'aureon_blog_settings[page_post_image]',
			array(
				'default' => $defaults['page_post_image'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[page_post_image]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display featured images', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[page_post_image]',
			)
		);

		// Padding
		$wp_customize->add_setting(
			'aureon_blog_settings[page_post_image_padding]',
			array(
				'default' => $defaults['page_post_image_padding'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[page_post_image_padding]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display padding around images', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[page_post_image_padding]',
				'active_callback' => 'aureon_premium_display_image_padding_single_page',
			)
		);

		// Location
		$wp_customize->add_setting(
			'aureon_blog_settings[page_post_image_position]',
			array(
				'default' => $defaults['page_post_image_position'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[page_post_image_position]',
			array(
				'type' => 'select',
				'label' => __( 'Location', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'choices' => array(
					'below-title' => __( 'Below Title', 'aureon-studio' ),
					'inside-content' => __( 'Above Title', 'aureon-studio' ),
					'above-content' => __( 'Above Content Area', 'aureon-studio' ),
				),
				'settings' => 'aureon_blog_settings[page_post_image_position]',
				'active_callback' => 'aureon_premium_single_page_featured_image_active',
			)
		);

		// Alignment
		$wp_customize->add_setting(
			'aureon_blog_settings[page_post_image_alignment]',
			array(
				'default' => $defaults['page_post_image_alignment'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[page_post_image_alignment]',
			array(
				'type' => 'select',
				'label' => __( 'Alignment', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'choices' => array(
					'center' => __( 'Center', 'aureon-studio' ),
					'left' => __( 'Left', 'aureon-studio' ),
					'right' => __( 'Right', 'aureon-studio' ),
				),
				'settings' => 'aureon_blog_settings[page_post_image_alignment]',
				'active_callback' => 'aureon_premium_single_page_featured_image_active',
			)
		);

		$wp_customize->add_setting(
			'aureon_blog_settings[page_post_image_size]',
			array(
				'default' => $defaults['page_post_image_size'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[page_post_image_size]',
			array(
				'type' => 'select',
				'label' => __( 'Media Attachment Size', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'choices' => aureon_blog_get_image_sizes(),
				'settings' => 'aureon_blog_settings[page_post_image_size]',
				'active_callback' => 'aureon_premium_single_page_featured_image_active',
			)
		);

		// Width
		$wp_customize->add_setting(
			'aureon_blog_settings[page_post_image_width]', array(
				'default' => $defaults['page_post_image_width'],
				'capability' => 'edit_theme_options',
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_empty_absint',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[page_post_image_width]',
			array(
				'type' => 'number',
				'label' => __( 'Width', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[page_post_image_width]',
				'active_callback' => 'aureon_premium_single_page_featured_image_active',
			)
		);

		// Height
		$wp_customize->add_setting(
			'aureon_blog_settings[page_post_image_height]', array(
				'default' => $defaults['page_post_image_height'],
				'capability' => 'edit_theme_options',
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_empty_absint',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[page_post_image_height]',
			array(
				'type' => 'number',
				'label' => __( 'Height', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[page_post_image_height]',
				'active_callback' => 'aureon_premium_single_page_featured_image_active',
			)
		);

		$wp_customize->add_control(
			new Aureon_Information_Customize_Control(
				$wp_customize,
				'aureon_regenerate_page_images_notice',
				array(
					'section'     => 'aureon_blog_section',
					'description' => sprintf(
						__( 'We will attempt to serve exact image sizes based on your width/height settings. If that is not possible, we will resize your images using CSS. Learn more about featured image sizing %s.', 'aureon-studio' ),
						'<a href="https://docs.aureonstudio.com/article/adjusting-the-featured-images/" target="_blank" rel="noopener noreferrer">' . __( 'here', 'aureon-studio' ) . '</a>'
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'active_callback' => 'aureon_premium_single_page_featured_image_active',
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_blog_columns_title',
				array(
					'section' => 'aureon_blog_section',
					'type' => 'aureon-customizer-title',
					'title'	=> __( 'Columns', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		// Enable columns
		$wp_customize->add_setting(
			'aureon_blog_settings[column_layout]',
			array(
				'default' => $defaults['column_layout'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[column_layout]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display posts in columns', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[column_layout]',
			)
		);

		// Column count class
		$wp_customize->add_setting(
			'aureon_blog_settings[columns]',
			array(
				'default' => $defaults['columns'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[columns]',
			array(
				'type' => 'select',
				'label' => __( 'Columns', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'choices' => array(
					'50' => '2',
					'33' => '3',
					'25' => '4',
					'20' => '5'
				),
				'settings' => 'aureon_blog_settings[columns]',
				'active_callback' => 'aureon_premium_blog_columns_active',
			)
		);

		// Featured column
		$wp_customize->add_setting(
			'aureon_blog_settings[featured_column]',
			array(
				'default' => $defaults['featured_column'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[featured_column]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Make first post featured', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[featured_column]',
				'active_callback' => 'aureon_premium_blog_columns_active',
			)
		);

		// Masonry
		$wp_customize->add_setting(
			'aureon_blog_settings[masonry]',
			array(
				'default' => $defaults['masonry'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_blog_settings[masonry]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Display posts in masonry grid', 'aureon-studio' ),
				'section' => 'aureon_blog_section',
				'settings' => 'aureon_blog_settings[masonry]',
				'active_callback' => 'aureon_premium_blog_columns_active',
			)
		);
	}
}

add_action( 'customize_controls_print_styles', 'aureon_blog_customizer_controls_css' );

function aureon_blog_customizer_controls_css() {
	?>
	<style>
		#customize-control-aureon_blog_settings-post_image_width .customize-control-title:after,
		#customize-control-aureon_blog_settings-post_image_height .customize-control-title:after,
		#customize-control-aureon_blog_settings-single_post_image_width .customize-control-title:after,
		#customize-control-aureon_blog_settings-single_post_image_height .customize-control-title:after,
		#customize-control-aureon_blog_settings-page_post_image_width .customize-control-title:after,
		#customize-control-aureon_blog_settings-page_post_image_height .customize-control-title:after {
			content: "px";
			width: 22px;
			display: inline-block;
			background: #FFF;
			height: 18px;
			border: 1px solid #DDD;
			text-align: center;
			text-transform: uppercase;
			font-size: 10px;
			line-height: 18px;
			margin-left: 5px
		}

		#customize-control-aureon_regenerate_images_notice p,
		#customize-control-aureon_regenerate_single_post_images_notice p,
		#customize-control-aureon_regenerate_page_images_notice p {
			margin-top: 0;
		}
	</style>
	<?php
}

add_action( 'customize_controls_enqueue_scripts', 'aureon_blog_customizer_control_scripts' );

function aureon_blog_customizer_control_scripts() {
	wp_enqueue_script( 'aureon-blog-customizer-control-scripts', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/controls.js', array( 'jquery','customize-controls' ), AUREON_BLOG_VERSION, true );
}

if ( ! function_exists( 'aureon_blog_customizer_live_preview' ) ) {
	add_action( 'customize_preview_init', 'aureon_blog_customizer_live_preview' );
	/**
	 * Add our live preview javascript
	 */
	function aureon_blog_customizer_live_preview() {
		wp_enqueue_script(
			'aureon-blog-themecustomizer',
			trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/customizer.js',
			array( 'jquery', 'customize-preview', 'aureon-blog' ),
			AUREON_BLOG_VERSION,
			true
		);
	}
}
