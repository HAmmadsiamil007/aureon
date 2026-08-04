<?php
/**
 * Search form — WP searchform template (standalone theme shell).
 *
 * Phase 16 (Safe Rebranding): original markup, never derived from a parent
 * theme. Accessible, escaped search form rendered by get_search_form().
 *
 * @package Lumina
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="lumina-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="lumina-search-form__label" for="lumina-search-field">
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'lumina' ); ?></span>
	</label>
	<input
		type="search"
		id="lumina-search-field"
		class="lumina-search-form__input"
		placeholder="<?php esc_attr_e( 'Search …', 'lumina' ); ?>"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		name="s"
	/>
	<button type="submit" class="lumina-search-form__submit"><?php esc_html_e( 'Search', 'lumina' ); ?></button>
</form>
