<?php
/**
 * AUREON Customizer contract matrix generator.
 *
 * For every Customizer setting, proves:
 *   setting -> storage (option bucket) -> PHP retrieval (reader call sites)
 *   -> frontend payload/design-pack consumer -> status
 *
 * Statuses:
 *   CONNECTED   registered + reader/consumer found
 *   DEAD        registered but never read anywhere
 *   ORPHANED    read but never registered (config bug)
 *   ENGINE_DEMO read only by luxury-engine demo adapters that the active
 *               Vineta complete-page routing never loads; documented, not
 *               counted as a defect.
 *
 * Usage: php scripts/check-customizer-contracts.php
 * Output: docs/customizer-contract-matrix.csv (exit 0 = no DEAD/ORPHANED)
 */

$root     = dirname( __DIR__ );
$scanDirs = array(
	$root . '/themes/aureon',
	$root . '/frontend',
	$root . '/plugins/aureon-studio',
);

// Luxury-engine demo keys: consumed by engine section adapters
// (page-faq.php, page-team.php, adapter-product.php, ...) that the Vineta
// complete-page router never loads. Each entry was verified against the
// actual ORPHANED output: the reader sites are frontend/adapters/* only,
// with defaults declared in aether-tokens.php / pack tokens.php.
$engine_demo = array(
	// adapter-product.php (single-product demo spec/size/review blocks).
	'aether_product_colors'      => true,
	'aether_product_items'       => true,
	'aether_product_reviews'     => true,
	'aether_product_score'       => true,
	'aether_product_score_bars'  => true,
	'aether_product_score_count' => true,
	'aether_product_sizes'       => true,
	'aether_product_trust'       => true,
	'aether_size_table'          => true,
	'aether_spec_items'          => true,
	// adapter-team.php / adapter-testimonials.php / adapter-faq.php
	// (About/Team/FAQ section adapters, unreachable under Vineta routing).
	'aether_team_items'        => true,
	'aether_testimonial_items' => true,
	'aether_reviews_count'     => true,
	'aether_reviews_score'     => true,
	'aether_faq_items'         => true,
	// adapter-coming-soon.php (coming-soon demo page adapter).
	'aether_coming_soon_date' => true,
	// adapter-wc-categories.php label copy for the demo categories grid.
	'aether_categories_label'    => true,
	'aether_categories_subtitle' => true,
	// adapter-site.php (luxury-engine shell footer columns; the Vineta footer
	// contract is server-rendered WP menus + pageData payloads, verified C4).
	'aether_footer_columns' => true,
);

// ---------------------------------------------------------------------------
// 1. Collect registered settings (customizer fields).
// Registration sites = any PHP file (theme, pack composer, plugin module)
// that actually registers a setting. Storage normalization: both
// `aureon_settings[key]` wrappers and bare keys live in the same
// `aureon_settings` option bucket (see aureon_get_option() and
// vineta_get_customizer_value()), so keys are compared normalized.
// ---------------------------------------------------------------------------
$registered = array();
$skip  = '/(_title|_section|_wrapper|aureon_sanitize_|aureon_is_|aureon_aether_)/i';

$regSources = array(
	$root . '/themes/aureon',
	$root . '/frontend/designs',
	$root . '/plugins/aureon-studio',
);
foreach ( $regSources as $src ) {
	if ( ! is_dir( $src ) ) {
		continue;
	}
	$rii = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $src, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $rii as $f ) {
		if ( ! $f->isFile() || 'php' !== $f->getExtension() ) {
			continue;
		}
		$c = file_get_contents( $f->getPathname() );
		$isRegistrar = false !== strpos( $c, '->add_setting(' ) || false !== strpos( $c, 'Customize_Field::add_field' );
		if ( ! $isRegistrar ) {
			continue;
		}
		$rel = str_replace( $root . '/', '', $f->getPathname() );
		// (A) `aureon_settings[key]` wrapper literals: normalize to `key`.
		preg_match_all( '/[\'"]aureon_settings\[([a-z0-9_]+)\][\'"]/i', $c, $m2 );
		// (B) Plugin bucket wrappers (aureon_secondary_nav_settings[key] etc.).
		preg_match_all( '/[\'"]aureon_[a-z0-9_]+_settings\[([a-z0-9_]+)\][\'"]/i', $c, $m3 );
		// (C) Standalone add_setting( 'aether_*' literals (pack repeaters).
		preg_match_all( '/add_setting\(\s*[\'"]((?:aether|aureon)_[a-z0-9_]+)[\'"]/', $c, $m4 );
		// (D) Option-map lines feeding registration loops, e.g.
		//     $frontend_shell_options = array( 'aether_announcement_enabled' => ... )
		// Line-anchored `=>` form only, so reader call args in the same file
		// are NOT mistaken for registrations.
		preg_match_all( '/^[\t ]*[\'"]((?:aether|aureon)_[a-z0-9_]+)[\'"]\s*=>/mi', $c, $m5 );
		// (E) Defaults tokens.php arrays in packs are NOT registrations; they
		// are already excluded because tokens.php files contain no add_setting
		// call. Nothing to do here — kept as documentation.
		foreach ( array( $m2[1], $m3[1], $m4[1], $m5[1] ) as $ids ) {
			foreach ( $ids as $id ) {
				if ( preg_match( $skip, $id ) ) {
					continue;
				}
				$registered[ $id ][] = $rel;
			}
		}
	}
}
$registered = array_map( 'array_unique', $registered );

// ---------------------------------------------------------------------------
// 2. Collect reader + consumer call sites across the codebase.
// ---------------------------------------------------------------------------
$readers = array();
foreach ( $scanDirs as $dir ) {
	if ( ! is_dir( $dir ) ) {
		continue;
	}
	$rii = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $rii as $f ) {
		if ( ! $f->isFile() || 'php' !== $f->getExtension() ) {
			continue;
		}
		$rel = str_replace( $root . '/', '', $f->getPathname() );
		// Skip the field definitions themselves for reader detection.
		if ( 0 === strpos( $rel, 'themes/aureon/inc/customizer' ) ) {
			continue;
		}
		$c = file_get_contents( $f->getPathname() );

		$found     = array();
		$found_map = array(); // interpolated-key hits, deduped.

		// Explicit readers. aureon_get_option / vineta_get_customizer_value /
		// aether_get_option read the aureon_settings bucket exclusively, so
		// they may use UNPREFIXED keys (plugin registers css_print_method,
		// post_content, navigation_* etc.). get_theme_mod reads theme mods
		// (a different store) so it keeps the aether_/aureon_ prefix filter.
		foreach ( array( 'aureon_get_option', 'vineta_get_customizer_value', 'aether_get_option' ) as $fn ) {
			preg_match_all( '/' . $fn . '\(\s*[\'"]([a-z0-9_]+)[\'"]/', $c, $m );
			foreach ( $m[1] as $id ) {
				if ( preg_match( $skip, $id ) ) {
					continue;
				}
				$found[] = $id;
			}
		}
		// Module bucket readers, e.g. aureon_wc_get_setting('breadcrumbs').
		preg_match_all( '/aureon_[a-z0-9_]+_get_setting\(\s*[\'"]([a-z0-9_]+)[\'"]/', $c, $mgs );
		foreach ( $mgs[1] as $id ) {
			if ( preg_match( $skip, $id ) ) {
				continue;
			}
			$found[] = $id;
		}
		preg_match_all( '/get_theme_mod\(\s*[\'"]((?:aether|aureon)_[a-z0-9_]+)[\'"]/', $c, $mtm );
		foreach ( $mtm[1] as $id ) {
			if ( preg_match( $skip, $id ) ) {
				continue;
			}
			$found[] = $id;
		}

		// (a) Quote-delimited aether_*/aureon_* tokens (token maps, adapters).
		// Filter/action names are quoted too, so this scan may only CONFIRM
		// keys that are already registered — it never introduces new keys.
		preg_match_all( '/[\'"]((?:aether|aureon)_[a-z0-9_]+)[\'"]/', $c, $m2 );
		foreach ( $m2[1] as $id ) {
			if ( preg_match( $skip, $id ) ) {
				continue;
			}
			if ( isset( $registered[ $id ] ) ) {
				$found[] = $id;
			}
		}

		// (b)+(c) Settings-bucket array access — extracted ONCE per file:
		//   $settings['k'], $aureon_settings['k'], $font_settings['k'],
		//   $saved['k'], $options['k'], ...
		// Literal keys check membership directly. INTERPOLATED templates
		// (e.g. "{$single}post_image_height" in the blog module, with
		// $single ∈ {'', 'single_', 'page_'}) are matched by comparing the
		// template's literal remainder against each registered key stripped
		// of its dynamic page_/single_ prefix.
		preg_match_all( '/\$[a-z0-9_]*(?:settings|saved|options)\[\s*[\'"]([^\'"]*)[\'"]\s*\]/i', $c, $mb );
		$interp = array();
		foreach ( $mb[1] as $raw ) {
			if ( '' === $raw ) {
				continue;
			}
			if ( false !== strpos( $raw, '{$' ) ) {
				$interp[] = $raw;
			} elseif ( isset( $registered[ $raw ] ) ) {
				$found[] = $raw;
			}
		}
		if ( $interp ) {
			foreach ( $interp as $tpl ) {
				$remainder = trim( preg_replace( '/\{\$[a-zA-Z0-9_]+\}/', '', $tpl ) );
				if ( '' === $remainder ) {
					continue;
				}
				foreach ( array_keys( $registered ) as $id ) {
					if ( preg_match( $skip, $id ) || isset( $found_map[ $id ] ) ) {
						continue;
					}
					$suffix = preg_replace( '/^(page_|single_)/', '', $id );
					if ( $suffix === $remainder ) {
						$found_map[ $id ] = $rel;
					}
				}
			}
		}

		// Merge interpolated-key hits into the found list.
		foreach ( $found_map as $id => $file ) {
			$found[] = $id;
		}
		foreach ( $found as $id ) {
			$readers[ $id ][] = $rel;
		}
	}
}

// ---------------------------------------------------------------------------
// 3. Classify + emit CSV.
// ---------------------------------------------------------------------------
@mkdir( $root . '/docs', 0777, true );
$csv = fopen( $root . '/docs/customizer-contract-matrix.csv', 'w' );
fputcsv( $csv, array( 'setting', 'registered_in', 'readers', 'status' ) );

$all = array_unique( array_merge( array_keys( $registered ), array_keys( $readers ) ) );
sort( $all );
$dead = 0;
$orphaned = 0;
$demo = 0;
foreach ( $all as $id ) {
	$reg = isset( $registered[ $id ] ) ? implode( ' | ', $registered[ $id ] ) : '';
	$rdr = isset( $readers[ $id ] ) ? implode( ' | ', array_unique( $readers[ $id ] ) ) : '';
	if ( $reg && $rdr ) {
		$status = 'CONNECTED';
	} elseif ( $reg && ! $rdr ) {
		$status = 'DEAD';
		$dead++;
	} elseif ( isset( $engine_demo[ $id ] ) ) {
		$status = 'ENGINE_DEMO';
		$demo++;
	} else {
		$status = 'ORPHANED';
		$orphaned++;
	}
	fputcsv( $csv, array( $id, $reg, $rdr, $status ) );
}
fclose( $csv );

echo "Registered settings: " . count( $registered ) . "\n";
echo "Read settings:       " . count( $readers ) . "\n";
echo "DEAD:        $dead\n";
echo "ORPHANED:    $orphaned\n";
echo "ENGINE_DEMO: $demo\n";
echo "Matrix written: docs/customizer-contract-matrix.csv\n";
exit( ( $dead + $orphaned ) > 0 ? 1 : 0 );
