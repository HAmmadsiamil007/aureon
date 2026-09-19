<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'aureon_execute_hooks' ) ) {
	function aureon_execute_hooks( $id ) {
		$hooks = get_option( 'aureon_hooks' );

		$content = isset( $hooks[$id] ) ? $hooks[$id] : null;

		$disable = isset( $hooks[$id . '_disable'] ) ? $hooks[$id . '_disable'] : null;

		if ( ! $content || 'true' == $disable ) {
			return;
		}

		$value = do_shortcode( $content );

		// Security contract: stored content is NEVER executed as PHP. When the
		// (shortcode-processed) content is a `snippet:<id>` reference, the
		// registered, version-controlled callable runs; everything else prints
		// verbatim. A missing id prints a visible notice instead of failing
		// silently.
		$snippet_id = class_exists( 'Aureon_Snippet_Registry' ) ? Aureon_Snippet_Registry::extract_id( $value ) : '';
		if ( '' !== $snippet_id ) {
			$out = Aureon_Snippet_Registry::execute( $snippet_id );
			if ( false !== $out ) {
				echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted registry output.
				return;
			}
			echo '<!-- aureon: unknown snippet ' . esc_attr( $snippet_id ) . ' -->';
			return;
		}

		echo $value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- legacy passthrough (shortcodes/HTML), unchanged behavior.
	}
}

if ( ! function_exists( 'aureon_hooks_wp_head' ) ) {
	add_action( 'wp_head', 'aureon_hooks_wp_head' );

	function aureon_hooks_wp_head() {
		aureon_execute_hooks( 'aureon_wp_head' );
	}
}

if ( ! function_exists( 'aureon_hooks_before_header' ) ) {
	add_action( 'aureon_before_header', 'aureon_hooks_before_header', 4 );

	function aureon_hooks_before_header() {
		aureon_execute_hooks( 'aureon_before_header' );
	}
}

if ( ! function_exists( 'aureon_hooks_before_header_content' ) ) {
	add_action( 'aureon_before_header_content', 'aureon_hooks_before_header_content' );

	function aureon_hooks_before_header_content() {
		aureon_execute_hooks( 'aureon_before_header_content' );
	}
}

if ( ! function_exists( 'aureon_hooks_after_header_content' ) ) {
	add_action( 'aureon_after_header_content', 'aureon_hooks_after_header_content' );

	function aureon_hooks_after_header_content() {
		aureon_execute_hooks( 'aureon_after_header_content' );
	}
}

if ( ! function_exists( 'aureon_hooks_after_header' ) ) {
	add_action( 'aureon_after_header', 'aureon_hooks_after_header' );

	function aureon_hooks_after_header() {
		aureon_execute_hooks( 'aureon_after_header' );
	}
}

if ( ! function_exists( 'aureon_hooks_inside_main_content' ) ) {
	add_action( 'aureon_before_main_content', 'aureon_hooks_inside_main_content', 9 );

	function aureon_hooks_inside_main_content() {
		aureon_execute_hooks( 'aureon_before_main_content' );
	}
}

if ( ! function_exists( 'aureon_hooks_before_content' ) ) {
	add_action( 'aureon_before_content', 'aureon_hooks_before_content' );

	function aureon_hooks_before_content() {
		aureon_execute_hooks( 'aureon_before_content' );
	}
}

if ( ! function_exists( 'aureon_hooks_after_entry_header' ) ) {
	add_action( 'aureon_after_entry_header', 'aureon_hooks_after_entry_header' );

	function aureon_hooks_after_entry_header() {
		aureon_execute_hooks( 'aureon_after_entry_header' );
	}
}

if ( ! function_exists( 'aureon_hooks_after_content' ) ) {
	add_action( 'aureon_after_content', 'aureon_hooks_after_content' );

	function aureon_hooks_after_content() {
		aureon_execute_hooks( 'aureon_after_content' );
	}
}

if ( ! function_exists( 'aureon_hooks_before_right_sidebar_content' ) ) {
	add_action( 'aureon_before_right_sidebar_content', 'aureon_hooks_before_right_sidebar_content', 5 );

	function aureon_hooks_before_right_sidebar_content() {
		aureon_execute_hooks( 'aureon_before_right_sidebar_content' );
	}
}

if ( ! function_exists( 'aureon_hooks_after_right_sidebar_content' ) ) {
	add_action( 'aureon_after_right_sidebar_content', 'aureon_hooks_after_right_sidebar_content' );

	function aureon_hooks_after_right_sidebar_content() {
		aureon_execute_hooks( 'aureon_after_right_sidebar_content' );
	}
}

if ( ! function_exists( 'aureon_hooks_before_left_sidebar_content' ) ) {
	add_action( 'aureon_before_left_sidebar_content', 'aureon_hooks_before_left_sidebar_content', 5 );

	function aureon_hooks_before_left_sidebar_content() {
		aureon_execute_hooks( 'aureon_before_left_sidebar_content' );
	}
}

if ( ! function_exists( 'aureon_hooks_after_left_sidebar_content' ) ) {
	add_action( 'aureon_after_left_sidebar_content', 'aureon_hooks_after_left_sidebar_content' );

	function aureon_hooks_after_left_sidebar_content() {
		aureon_execute_hooks( 'aureon_after_left_sidebar_content' );
	}
}

if ( ! function_exists( 'aureon_hooks_before_footer' ) ) {
	add_action( 'aureon_before_footer', 'aureon_hooks_before_footer' );

	function aureon_hooks_before_footer() {
		aureon_execute_hooks( 'aureon_before_footer' );
	}
}

if ( ! function_exists( 'aureon_hooks_after_footer_widgets' ) ) {
	add_action( 'aureon_after_footer_widgets', 'aureon_hooks_after_footer_widgets' );

	function aureon_hooks_after_footer_widgets() {
		aureon_execute_hooks( 'aureon_after_footer_widgets' );
	}
}

if ( ! function_exists( 'aureon_hooks_before_footer_content' ) ) {
	add_action( 'aureon_before_footer_content', 'aureon_hooks_before_footer_content' );

	function aureon_hooks_before_footer_content() {
		aureon_execute_hooks( 'aureon_before_footer_content' );
	}
}

if ( ! function_exists( 'aureon_hooks_after_footer_content' ) ) {
	add_action( 'aureon_after_footer_content', 'aureon_hooks_after_footer_content' );

	function aureon_hooks_after_footer_content() {
		aureon_execute_hooks( 'aureon_after_footer_content' );
	}
}

if ( ! function_exists( 'aureon_hooks_wp_footer' ) ) {
	add_action( 'wp_footer', 'aureon_hooks_wp_footer' );

	function aureon_hooks_wp_footer() {
		aureon_execute_hooks( 'aureon_wp_footer' );
	}
}
