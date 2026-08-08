<?php
/**
 * Hero adapter — maps Customizer slides to hero/slider component data.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_hero() {
	$slides = aureon_get_option( 'aether_hero_slides' );

	// Customizer stores the repeater as JSON; tokens default is a PHP array.
	if ( is_string( $slides ) && '' !== trim( $slides ) ) {
		$slides = json_decode( $slides, true );
	}

	$data = array();

	if ( empty( $slides ) || ! is_array( $slides ) ) {
		return array( 'slides' => array() );
	}

	foreach ( $slides as $slide ) {
		if ( ! is_array( $slide ) ) {
			continue;
		}

		// Normalize both shapes: editor repeater (title/subtitle/cta/url)
		// and legacy/phantom shape (headline/subline/buttons).
		$title    = isset( $slide['title'] )    ? $slide['title']    : ( isset( $slide['headline'] ) ? $slide['headline'] : '' );
		$subtitle = isset( $slide['subtitle'] ) ? $slide['subtitle'] : ( isset( $slide['subline'] )  ? $slide['subline']  : '' );
		$image    = isset( $slide['image'] ) ? $slide['image'] : '';
		$alt      = isset( $slide['alt'] )      ? sanitize_text_field( $slide['alt'] ) : ( isset( $slide['label'] ) ? sanitize_text_field( $slide['label'] ) : '' );
		$accent   = isset( $slide['accent'] )   ? sanitize_text_field( $slide['accent'] ) : '';

		$buttons = array();
		if ( ! empty( $slide['buttons'] ) && is_array( $slide['buttons'] ) ) {
			$buttons = $slide['buttons'];
		} elseif ( ! empty( $slide['cta'] ) ) {
			$buttons[] = array(
				'label' => $slide['cta'],
				'url'   => isset( $slide['url'] ) ? esc_url_raw( $slide['url'] ) : '',
				'style' => 'primary',
			);
		}

		$data[] = array(
			'headline' => sanitize_text_field( $title ),
			'accent'   => $accent,
			'subline'  => sanitize_text_field( $subtitle ),
			'image'    => aether_viewmodel_resolve_image( $image ),
			'alt'      => $alt,
			'buttons'  => $buttons,
		);
	}

	return array(
		'slides'   => $data,
		'behavior' => array( 'parallax-section' => true ),
	);
}
