<?php
/**
 * Template Name: AETHER Styleguide
 * Template Post Type: page
 *
 * AETHER design-system styleguide.
 *
 * Every interactive element on this page is a PRODUCTION manifest component
 * rendered via `aether_render_component()` with sample data — there are no
 * styleguide-only components. The single source of truth for component
 * templates is `frontend/manifest/components.php`.
 *
 * Assign this template to a page (e.g. `/styleguide/`) in the editor:
 * Page Attributes → Template → "AETHER Styleguide".
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_component' ) ) :

	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$home_url = home_url( '/' );

	aether_render_component(
		'hero/page-title',
		array(
			'label'    => __( 'Design System', 'aureon' ),
			'title'    => __( 'AETHER Styleguide', 'aureon' ),
			'subtitle' => __( 'Every component below is a production manifest component rendered with sample data. Source of truth: frontend/manifest/components.php.', 'aureon' ),
			'behavior' => array(),
		)
	);

	/**
	 * Styleguide block opener — documentation chrome only (scoped classes,
	 * no effect on production pages).
	 *
	 * @param string $title Block title.
	 * @param string $note  Variant / state annotation.
	 */
	$sg_open = function ( $title, $note = '' ) {
		printf(
			'<section class="styleguide-block"><div class="styleguide-block-head"><h2>%1$s</h2>%2$s</div>',
			esc_html( $title ),
			$note ? '<p>' . esc_html( $note ) . '</p>' : ''
		);
	};
	$sg_close = function () {
		echo '</section>';
	};
	?>
	<style>
		/* Styleguide documentation chrome — scoped, never used in production pages. */
		.styleguide-blocks { padding: 72px 0 120px; }
		.styleguide-block { padding: 40px 0; border-top: 1px solid var(--line, #1A1A1A); }
		.styleguide-block-head h2 { font-family: var(--font-heading); font-size: 22px; letter-spacing: .02em; margin: 0 0 6px; }
		.styleguide-block-head p { color: var(--muted); font-size: 14px; margin: 0 0 24px; }
		.styleguide-row { display: flex; flex-wrap: wrap; gap: 24px; align-items: flex-start; }
		.styleguide-swatch { width: 128px; }
		.styleguide-swatch i { display: block; height: 56px; border-radius: 12px; border: 1px solid var(--line); margin-bottom: 8px; }
		.styleguide-swatch b { font-size: 13px; }
		.styleguide-swatch span { display: block; font-size: 12px; color: var(--muted); }
	</style>

	<div class="container styleguide-blocks">

		<?php
		// ─── Typography ───────────────────────────────────────────────────
		$sg_open( __( 'Typography', 'aureon' ), __( 'Tokens: --font-heading / --font-body. Families bridged from the dynamic Typography Manager.', 'aureon' ) );
		?>
		<div class="styleguide-row" style="flex-direction:column; gap:8px;">
			<h1 style="font-family:var(--font-heading); font-size:56px; line-height:1.05; margin:0;">Display Heading — Step into the void</h1>
			<h2 style="font-family:var(--font-heading); font-size:40px; margin:0;">Section Heading — Find Your Fit</h2>
			<h3 style="font-family:var(--font-heading); font-size:28px; margin:0;">Card Heading — AETHER Void Runner</h3>
			<p style="font-family:var(--font-body); font-size:17px; max-width:640px; color:var(--muted); margin:0;">Body copy — Precision-cut garments engineered in the dark. The body stack is Satoshi (or the Typography Manager body family) with a muted color for supporting text.</p>
			<span class="section-label">Eyebrow Label — Shop by Category</span>
			<p style="font-size:13px; color:var(--muted); letter-spacing:.12em; text-transform:uppercase;">Overline / Meta — 6 min read</p>
		</div>
		<?php $sg_close(); ?>

		<?php
		// ─── Color palette ────────────────────────────────────────────────
		$sg_open( __( 'Color Palette', 'aureon' ), __( 'Live tokens: --void/--surface/--text/--muted/--gold/--line/--error/--success. Editable in Customizer → AETHER Frontend → Design — Colors.', 'aureon' ) );
		$sg_swatches = array(
			'--void'       => '#09090B',
			'--surface'    => '#141416',
			'--surface-2'  => '#1a1a1d',
			'--surface-3'  => '#232327',
			'--text'       => '#FFFFFF',
			'--muted'      => '#A8B5C0',
			'--gold'       => '#C8956C',
			'--gold-alt'   => '#D4A574',
			'--line'       => '#1A1A1A',
			'--error'      => '#CC4444',
			'--success'    => '#4CAF50',
		);
		?>
		<div class="styleguide-row">
			<?php foreach ( $sg_swatches as $sg_var => $sg_hex ) : ?>
				<div class="styleguide-swatch">
					<i style="background:var(<?php echo esc_attr( $sg_var ); ?>)"></i>
					<b><?php echo esc_html( $sg_var ); ?></b>
					<span><?php echo esc_html( $sg_hex ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
		<?php $sg_close(); ?>

		<?php
		// ─── Buttons & CTA ────────────────────────────────────────────────
		$sg_open( __( 'Buttons & CTA', 'aureon' ), __( 'Classes: .btn .btn-primary .btn-outline .btn-lg .btn-sm. CTA banner = section/cta component.', 'aureon' ) );
		?>
		<div class="styleguide-row">
			<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-primary" data-magnetic="0.12">Primary Action</a>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-outline" data-magnetic="0.12">Secondary Action</a>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-primary btn-sm" data-magnetic="0.12">Small Primary</a>
			<button class="btn btn-outline" disabled>Disabled State</button>
		</div>
		<?php
		aether_render_component(
			'section/cta',
			array(
				'label' => __( 'View the Full Collection', 'aureon' ),
				'url'   => $shop_url,
			)
		);
		$sg_close();
		?>

		<?php
		// ─── Section header ───────────────────────────────────────────────
		$sg_open( __( 'Section Header', 'aureon' ), __( 'Eyebrow + title + subtitle heading block (motion-text words/lines).', 'aureon' ) );
		aether_render_component(
			'section/header',
			array(
				'label'    => __( 'Shop by Category', 'aureon' ),
				'title'    => __( 'Find Your Fit', 'aureon' ),
				'subtitle' => __( 'Six colorways. One obsession. Engineered silhouettes across Men, Women and Kids.', 'aureon' ),
			)
		);
		$sg_close();
		?>

		<?php
		// ─── Accordion (FAQ) ──────────────────────────────────────────────
		$sg_open( __( 'Accordion', 'aureon' ), __( 'section/accordion — states: closed, open (aria-expanded).', 'aureon' ) );
		aether_render_component(
			'section/accordion',
			array(
				'question' => __( 'What makes AETHER different?', 'aureon' ),
				'answer'   => __( 'AETHER combines carbon fiber plate technology with our proprietary Void Air cushioning system, delivering 40% more energy return than traditional running shoes.', 'aureon' ),
				'open'     => true,
			)
		);
		aether_render_component(
			'section/accordion',
			array(
				'question' => __( 'What is the return policy?', 'aureon' ),
				'answer'   => __( 'We offer a 30-day no-questions-asked return policy with free return shipping on all orders.', 'aureon' ),
			)
		);
		aether_render_component(
			'section/accordion',
			array(
				'question' => __( 'How long does shipping take?', 'aureon' ),
				'answer'   => __( 'Standard shipping is free on orders over $200 and takes 3-5 business days. Express shipping is available for a small fee.', 'aureon' ),
			)
		);
		$sg_close();
		?>

		<?php
		// ─── Rating ───────────────────────────────────────────────────────
		$sg_open( __( 'Star Rating', 'aureon' ), __( 'commerce/rating — 0–5 with half-star support.', 'aureon' ) );
		?>
		<div class="styleguide-row">
			<div class="product-rating"><?php aether_render_component( 'commerce/rating', array( 'stars' => 5 ) ); ?> <span>(128)</span></div>
			<div class="product-rating"><?php aether_render_component( 'commerce/rating', array( 'stars' => 4.5 ) ); ?> <span>(86)</span></div>
			<div class="product-rating"><?php aether_render_component( 'commerce/rating', array( 'stars' => 3 ) ); ?> <span>(42)</span></div>
		</div>
		<?php $sg_close(); ?>

		<?php
		// ─── Product cards ────────────────────────────────────────────────
		$sg_open( __( 'Product Cards', 'aureon' ), __( 'card/product — home layout (rating/actions) + shop layout (compact). Data contract: docs/FRONTEND_DATA_CONTRACT.md §6.', 'aureon' ) );
		$sg_products = array(
			array(
				'name'    => 'AETHER Void Runner',
				'tagline' => 'Carbon fiber · 40mm cushion',
				'price'   => '$449',
				'rating'  => 5,
				'reviews' => 128,
				'badge'   => 'Bestseller',
				'url'     => $shop_url,
			),
			array(
				'name'    => 'AETHER Cloud Stride',
				'tagline' => 'Ultra-light · Zero gravity',
				'price'   => '$99',
				'rating'  => 4.5,
				'reviews' => 86,
				'badge'   => 'New',
				'url'     => $shop_url,
			),
			array(
				'name'    => 'AETHER Midnight Edition',
				'tagline' => 'Stealth black · Reflective',
				'price'   => '$479',
				'rating'  => 5,
				'reviews' => 64,
				'badge'   => 'Limited',
				'url'     => $shop_url,
			),
		);
		?>
		<div class="products-grid">
			<?php foreach ( $sg_products as $sg_product ) : ?>
				<?php aether_render_component( 'card/product', wp_parse_args( $sg_product, array( 'behavior' => array( 'tilt' => true ) ) ) ); ?>
			<?php endforeach; ?>
		</div>
		<div class="products-grid" style="margin-top:32px;">
			<?php
			foreach ( $sg_products as $sg_product ) {
				aether_render_component(
					'card/product',
					array(
						'name'        => $sg_product['name'],
						'price_plain' => $sg_product['price'],
						'badge'       => 'Sale' === $sg_product['badge'] ? $sg_product['badge'] : '',
						'url'         => $sg_product['url'],
						'layout'      => 'shop',
						'behavior'    => array( 'tilt' => true ),
					)
				);
			}
			?>
		</div>
		<?php $sg_close(); ?>

		<?php
		// ─── Category cards ───────────────────────────────────────────────
		$sg_open( __( 'Category Cards', 'aureon' ), __( 'card/category — modifiers: default / large / accent.', 'aureon' ) );
		?>
		<div class="category-grid">
			<?php
			aether_render_component(
				'card/category',
				array(
					'name'     => 'Men',
					'count'    => '24 Products',
					'url'      => $shop_url,
					'modifier' => 'large',
				)
			);
			aether_render_component(
				'card/category',
				array(
					'name'     => 'Women',
					'count'    => '18 Products',
					'url'      => $shop_url,
				)
			);
			aether_render_component(
				'card/category',
				array(
					'name'     => 'New Arrivals',
					'count'    => 'Just Dropped',
					'url'      => $shop_url,
					'modifier' => 'accent',
				)
			);
			?>
		</div>
		<?php $sg_close(); ?>

		<?php
		// ─── Blog cards ───────────────────────────────────────────────────
		$sg_open( __( 'Blog Cards', 'aureon' ), __( 'card/blog — image, category, date, title, excerpt. Data contract §8.', 'aureon' ) );
		?>
		<div class="blog-grid">
			<?php
			foreach ( array(
				array( 'category' => 'Design', 'date' => 'Aug 4, 2026', 'title' => 'The Anatomy of a Carbon Plate', 'excerpt' => 'Why aerospace-grade materials changed the running game — and how we tune them.', 'url' => $home_url ),
				array( 'category' => 'Culture', 'date' => 'Jul 28, 2026', 'title' => 'Inside the Void Lab', 'excerpt' => 'A look at the biomechanics studio where every silhouette is born.', 'url' => $home_url ),
				array( 'category' => 'Journal', 'date' => 'Jul 19, 2026', 'title' => 'Breaking in: 200 Miles Later', 'excerpt' => 'Our test team logs ten thousand miles so your first run feels like a homecoming.', 'url' => $home_url ),
			) as $sg_post ) {
				aether_render_component( 'card/blog', $sg_post );
			}
			?>
		</div>
		<?php $sg_close(); ?>

		<?php
		// ─── Review cards ─────────────────────────────────────────────────
		$sg_open( __( 'Review Cards', 'aureon' ), __( 'card/review — initials avatar, verified badge, stars, title, quote. Data contract: testimonials.', 'aureon' ) );
		?>
		<div class="styleguide-row">
			<?php
			foreach ( array(
				array(
					'name'     => 'Marcus Chen',
					'role'     => 'Professional Runner',
					'verified' => true,
					'stars'    => 5,
					'title'    => 'Game changer for marathon training',
					'quote'    => 'The carbon fiber plate gives insane energy return. I shaved 3 minutes off my half marathon time.',
					'date'     => '2 weeks ago',
				),
				array(
					'name'     => 'Sarah Kim',
					'role'     => 'Sneaker Collector',
					'verified' => true,
					'stars'    => 5,
					'title'    => 'Premium feel from box to street',
					'quote'    => 'The unboxing experience alone is worth it. These are functional art.',
					'date'     => '1 month ago',
				),
				array(
					'name'     => 'Aisha Lawson',
					'role'     => 'Fitness Enthusiast',
					'verified' => true,
					'stars'    => 4.5,
					'title'    => 'Best running shoe I have owned',
					'quote'    => 'They still look brand new after 200+ miles. Worth every penny.',
					'date'     => '5 days ago',
				),
			) as $sg_review ) {
				aether_render_component( 'card/review', $sg_review );
			}
			?>
		</div>
		<?php $sg_close(); ?>

		<?php
		// ─── Team cards ───────────────────────────────────────────────────
		$sg_open( __( 'Team Cards', 'aureon' ), __( 'card/team — name, role, bio (image optional).', 'aureon' ) );
		?>
		<div class="team-grid">
			<?php
			foreach ( array(
				array( 'name' => 'Amara Voss', 'role' => 'Co-founder · Head of Biomechanics', 'bio' => 'Architect of the Void Air cushioning system.' ),
				array( 'name' => 'Kai Nakamura', 'role' => 'Co-founder · Lead Designer', 'bio' => 'Form follows stride.' ),
				array( 'name' => 'Leila Haddad', 'role' => 'Head of Engineering', 'bio' => 'Obsessed with gram-level precision.' ),
			) as $sg_member ) {
				aether_render_component( 'card/team', $sg_member );
			}
			?>
		</div>
		<?php $sg_close(); ?>

		<?php
		// ─── Filter bar ───────────────────────────────────────────────────
		$sg_open( __( 'Filter Bar', 'aureon' ), __( 'section/filter-bar — active state styling.', 'aureon' ) );
		aether_render_component(
			'section/filter-bar',
			array(
				'buttons' => array(
					array( 'label' => 'All', 'url' => $shop_url, 'active' => true ),
					array( 'label' => 'Men', 'url' => $shop_url ),
					array( 'label' => 'Women', 'url' => $shop_url ),
					array( 'label' => 'Kids', 'url' => $shop_url ),
					array( 'label' => 'Sale', 'url' => $shop_url ),
				),
			)
		);
		$sg_close();
		?>

		<?php
		// ─── Pagination ───────────────────────────────────────────────────
		$sg_open( __( 'Pagination', 'aureon' ), __( 'section/pagination — numbered window with gap dots, query-string aware base.', 'aureon' ) );
		aether_render_component(
			'section/pagination',
			array(
				'current' => 3,
				'total'   => 9,
				'base'    => $shop_url,
			)
		);
		$sg_close();
		?>

		<?php
		// ─── Breadcrumb ───────────────────────────────────────────────────
		$sg_open( __( 'Breadcrumb', 'aureon' ), __( 'product/breadcrumb — trail with current-page terminal.', 'aureon' ) );
		aether_render_component(
			'product/breadcrumb',
			array(
				'crumbs' => array(
					array( 'label' => 'Home', 'url' => $home_url ),
					array( 'label' => 'Collection', 'url' => $shop_url ),
					array( 'label' => 'Men\'s Sneakers', 'url' => $shop_url ),
					array( 'label' => 'AETHER Void Runner' ),
				),
			)
		);
		$sg_close();
		?>

		<?php
		// ─── Article meta ─────────────────────────────────────────────────
		$sg_open( __( 'Article Meta', 'aureon' ), __( 'content/article-meta — author / date / read time line.', 'aureon' ) );
		aether_render_component(
			'content/article-meta',
			array(
				'author'    => 'Amara Voss',
				'date'      => 'August 4, 2026',
				'read_time' => '6',
			)
		);
		$sg_close();
		?>

		<?php
		// ─── Newsletter form ──────────────────────────────────────────────
		$sg_open( __( 'Newsletter Form', 'aureon' ), __( 'form/newsletter — submit posts to admin-ajax (graceful fallback).', 'aureon' ) );
		aether_render_component(
			'form/newsletter',
			array(
				'placeholder' => __( 'Your email address', 'aureon' ),
				'button_text' => __( 'Join the Chronicle', 'aureon' ),
			)
		);
		$sg_close();
		?>

		<?php
		// ─── Page-level states (documented, not stacked here) ─────────────
		// `utility/countdown` (coming-soon) and `utility/error-404` are
		// full-bleed page states rendered on their real routes (/coming-soon/
		// and any 404). They are intentionally NOT stacked on this page:
		// each embeds the decorative fog block with the shared #hl_01–03 ids,
		// and stacking them would duplicate those ids in one document.
		$sg_open( __( 'Page-Level States', 'aureon' ), __( 'utility/countdown and utility/error-404 — live on their real routes.', 'aureon' ) );
		echo '<p class="section-subtitle">' . esc_html__( 'The countdown state renders on /coming-soon/; the 404 state renders for any missing URL. Both are covered by the route suite.', 'aureon' ) . '</p>';
		$sg_close();
		?>

	</div>

<?php endif; ?>

<?php get_footer(); ?>
