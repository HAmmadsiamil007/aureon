<?php
/**
 * Ferm Living split hero — full-height media panels with bottom-left titles.
 *
 * Shadows engine section id 'hero' (pack registration loads after base, so
 * this re-registration wins). Same adapter contract as the engine hero:
 * adapter-hero.php -> slides[] { headline, accent, subline, image, alt,
 * buttons[], badge, overlay, mobile_image }.
 *
 * Source: fermliving.com homepage "hero_split" — 2-up grid of full-screen
 * image panels; each panel carries a cream title bottom-left and an outlined
 * "Shop Now" button. Mobile stacks panels vertically.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adapter extension (data layer): fill imageless hero slides from live store
 * data so the split hero never renders empty on fresh installs.
 *
 * Packs cannot ship autoloaded adapters (the engine only globs frontend/
 * adapters + active-pack sections), so this adapter-side enrichment lives in
 * the pack section bootstrap. It runs ONLY for the fermliving design and ONLY
 * fills gaps — Customizer-configured slides always win untouched.
 *
 * @param array  $data Section data (adapter output).
 * @param string $id   Section id.
 * @return array
 */
if ( ! function_exists( 'ferm_hero_store_fallbacks' ) ) {
	function ferm_hero_store_fallbacks( $data, $id ) {
	if ( 'hero' !== $id || ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $data;
	}

	$slides = isset( $data['slides'] ) ? (array) $data['slides'] : array();
	$needs_image = false;
	foreach ( $slides as $slide ) {
		if ( empty( $slide['image'] ) && empty( $slide['mobile_image'] ) ) {
			$needs_image = true;
			break;
		}
	}
	if ( ! $needs_image ) {
		return $data;
	}

	// Candidate imagery: top sellers first, then categories with thumbnails.
	$candidates = array();

	if ( function_exists( 'wc_get_products' ) ) {
		$products = wc_get_products( array(
			'limit'   => 4,
			'status'  => 'publish',
			'orderby' => 'popularity',
			'order'   => 'DESC',
		) );
		foreach ( (array) $products as $product ) {
			$img_id = $product->get_image_id();
			if ( ! $img_id ) {
				continue;
			}
			$src = wp_get_attachment_image_url( $img_id, 'full' );
			if ( ! $src ) {
				continue;
			}
			$candidates[] = array(
				'image' => $src,
				'alt'   => $product->get_name(),
				'url'   => get_permalink( $product->get_id() ),
				'title' => sprintf( '%s — %s', get_bloginfo( 'name' ), __( 'Bestsellers', 'aureon' ) ),
			);
		}
	}

	if ( count( $candidates ) < 2 && function_exists( 'get_terms' ) ) {
		$terms = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => 4,
		) );
		foreach ( (array) $terms as $term ) {
			if ( is_wp_error( $term ) ) {
				continue;
			}
			$thumb_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
			if ( ! $thumb_id ) {
				continue;
			}
			$src = wp_get_attachment_image_url( $thumb_id, 'full' );
			if ( ! $src ) {
				continue;
			}
			$link = get_term_link( $term );
			$candidates[] = array(
				'image' => $src,
				'alt'   => $term->name,
				'url'   => is_wp_error( $link ) ? '' : $link,
				'title' => $term->name,
			);
		}
	}

	foreach ( $slides as $i => $slide ) {
		if ( ! empty( $slide['image'] ) || ! empty( $slide['mobile_image'] ) || empty( $candidates ) ) {
			continue;
		}
		$fallback = array_shift( $candidates );
		$slides[ $i ]['image'] = $fallback['image'];
		$slides[ $i ]['alt']   = $fallback['alt'];
		if ( ! empty( $fallback['title'] ) && empty( $slide['headline'] ) ) {
			$slides[ $i ]['headline'] = $fallback['title'];
		}
		if ( ! empty( $fallback['url'] ) ) {
			if ( isset( $slides[ $i ]['buttons'][0] ) && empty( $slides[ $i ]['buttons'][0]['url'] ) ) {
				$slides[ $i ]['buttons'][0]['url'] = $fallback['url'];
			} elseif ( empty( $slides[ $i ]['buttons'] ) ) {
				$slides[ $i ]['buttons'][] = array(
					'label' => __( 'Shop Now', 'aureon' ),
					'url'   => $fallback['url'],
					'style' => 'primary',
				);
			}
		}
	}

	// Drop slides that are still imageless (nothing to show).
	foreach ( $slides as $i => $slide ) {
		if ( empty( $slide['image'] ) && empty( $slide['mobile_image'] ) ) {
			unset( $slides[ $i ] );
		}
	}

	$data['slides'] = array_values( $slides );
	return $data;
	}
}
add_filter( 'aether_section_data', 'ferm_hero_store_fallbacks', 10, 2 );

aether_register_section( 'hero', array(
	'template' => 'sections/section-hero.php',
	'adapter'  => 'adapter-hero.php',
	'behavior' => array(),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$slides = isset( $sectionData['slides'] ) ? (array) $sectionData['slides'] : array();

if ( empty( $slides ) ) {
	return;
}
?>
<section class="ferm-split-hero" id="hero" aria-label="<?php esc_attr_e( 'Featured collections', 'aureon' ); ?>">
	<?php foreach ( $slides as $slide ) :
		$image   = isset( $slide['image'] ) ? $slide['image'] : '';
		$mobile  = ! empty( $slide['mobile_image'] ) ? $slide['mobile_image'] : '';
		$alt     = isset( $slide['alt'] ) ? $slide['alt'] : '';
		$title   = isset( $slide['headline'] ) ? $slide['headline'] : '';
		$subline = isset( $slide['subline'] ) ? $slide['subline'] : '';
		$buttons = isset( $slide['buttons'] ) ? (array) $slide['buttons'] : array();
		if ( empty( $image ) && empty( $mobile ) ) {
			continue;
		}
		$button = ! empty( $buttons[0] ) ? (array) $buttons[0] : array();
		$btn_label = isset( $button['label'] ) ? $button['label'] : '';
		$btn_url   = isset( $button['url'] ) ? $button['url'] : '';
		?>
		<div class="ferm-hero-panel">
			<a href="<?php echo esc_url( $btn_url ? $btn_url : '#' ); ?>"
			   class="ferm-hero-panel-link"
			   aria-label="<?php echo esc_attr( $title ); ?>"></a>

			<picture class="ferm-hero-media">
				<?php if ( $mobile ) : ?>
					<source media="(max-width: 767px)" srcset="<?php echo esc_url( $mobile ); ?>">
				<?php endif; ?>
				<img src="<?php echo esc_url( $image ); ?>"
					 alt="<?php echo esc_attr( $alt ? $alt : $title ); ?>"
					 loading="eager"
					 decoding="async"
					 fetchpriority="high">
			</picture>

			<div class="ferm-hero-content">
				<?php if ( $title ) : ?>
					<h2 class="ferm-hero-title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( $subline ) : ?>
					<p class="ferm-hero-subline"><?php echo esc_html( $subline ); ?></p>
				<?php endif; ?>
				<?php if ( $btn_label && $btn_url ) : ?>
					<a href="<?php echo esc_url( $btn_url ); ?>" class="btn ferm-btn-outline-light"><?php echo esc_html( $btn_label ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</section>
