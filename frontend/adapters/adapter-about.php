<?php
/**
 * About adapter — mission, features, story, stats + team for the about page.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_about( $args = array() ) {
	// Marketing copy is demo content — gated so production stores with
	// aether_demo_content=false render the about page as empty sections
	// (each section template bails on empty data), matching the rest of
	// the engine (F3-1).
	$demo = (bool) aureon_get_option( 'aether_demo_content', true );

	return array(
		'label'    => __( 'Our Technology', 'aureon' ),
		'title'    => __( 'Engineered for the Void', 'aureon' ),
		'subtitle' => __( 'Performance science meets relentless innovation', 'aureon' ),
		'mission'  => $demo ? array(
			'label'  => __( 'Our Mission', 'aureon' ),
			'title'  => __( 'Born from Performance Science', 'aureon' ),
			'text'   => array(
				__( 'AETHER was founded on a single obsession: to create the most responsive running shoe ever made. Our team of biomechanical engineers and material scientists spent three years developing the Void Air cushioning system — a breakthrough that delivers 40% more energy return than traditional foam.', 'aureon' ),
				__( 'Every component of the Void Runner is engineered with purpose. From the full-length carbon fiber plate to the DWR-treated engineered mesh upper, no detail is too small when the goal is defying gravity.', 'aureon' ),
			),
			'image'  => '',
			'alt'    => __( 'AETHER Technology', 'aureon' ),
		) : array(),
		'features' => $demo ? array(
			'label' => __( 'Innovation', 'aureon' ),
			'title' => __( 'The Technology Inside', 'aureon' ),
			'items' => array(
				array(
					'icon'        => 'fa-layer-group',
					'title'       => __( 'Carbon Fiber Plate', 'aureon' ),
					'description' => __( 'Full-length carbon fiber plate provides 40% more responsive energy transfer with every stride', 'aureon' ),
				),
				array(
					'icon'        => 'fa-wind',
					'title'       => __( 'Void Air Cushioning', 'aureon' ),
					'description' => __( '40mm of pressurized air capsules deliver cloud-like comfort with explosive energy return', 'aureon' ),
				),
				array(
					'icon'        => 'fa-sliders-h',
					'title'       => __( 'Adaptive Lacing', 'aureon' ),
					'description' => __( 'Micro-adjust fit system ensures perfect lockdown whether you are sprinting or cruising', 'aureon' ),
				),
				array(
					'icon'        => 'fa-sun',
					'title'       => __( 'Reflective Weave', 'aureon' ),
					'description' => __( '360° reflective yarn woven into the upper for visibility in low light conditions', 'aureon' ),
				),
			),
		) : array(),
		'story'    => $demo ? array(
			'quote' => __( 'Founded in 2024 by performance engineers and biomechanists obsessed with defying gravity.', 'aureon' ),
		) : array(),
		'values'   => $demo ? array(
			'label' => __( 'Our Values', 'aureon' ),
			'title' => __( 'What Drives Us', 'aureon' ),
			'items' => array(
				array(
					'icon'        => 'fa-lightbulb',
					'title'       => __( 'Innovation', 'aureon' ),
					'description' => __( 'We push the boundaries of materials science and biomechanics to create products that redefine what is possible.', 'aureon' ),
				),
				array(
					'icon'        => 'fa-bolt',
					'title'       => __( 'Performance', 'aureon' ),
					'description' => __( 'Every component is engineered with purpose. We do not compromise on function for form.', 'aureon' ),
				),
				array(
					'icon'        => 'fa-leaf',
					'title'       => __( 'Sustainability', 'aureon' ),
					'description' => __( 'We are committed to zero-waste manufacturing and sustainable materials without sacrificing performance.', 'aureon' ),
				),
			),
		) : array(),
		'stats'    => $demo ? array(
			'items' => array(
				array( 'number' => '280g', 'label' => __( 'Weight', 'aureon' ) ),
				array( 'number' => '40mm', 'label' => __( 'Cushion', 'aureon' ) ),
				array( 'number' => '40%',  'label' => __( 'More Responsive', 'aureon' ) ),
				array( 'number' => '10K+', 'label' => __( 'Miles Tested', 'aureon' ) ),
			),
		) : array(),
	);
}