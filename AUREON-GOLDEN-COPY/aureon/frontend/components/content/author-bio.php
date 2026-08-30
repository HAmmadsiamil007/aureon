<?php
/**
 * Author bio — author card under articles.
 *
 * Key:    'content/author-bio'
 * Source: single-blog.html `.author-bio`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $author       Author name. Default ''.`
 * - `string $description  Bio text. Default 'Written by the AETHER team.'.`
 * - `string $avatar       Avatar URL. Default ''.`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$author        = isset( $componentData['author'] ) ? $componentData['author'] : '';
$description   = isset( $componentData['description'] ) ? $componentData['description'] : __( 'Written by the AETHER team.', 'aureon' );
$avatar        = isset( $componentData['avatar'] ) ? $componentData['avatar'] : '';
?>
<div class="article-author-bio" data-reveal data-phantom="author_bio">
	<?php if ( $avatar ) : ?>
		<div class="author-avatar">
			<img src="<?php echo esc_url( $avatar ); ?>" alt="<?php echo esc_attr( $author ); ?>" loading="lazy">
		</div>
	<?php else : ?>
		<div class="author-avatar">
			<i class="fas fa-user"></i>
		</div>
	<?php endif; ?>
	<div class="author-info">
		<strong data-phantom="author_name"><?php echo esc_html( $author ); ?></strong>
		<p data-phantom="author_description"><?php echo esc_html( $description ); ?></p>
	</div>
</div>