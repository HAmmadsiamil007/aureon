<?php
/**
 * Ferm Living Preloader
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
$logo_svg = file_get_contents( aether_active_design_dir() . "assets/logo.svg" );
?>
<div class="fixed inset-0 z-[9999999999] flex items-center justify-center bg-cream transition-opacity duration-500 ease-in-out" id="preloader" data-preloader>
  <div class="flex flex-col items-center gap-4">
    <div class="h-[45px] w-auto"><?php echo $logo_svg ?: "<svg viewBox=\"0 0 738 694\" class=\"h-full w-full fill-black\"><path d=\"M164.73,536.33h-34.05c3.61,4.74,6.09,10.38,6.09,19.4v114.57c0,9.02-2.48,14.66-6.09,19.4h34.05c-3.61-4.74-6.09-10.37-6.09-19.4v-114.57c0-9.02,2.48-14.66,6.09-19.4Z\"/></svg>"; ?></div>
    <div class="h-1 w-24 bg-black animate-pulse"></div>
  </div>
</div>
<script>document.addEventListener("DOMContentLoaded",function(){const p=document.getElementById("preloader");if(p){window.addEventListener("load",()=>{p.style.opacity="0";setTimeout(()=>p.remove(),500);});}});</script>
