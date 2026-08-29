<?php
/**
 * Ferm Living About Page - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
get_header();
$about_data = apply_filters( "aether_adapter_about_data", [] );
?>
<main class="content" id="main-content">
  <div class="limit mx-auto px-4 tab_l:px-6 py-8">
    <header class="mb-16">
      <h1 class="font-primary text-3xl tab_l:text-4xl font-medium leading-[1.15]"><?php echo esc_html( $about_data["heading"] ?? "About Ferm Living" ); ?></h1>
    </header>
    <?php if ( $about_data["body"] ) : ?><div class="prose prose-lg mb-16"><?php echo wp_kses_post( $about_data["body"] ); ?></div><?php endif; ?>
    <?php if ( $about_data["features"] ) : ?>
    <section class="mb-16" data-component="features">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php foreach ( $about_data["features"] as $feature ) : $title = $feature["title"] ?? ""; $desc = $feature["description"] ?? ""; ?>
        <div class="p-6">
          <h3 class="font-primary text-xl font-medium mb-3"><?php echo esc_html( $title ); ?></h3>
          <p class="text-black/60"><?php echo esc_html( $desc ); ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>
    <?php if ( $about_data["values"] ) : ?>
    <section class="mb-16" data-component="values">
      <h2 class="font-primary text-2xl font-medium mb-8">Our Values</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <?php foreach ( $about_data["values"] as $value ) : $title = $value["title"] ?? ""; $desc = $value["description"] ?? ""; ?>
        <div>
          <h3 class="font-primary text-xl font-medium mb-3"><?php echo esc_html( $title ); ?></h3>
          <p class="text-black/60"><?php echo esc_html( $desc ); ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>
    <?php if ( $about_data["stats"] ) : ?>
    <section class="mb-16" data-component="stats">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        <?php foreach ( $about_data["stats"] as $stat ) : $number = $stat["number"] ?? ""; $label = $stat["label"] ?? ""; ?>
        <div>
          <div class="font-primary text-4xl tab_l:text-5xl font-medium"><?php echo esc_html( $number ); ?></div>
          <div class="text-black/60 mt-1"><?php echo esc_html( $label ); ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>
  </div>
</main>
<?php get_footer(); ?>
