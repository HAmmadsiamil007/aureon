<?php
/**
 * Ferm Living author bio — author card under articles.
 *
 * Key:    'content/author-bio' (override)
 * Source: fermliving.com article structure
 * Props:  author, description, avatar.
 * Contract: keeps .article-author-bio, [data-phantom="author_bio"] —
 *           platform article JS operates unchanged.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$author      = isset( $componentData['author'] ) ? $componentData['author'] : '';
$description = isset( $componentData['description'] ) ? $componentData['description'] : '';
$avatar      = isset( $componentData['avatar'] ) ? $componentData['avatar'] : '';

if ( empty( $description ) ) {
	$description = 'Written by the Ferm Living team.';
}
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
