<?php
/**
 * Ferm Living Contact Page - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
get_header();
$contact_data = apply_filters( "aether_adapter_contact_data", [] );
?>
<main class="content" id="main-content">
  <div class="limit mx-auto px-4 tab_l:px-6 py-8">
    <header class="mb-12">
      <h1 class="font-primary text-3xl tab_l:text-4xl font-medium leading-[1.15]"><?php echo esc_html( $contact_data["heading"] ?? "Get in Touch" ); ?></h1>
    </header>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
      <div class="prose prose-lg">
        <address class="not-italic mb-8">
          <?php echo nl2br( esc_html( $contact_data["address"] ?? "" ) ); ?>
        </address>
        <div class="mb-4"><strong>Phone:</strong> <a href="tel:<?php echo esc_attr( $contact_data["phone"] ?? "" ); ?>"><?php echo esc_html( $contact_data["phone"] ?? "" ); ?></a></div>
        <div class="mb-4"><strong>Email:</strong> <a href="mailto:<?php echo esc_attr( $contact_data["email"] ?? "" ); ?>"><?php echo esc_html( $contact_data["email"] ?? "" ); ?></a></div>
        <div><strong>Hours:</strong> <?php echo esc_html( $contact_data["hours"] ?? "" ); ?></div>
      </div>
      <div>
        <?php echo do_shortcode( "[contact-form-7 id=\"contact-form\"]" ); ?>
      </div>
    </div>
  </div>
</main>
<?php get_footer(); ?>
