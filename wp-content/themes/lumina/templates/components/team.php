<?php
/**
 * Team — member cards.
 *
 * Expected data: title, members (name, role, photo, bio).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<section class="lumina-team">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="lumina-team__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<div class="lumina-team__grid">
		<?php foreach ( (array) $view->prop( 'members', array() ) as $member ) : ?>
			<article class="lumina-team__member" data-lumina-anim="reveal">
				<?php if ( ! empty( $member['photo'] ) ) : ?>
					<img class="lumina-team__photo" src="<?php echo $view->url( $member['photo'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="<?php echo $view->attr( $member['name'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" loading="lazy" />
				<?php endif; ?>
				<h3 class="lumina-team__name"><?php echo $view->e( $member['name'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h3>
				<?php if ( ! empty( $member['role'] ) ) : ?>
					<p class="lumina-team__role"><?php echo $view->e( $member['role'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $member['bio'] ) ) : ?>
					<p class="lumina-team__bio"><?php echo $view->e( $member['bio'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
</section>
