<?php
/**
 * Quick view modal — overlay dialog filled by JS with AJAX product data.
 *
 * Key:    'commerce/quick-view'
 * Source: shop.html quick-view trigger buttons (no modal in source — engine-built)
 *
 * Props:  none (markup is a shell; data arrives via aether_quick_view AJAX)
 *
 * Slots:  none
 * Variants: none
 * Tokens:  uses --void/--surface/--chrome/--gold custom props only.
 *
 * @package Aureon
 */
?>
<div class="quick-view-modal" id="aetherQuickView" role="dialog" aria-modal="true" aria-labelledby="aetherQuickViewTitle" hidden>
	<div class="quick-view-backdrop" data-quickview-close></div>
	<div class="quick-view-panel" role="document">
		<button class="quick-view-close" type="button" data-quickview-close aria-label="<?php esc_attr_e( 'Close', 'aureon' ); ?>"><i class="fas fa-times"></i></button>
		<div class="quick-view-body" data-quickview-body>
			<p class="quick-view-loading"><?php esc_html_e( 'Loading…', 'aureon' ); ?></p>
		</div>
	</div>
</div>
