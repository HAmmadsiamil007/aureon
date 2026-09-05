<?php
/**
 * This file adds global scripts.
 *
 * @since 2.0.0
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

add_action( 'enqueue_block_editor_assets', 'aureon_premium_enqueue_editor_scripts' );
/**
 * Add scripts to the non-Elements block editor.
 *
 * @since 2.0.0
 */
function aureon_premium_enqueue_editor_scripts() {
	global $pagenow;

	$deps = array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' );

	if ( 'widgets.php' === $pagenow ) {
		unset( $deps[3] );
	}

	wp_enqueue_script(
		'aureon-studio-editor',
		AUREON_STUDIO_DIR_URL . 'dist/editor.js',
		$deps,
		filemtime( AUREON_STUDIO_DIR_PATH . 'dist/editor.js' ),
		true
	);

	wp_set_script_translations( 'aureon-studio-editor', 'aureon-studio', AUREON_STUDIO_DIR_PATH . 'langs' );

	global $aureon_elements;
	$active_elements = array();

	if ( class_exists( 'Aureon_Elements_Helper' ) && ! empty( $aureon_elements ) ) {
		foreach ( (array) $aureon_elements as $key => $data ) {
			$type = esc_html( Aureon_Elements_Helper::get_element_type_label( $data['type'] ) );

			$active_elements[] = array(
				'type' => $type,
				'name' => get_the_title( $data['id'] ),
				'url'  => get_edit_post_link( $data['id'] ),
			);
		}
	}

	$post_type_is_public = false;

	if ( get_post_type() ) {
		$post_type = get_post_type_object( get_post_type() );

		if ( is_object( $post_type ) && ! empty( $post_type->public ) ) {
			$post_type_is_public = true;
		}
	}

	wp_localize_script(
		'aureon-studio-editor',
		'aureonStudioEditor',
		array(
			'isBlockElement' => 'aureon_elements' === get_post_type(),
			'activeElements' => $active_elements,
			'elementsUrl' => esc_url( admin_url( 'edit.php?post_type=aureon_elements' ) ),
			'postTypeIsPublic' => $post_type_is_public,
		)
	);

	wp_enqueue_style(
		'aureon-studio-editor',
		AUREON_STUDIO_DIR_URL . 'dist/editor.css',
		array( 'wp-edit-blocks' ),
		filemtime( AUREON_STUDIO_DIR_PATH . 'dist/editor.css' )
	);
}
