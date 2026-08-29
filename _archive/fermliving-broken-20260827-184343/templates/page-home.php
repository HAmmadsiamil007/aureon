<?php
/**
 * Ferm Living Homepage - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
get_header();
?>
<main class="content" id="main-content">
  <?php
  $sections = apply_filters( "aether_frontpage_sections", [] );
  foreach ( $sections as $section ) {
    $section_file = aether_active_design_dir() . "sections/section-{$section}.php";
    if ( file_exists( $section_file ) ) {
      include $section_file;
    }
  }
  ?>
</main>
<?php get_footer(); ?>
