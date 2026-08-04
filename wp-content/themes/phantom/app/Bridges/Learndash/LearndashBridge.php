<?php
/**
 * LearndashBridge — LearnDash capability adapter.
 *
 * Phase 8 (Plugin Bridges): course id + enrollment status through the public
 * LearnDash API, capability-guarded; safe defaults when absent.
 *
 * @package Phantom\Core\Bridges\Learndash
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Bridges\Learndash;

use Phantom\Core\Bridges\Bridge;

/**
 * LearnDash adapter.
 */
final class LearndashBridge extends Bridge {

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'learndash';
	}

	/**
	 * {@inheritDoc}
	 */
	public function name(): string {
		return 'LearnDash';
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_active(): bool {
		return $this->guard( 'SFWD_LMS' ) || defined( 'LEARNDASH_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->constant_version( 'LEARNDASH_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function capabilities(): array {
		return array( 'course_id', 'enrollment_status' );
	}

	/**
	 * Course id for a post id (0 when none).
	 *
	 * @param int $post_id Post id.
	 * @return int
	 */
	public function course_id( int $post_id ): int {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- LearnDash core function.
		if ( $post_id <= 0 || ! function_exists( 'learndash_get_course_id' ) ) {
			return 0;
		}

		return (int) learndash_get_course_id( $post_id );
	}

	/**
	 * Enrollment status for a user in a course.
	 *
	 * @param int $user_id   User id.
	 * @param int $course_id Course id.
	 * @return string
	 */
	public function enrollment_status( int $user_id, int $course_id ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- LearnDash core function.
		if ( $user_id <= 0 || $course_id <= 0 || ! function_exists( 'learndash_is_user_enrolled' ) ) {
			return 'not_enrolled';
		}

		return learndash_is_user_enrolled( $user_id, $course_id ) ? 'enrolled' : 'not_enrolled';
	}
}
