jQuery( function( $ ) {
	// Featured image controls
	var featuredImageArchiveControls = [
		'aureon_blog_settings-post_image',
		'aureon_blog_settings-post_image_padding',
		'aureon_blog_settings-post_image_position',
		'aureon_blog_settings-post_image_alignment',
		'aureon_blog_settings-post_image_size',
		'aureon_blog_settings-post_image_width',
		'aureon_blog_settings-post_image_height',
		'aureon_regenerate_images_notice',
	];

	$.each( featuredImageArchiveControls, function( index, value ) {
		$( '#customize-control-' + value ).attr( 'data-control-section', 'featured-image-archives' );
	} );

	var featuredImageSingleControls = [
		'aureon_blog_settings-single_post_image',
		'aureon_blog_settings-single_post_image_padding',
		'aureon_blog_settings-single_post_image_position',
		'aureon_blog_settings-single_post_image_alignment',
		'aureon_blog_settings-single_post_image_size',
		'aureon_blog_settings-single_post_image_width',
		'aureon_blog_settings-single_post_image_height',
		'aureon_regenerate_single_post_images_notice',
	];

	$.each( featuredImageSingleControls, function( index, value ) {
		$( '#customize-control-' + value ).attr( 'data-control-section', 'featured-image-single' ).css( {
			visibility: 'hidden',
			height: '0',
			width: '0',
			margin: '0',
			overflow: 'hidden',
		} );
	} );

	var featuredImagePageControls = [
		'aureon_blog_settings-page_post_image',
		'aureon_blog_settings-page_post_image_padding',
		'aureon_blog_settings-page_post_image_position',
		'aureon_blog_settings-page_post_image_alignment',
		'aureon_blog_settings-page_post_image_size',
		'aureon_blog_settings-page_post_image_width',
		'aureon_blog_settings-page_post_image_height',
		'aureon_regenerate_page_images_notice',
	];

	$.each( featuredImagePageControls, function( index, value ) {
		$( '#customize-control-' + value ).attr( 'data-control-section', 'featured-image-page' ).css( {
			visibility: 'hidden',
			height: '0',
			width: '0',
			margin: '0',
			overflow: 'hidden',
		} );
	} );

	// Post meta controls
	var postMetaArchiveControls = [
		'aureon_settings-post_content',
		'aureon_blog_settings-excerpt_length',
		'aureon_blog_settings-read_more',
		'aureon_blog_settings-read_more_button',
		'aureon_blog_settings-date',
		'aureon_blog_settings-author',
		'aureon_blog_settings-categories',
		'aureon_blog_settings-tags',
		'aureon_blog_settings-comments',
		'aureon_blog_settings-infinite_scroll',
		'aureon_blog_settings-infinite_scroll_button',
		'blog_masonry_load_more_control',
		'blog_masonry_loading_control',
	];

	$.each( postMetaArchiveControls, function( index, value ) {
		$( '#customize-control-' + value ).attr( 'data-control-section', 'post-meta-archives' );
	} );

	var postMetaSingleControls = [
		'aureon_blog_settings-single_date',
		'aureon_blog_settings-single_author',
		'aureon_blog_settings-single_categories',
		'aureon_blog_settings-single_tags',
		'aureon_blog_settings-single_post_navigation',
	];

	$.each( postMetaSingleControls, function( index, value ) {
		$( '#customize-control-' + value ).attr( 'data-control-section', 'post-meta-single' ).css( {
			visibility: 'hidden',
			height: '0',
			width: '0',
			margin: '0',
			overflow: 'hidden',
		} );
	} );
} );
