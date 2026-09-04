<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="aureon_sections_control">

		<?php
		global $post;
	    $use_sections = get_post_meta( $post->ID, '_aureon_use_sections', true );
        //$use_sections = isset( $use_sections['use_sections'] ) && 'true' == $use_sections['use_sections'] ? true : false;
        wp_nonce_field( 'aureon_sections_use_sections_nonce', '_aureon_sections_use_sections_nonce' );
		?>
		<label for="_aureon_use_sections[use_sections]">
			<input type="checkbox" class="use-sections-switch" name="_aureon_use_sections[use_sections]" id="_aureon_use_sections[use_sections]" value="true" <?php if ( isset ( $use_sections['use_sections'] ) ) checked( $use_sections['use_sections'], 'true', true );?> />
			<?php _e( 'Use Sections', 'aureon-studio' ); ?>
		</label>
</div>
