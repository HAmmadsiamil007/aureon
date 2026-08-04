<?php
/**
 * Phantom Core — supported plugin bridge matrix (Phase 8).
 *
 * Mirrors docs/plugins.md. Each entry: name, capabilities, plugin file path
 * (for HealthCheck), optional minimum version.
 *
 * @package Phantom\Core\Bridges\Config
 * @since 0.8.0
 */

declare( strict_types=1 );

return array(
	'acf'         => array(
		'name'         => 'Advanced Custom Fields',
		'plugin'       => 'advanced-custom-fields/acf.php',
		'capabilities' => array( 'fields', 'sub_fields', 'image', 'group', 'repeater' ),
	),
	'rankmath'    => array(
		'name'         => 'Rank Math SEO',
		'plugin'       => 'seo-by-rank-math/rank-math.php',
		'capabilities' => array( 'meta_title', 'meta_description', 'robots' ),
	),
	'yoast'       => array(
		'name'         => 'Yoast SEO',
		'plugin'       => 'wordpress-seo/wp-seo.php',
		'capabilities' => array( 'meta_title', 'meta_description', 'canonical' ),
	),
	'wpml'        => array(
		'name'         => 'WPML',
		'plugin'       => 'sitepress-multilingual-cms/sitepress.php',
		'capabilities' => array( 'locale', 'languages', 'is_translated' ),
	),
	'polylang'    => array(
		'name'         => 'Polylang',
		'plugin'       => 'polylang/polylang.php',
		'capabilities' => array( 'locale', 'languages', 'is_translated' ),
	),
	'fluentforms' => array(
		'name'         => 'Fluent Forms',
		'plugin'       => 'fluentform/fluentform.php',
		'capabilities' => array( 'embed' ),
	),
	'gravity'     => array(
		'name'         => 'Gravity Forms',
		'plugin'       => 'gravityforms/gravityforms.php',
		'capabilities' => array( 'embed', 'enqueue_assets' ),
	),
	'wpforms'     => array(
		'name'         => 'WPForms',
		'plugin'       => 'wpforms-lite/wpforms.php',
		'capabilities' => array( 'embed' ),
	),
	'buddypress'  => array(
		'name'         => 'BuddyPress',
		'plugin'       => 'buddypress/bp-loader.php',
		'capabilities' => array( 'avatar', 'profile_url' ),
	),
	'bbpress'     => array(
		'name'         => 'bbPress',
		'plugin'       => 'bbpress/bbpress.php',
		'capabilities' => array( 'is_topic', 'is_reply', 'forum_url' ),
	),
	'learndash'   => array(
		'name'         => 'LearnDash',
		'plugin'       => 'sfwd-lms/sfwd_lms.php',
		'capabilities' => array( 'course_id', 'enrollment_status' ),
	),
	'tec'         => array(
		'name'         => 'The Events Calendar',
		'plugin'       => 'the-events-calendar/the-events-calendar.php',
		'capabilities' => array( 'events', 'ticket_count' ),
	),
);
