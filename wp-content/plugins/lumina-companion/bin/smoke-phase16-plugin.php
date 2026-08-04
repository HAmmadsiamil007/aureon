<?php
/**
 * Phase 16 — Lumina Companion plugin smoke suite (WP-free CLI).
 *
 * Loads the plugin's main file WITHOUT a live WordPress install and asserts:
 *
 *   1. Main plugin file parses + PSR-4 autoloader registers
 *   2. Plugin::instance() singleton + boot() runs WP-free (guarded)
 *   3. All 17 modules registered (full premium parity: spacing, typography,
 *      page-header, secondary-nav, menu-plus, sections, site-library,
 *      woocommerce, colors, backgrounds, blog, copyright, disable-elements,
 *      elements, font-library, hooks, general)
 *   4. Module contract: slug() + label() on every module
 *   5. Spacing/Typography/PageHeader/MenuPlus/WooCommerce emit token-driven
 *      CSS carrying --lumina-* vars, with zero --phantom- vars
 *   6. Guarded WP hooks never throw WP-free (register/customizer no-ops)
 *   7. inject_template_data() injects expected keys without WP
 *   8. Zero GeneratePress/GP/Phantom identifiers in plugin source
 *
 * Usage: php bin/smoke-phase16-plugin.php
 * Exit code 0 = all assertions passed; 1 = any failure.
 *
 * @package Lumina\Companion
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

// WP-free option store so conditional modules (colors, backgrounds,
// font-library) can be exercised through the real settings() path.
$GLOBALS['lumina_wp_options'] = array();

if ( ! function_exists( 'get_option' ) ) {
	/**
	 * WP-free get_option stub for the smoke suite.
	 *
	 * @param string $option  Option name.
	 * @param mixed  $default Default when unset.
	 * @return mixed
	 */
	function get_option( string $option, mixed $default = false ): mixed {
		return $GLOBALS['lumina_wp_options'][ $option ] ?? $default;
	}
}

require dirname( __DIR__ ) . '/lumina-companion.php';

$passes = 0;
$fails  = 0;

/**
 * Record and print an assertion result.
 *
 * @param string $label  Assertion label.
 * @param bool   $ok     Passed?
 * @param string $detail Optional evidence.
 * @return void
 */
function check( string $label, bool $ok, string $detail = '' ): void {
	global $passes, $fails;

	if ( $ok ) {
		++$passes;
		echo "[PASS] {$label}\n";
	} else {
		++$fails;
		echo "[FAIL] {$label}" . ( '' !== $detail ? " — {$detail}" : '' ) . "\n";
	}
}

echo "== Lumina Companion smoke suite (Phase 16, WP-free) ==\n\n";

// 1. Main file + autoloader.
check( 'plugin constants defined', defined( 'LUMINA_COMPANION_VERSION' ) && '1.0.0' === LUMINA_COMPANION_VERSION );
check( 'PSR-4 resolves Plugin', class_exists( \Lumina\Companion\Plugin::class ) );

// 2. Singleton + guarded boot.
$plugin = \Lumina\Companion\Plugin::instance();
check( 'Plugin::instance() singleton', $plugin === \Lumina\Companion\Plugin::instance() );

$plugin->boot(); // must not throw WP-free.
check( 'boot() runs WP-free (guarded no-ops)', true );

// 3. Module registry.
$slugs    = $plugin->module_slugs();
$expected = array(
	'spacing', 'typography', 'page-header', 'secondary-nav', 'menu-plus',
	'sections', 'site-library', 'woocommerce', 'colors', 'backgrounds',
	'blog', 'copyright', 'disable-elements', 'elements', 'font-library',
	'hooks', 'general',
);
check( 'all 17 modules registered', array() === array_diff( $expected, $slugs ), implode( ',', $slugs ) );

// 4. Module contract.
$contract_ok = true;
foreach ( $slugs as $slug ) {
	$module = $plugin->module( $slug );
	if ( ! $module instanceof \Lumina\Companion\Modules\ModuleInterface ) {
		$contract_ok = false;
		break;
	}
	if ( '' === $module->slug() || '' === $module->label() ) {
		$contract_ok = false;
		break;
	}
}
check( 'every module satisfies ModuleInterface (slug + label)', $contract_ok );

// 5. Token-driven CSS across the full module set.
// Seed options for the conditional modules so their emission path is proven.
$GLOBALS['lumina_wp_options']['lumina_companion_colors']        = array( 'text' => '#123456', 'heading' => '#654321' );
$GLOBALS['lumina_wp_options']['lumina_companion_backgrounds']   = array( 'body_color' => '#f0f0f0' );
$GLOBALS['lumina_wp_options']['lumina_companion_font-library']  = array( 'body_font' => 'Inter' );

$spacing_css  = $plugin->module( 'spacing' )->css();
$typo_css     = $plugin->module( 'typography' )->css();
$header_css   = $plugin->module( 'page-header' )->css();
$menu_css     = $plugin->module( 'menu-plus' )->css();
$woo_css      = $plugin->module( 'woocommerce' )->css();
$colors_css   = $plugin->module( 'colors' )->css();
$backgrounds_css = $plugin->module( 'backgrounds' )->css();
$copyright_css   = $plugin->module( 'copyright' )->css();
$fonts_css    = $plugin->module( 'font-library' )->css();
$general_css  = $plugin->module( 'general' )->css();

check( 'spacing CSS emits --lumina-spacing-*', false !== strpos( $spacing_css, '--lumina-spacing-container' ) );
check( 'typography CSS emits --lumina-typography-*', false !== strpos( $typo_css, '--lumina-typography-font-sans' ) );
check( 'page-header CSS targets .lumina-page-header', false !== strpos( $header_css, '.lumina-page-header' ) );
check( 'menu-plus CSS targets .lumina-mega', false !== strpos( $menu_css, '.lumina-mega' ) );
check( 'woocommerce CSS emits --lumina-woo-columns', false !== strpos( $woo_css, '--lumina-woo-columns' ) );
check( 'colors CSS emits --lumina-color-*', false !== strpos( $colors_css, '--lumina-color-text' ) );
check( 'backgrounds CSS emits --lumina-bg-*', false !== strpos( $backgrounds_css, '--lumina-bg-body' ) );
check( 'copyright CSS targets .lumina-copyright', false !== strpos( $copyright_css, '.lumina-copyright' ) );
check( 'font-library CSS emits --lumina-font-*', false !== strpos( $fonts_css, '--lumina-font-body' ) );
check( 'general CSS emits --lumina-container', false !== strpos( $general_css, '--lumina-container' ) );

$all_css = $spacing_css . $typo_css . $header_css . $menu_css . $woo_css . $colors_css . $backgrounds_css . $copyright_css . $fonts_css . $general_css;
check( 'no --phantom- vars anywhere in module CSS', false === strpos( $all_css, '--phantom-' ) );

// 6. Guarded WP hooks WP-free.
$plugin->register_customizer( new stdClass() ); // must not throw.
check( 'customizer() guarded WP-free (no-op)', true );

// 7. Template data injection.
$data = $plugin->inject_template_data( array(), 'header' );
check( 'template_data() injects spacing on header slug', isset( $data['spacing'] ) && is_array( $data['spacing'] ) );
check( 'template_data() injects page_header on any slug', isset( $data['page_header'] ) && true === $data['page_header']['enabled'] );
check( 'template_data() injects site_library', isset( $data['site_library'] ) && true === $data['site_library']['enabled'] );
check( 'template_data() injects disable flags', isset( $data['disable'] ) && is_array( $data['disable'] ) );
check( 'template_data() injects blog settings on blog slug', isset( $plugin->inject_template_data( array(), 'blog' )['blog'] ) && is_array( $plugin->inject_template_data( array(), 'blog' )['blog'] ) );
check( 'template_data() injects copyright on footer slug', isset( $plugin->inject_template_data( array(), 'footer' )['copyright'] ) && is_array( $plugin->inject_template_data( array(), 'footer' )['copyright'] ) );
check( 'template_data() injects general on home slug', isset( $plugin->inject_template_data( array(), 'home' )['general'] ) && is_array( $plugin->inject_template_data( array(), 'home' )['general'] ) );

// 8. Zero forbidden identifiers in plugin source.
$forbidden = 0;
$rii       = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( dirname( __DIR__ ) . '/src', FilesystemIterator::SKIP_DOTS ) );

foreach ( $rii as $file ) {
	if ( 'php' !== strtolower( $file->getExtension() ) ) {
		continue;
	}

	$content = strtolower( (string) file_get_contents( $file->getPathname() ) );

	if ( false !== strpos( $content, 'generatepress' ) || false !== strpos( $content, 'gp_premium' ) || false !== strpos( $content, 'phantom' ) ) {
		++$forbidden;
		echo '  [forbidden-ref] ' . $file->getPathname() . PHP_EOL;
	}
}
check( 'zero GeneratePress/GP Premium/Phantom identifiers in src/', 0 === $forbidden );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
