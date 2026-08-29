<?php
/**
 * Ferm Living Product Archive - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
get_header();
$archive_data = apply_filters( "aether_adapter_archive_data", [] );
$products = $archive_data["items"] ?? [];
$term = get_queried_object();
?>
<main class="content" id="main-content">
  <div class="limit mx-auto px-4 tab_l:px-6 py-8">
    <header class="mb-12">
      <h1 class="font-primary text-3xl tab_l:text-4xl font-medium leading-[1.15]"><?php echo esc_html( $term->name ?? "Shop" ); ?></h1>
      <?php if ( $term->description ) : ?><div class="prose prose-sm mt-4"><?php echo wp_kses_post( $term->description ); ?></div><?php endif; ?>
    </header>
    <div class="flex flex-col md:flex-row gap-8">
      <aside class="md:w-64 flex-shrink-0" data-component="shopFilter">
        <?php if ( $archive_data["filters"] ?? [] ) : ?>
        <div class="space-y-6">
          <?php foreach ( $archive_data["filters"] as $filter ) : ?>
          <fieldset>
            <legend class="font-medium mb-3"><?php echo esc_html( $filter["label"] ); ?></legend>
            <label class="flex items-center gap-2"><input type="checkbox" value="<?php echo esc_attr( $filter["url"] ); ?>" data-filter><?php echo esc_html( $filter["label"] ); ?></label>
          </fieldset>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </aside>
      <div class="flex-1">
        <div class="flex items-center justify-between mb-6" data-component="shopToolbar">
          <select class="h-10 px-4 border border-black/20 text-sm" data-sort><?php foreach ( [ "Featured" => "", "Best selling" => "?sort=best-selling", "Alphabetically, A-Z" => "?sort=title-asc", "Alphabetically, Z-A" => "?sort=title-desc", "Price, low to high" => "?sort=price-asc", "Price, high to low" => "?sort=price-desc", "Date, new to old" => "?sort=date-desc", "Date, old to new" => "?sort=date-asc" ] as $label => $value ) : ?><option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select>
        </div>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4" data-component="productCardGrid">
          <?php foreach ( $products as $product ) : include aether_active_design_dir() . "components/cards/product.php"; endforeach; ?>
        </div>
        <?php if ( $archive_data["pagination"] ?? false ) : ?>
        <nav class="mt-8 flex justify-center gap-2" data-component="pagination"><?php echo wp_kses_post( $archive_data["pagination"] ); ?></nav>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>
<?php get_footer(); ?>
