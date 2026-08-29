<?php
/**
 * Ferm Living announcement bar — rotating USP carousel (frozen source structure).
 *
 * Key:    'shell/announcement' (override)
 * Source: fermliving.com announcement structure
 * Props:  items (array of {text, url}).
 * Contract: keeps .usp-text, data-usp-item, data-usp-current-index —
 *           platform announcement JS operates unchanged.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();
$items         = isset( $componentData['items'] ) ? (array) $componentData['items'] : array();

if ( empty( $items ) ) {
	return;
}

$texts = array();
foreach ( $items as $item ) {
	$text = isset( $item['text'] ) ? $item['text'] : '';
	if ( '' !== $text ) {
		$texts[] = $item;
	}
}

if ( empty( $texts ) ) {
	return;
}

$announcement_enabled = (bool) aureon_get_option( 'aether_announcement_enabled', true );
if ( ! $announcement_enabled ) {
	return;
}

$speed = (int) aureon_get_option( 'ferm_announcement_speed', 4000 );
$total = count( $texts );
?>
<div
  class='relative flex h-full w-full items-center justify-between overflow-hidden font-secondary text-sm'
  data-component='uspHeader'
  data-speed='<?php echo esc_attr( $speed ); ?>'
  data-usp-length='<?php echo esc_attr( $total ); ?>'
  role='region'
  aria-label='Announcements'
  aria-roledescription='carousel'
>
  <div
    class='left-0 right-0 top-[5px] flex w-full items-center justify-between'
    aria-live='polite'
    aria-atomic='true'
  >
    <?php foreach ( $texts as $index => $item ) :
      $text      = isset( $item['text'] ) ? $item['text'] : '';
      $url       = isset( $item['url'] ) ? $item['url'] : '';
      $usp_index = $index + 1;
      $is_first  = ( 1 === $usp_index );
      $aria_hidden = $is_first ? 'false' : 'true';
      $animate_class = $is_first ? ' animate-in' : '';
      ?>
      <?php if ( $url ) : ?>
        <a
          data-usp-item
          data-usp-index='<?php echo esc_attr( $usp_index ); ?>'
          href='<?php echo esc_url( $url ); ?>'
          aria-hidden='<?php echo esc_attr( $aria_hidden ); ?>'
          class='usp-text absolute block w-full -translate-y-full cursor-pointer overflow-hidden text-ellipsis whitespace-nowrap text-left font-normal leading-[16px] no-underline opacity-0 [color:inherit] hover:!opacity-50 [&_p]:!m-0 [&_p]:text-center [&_p]:leading-normal [&_p]:![font-size:inherit] [&_p]:tab_p:text-left<?php echo esc_attr( $animate_class ); ?>'
        >
          <p><?php echo esc_html( $text ); ?></p>
        </a>
      <?php else : ?>
        <div
          data-usp-item
          data-usp-index='<?php echo esc_attr( $usp_index ); ?>'
          aria-hidden='<?php echo esc_attr( $aria_hidden ); ?>'
          class='usp-text absolute block w-full -translate-y-full cursor-default overflow-hidden text-ellipsis whitespace-nowrap text-left font-normal leading-[16px] no-underline opacity-0 transition-all duration-300 ease-in-out [color:inherit] [&_p]:!m-0 [&_p]:text-center [&_p]:leading-normal [&_p]:![font-size:inherit] [&_p]:tab_p:text-left<?php echo esc_attr( $animate_class ); ?>'
        >
          <p><?php echo esc_html( $text ); ?></p>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
    
    <span class='w-full text-right hidden font-normal [color:inherit] tab_p:block'>
      <span data-usp-current-index>1</span> / <?php echo esc_attr( $total ); ?>
    </span>
  </div>
</div>