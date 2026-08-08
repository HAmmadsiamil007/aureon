<?php
/**
 * Component manifest — single source of truth for component templates.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	// Shell.
	'shell/preloader'       => array( 'template' => 'components/shell/preloader.php' ),
	'shell/fog'             => array( 'template' => 'components/shell/fog.php' ),
	'shell/skip-link'       => array( 'template' => 'components/shell/skip-link.php' ),
	'shell/announcement'    => array( 'template' => 'components/shell/announcement.php' ),
	'shell/header'          => array( 'template' => 'components/shell/header.php' ),
	'shell/mobile-chrome'   => array( 'template' => 'components/shell/mobile-chrome.php' ),
	'shell/footer'          => array( 'template' => 'components/shell/footer.php' ),
	// Hero.
	'hero/slider'           => array( 'template' => 'components/hero/slider.php' ),
	'hero/slide'            => array( 'template' => 'components/hero/slide.php' ),
	'hero/page-title'       => array( 'template' => 'components/hero/page-title.php' ),
	'hero/page-banner'      => array( 'template' => 'components/hero/page-banner.php' ),
	// Section.
	'section/header'        => array( 'template' => 'components/section/header.php' ),
	'section/filter-bar'    => array( 'template' => 'components/section/filter-bar.php' ),
	'section/accordion'     => array( 'template' => 'components/section/accordion.php' ),
	'section/cta'           => array( 'template' => 'components/section/cta.php' ),
	'section/newsletter'    => array( 'template' => 'components/section/newsletter.php' ),
	'section/pagination'    => array( 'template' => 'components/section/pagination.php' ),
	// Cards.
	'card/product'          => array( 'template' => 'components/cards/product.php' ),
	'card/category'         => array( 'template' => 'components/cards/category.php' ),
	'card/blog'             => array( 'template' => 'components/cards/blog.php' ),
	'card/review'           => array( 'template' => 'components/cards/review.php' ),
	'card/team'             => array( 'template' => 'components/cards/team.php' ),
	'card/wishlist'         => array( 'template' => 'components/cards/wishlist.php' ),
	// Cart / Checkout / Account.
	'cart/items'            => array( 'template' => 'components/cart/items.php' ),
	'cart/summary'          => array( 'template' => 'components/cart/summary.php' ),
	'checkout/order-items'  => array( 'template' => 'components/checkout/order-items.php' ),
	'account/profile'       => array( 'template' => 'components/account/profile.php' ),
	'account/orders'        => array( 'template' => 'components/account/orders.php' ),
	// Auth.
	'auth/password-strength' => array( 'template' => 'components/auth/password-strength.php' ),
	// Order.
	'order/confirmation'    => array( 'template' => 'components/order/confirmation.php' ),
	// Commerce.
	'commerce/rating'       => array( 'template' => 'components/commerce/rating.php' ),
	'commerce/quick-view'   => array( 'template' => 'components/commerce/quick-view.php' ),
	'product/breadcrumb'    => array( 'template' => 'components/product/breadcrumb.php' ),
	'product/gallery'       => array( 'template' => 'components/product/gallery.php' ),
	'product/info'          => array( 'template' => 'components/product/info.php' ),
	'product/sticky-bar'    => array( 'template' => 'components/product/sticky-bar.php' ),
	'product/specs'         => array( 'template' => 'components/product/specs.php' ),
	'product/reviews'       => array( 'template' => 'components/product/reviews.php' ),
	'product/related'       => array( 'template' => 'components/product/related.php' ),
	'product/size-guide'    => array( 'template' => 'components/product/size-guide.php' ),
	// Content.
	'content/page'          => array( 'template' => 'components/content/page.php' ),
	'content/article-hero'  => array( 'template' => 'components/content/article-hero.php' ),
	'content/article-meta'  => array( 'template' => 'components/content/article-meta.php' ),
	'content/article-body'  => array( 'template' => 'components/content/article-body.php' ),
	'content/author-bio'    => array( 'template' => 'components/content/author-bio.php' ),
	'content/story'         => array( 'template' => 'components/content/story.php' ),
	// Forms.
	'form/contact'          => array( 'template' => 'components/forms/contact.php' ),
	'form/login'            => array( 'template' => 'components/forms/login.php' ),
	'form/register'         => array( 'template' => 'components/forms/register.php' ),
	'form/newsletter'       => array( 'template' => 'components/forms/newsletter.php' ),
	'form/forgot-password'  => array( 'template' => 'components/forms/forgot-password.php' ),
	// Utility.
	'error/404'             => array( 'template' => 'components/utility/error-404.php' ),
	'soon/countdown'        => array( 'template' => 'components/utility/countdown.php' ),
);
