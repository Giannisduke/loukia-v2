<header class="banner fixed-top">
  <nav class="navbar navbar-expand-lg navbar-light ">
    <div class="col-24 col-lg-13 p-0">
      <?php if ( function_exists( 'the_custom_logo' ) ) {
       the_custom_logo();
      } ?>
  </div>
  <div class="col-11">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="true" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">

<?php
   wp_nav_menu([
     'menu'            => 'Primary Navigation',
//     'theme_location'  => 'Primary Navigation',
     'container'       => 'div',
     'container_id'    => 'bs4navbar',
     'container_class' => 'collapse navbar-collapse',
     'menu_id'         => false,
     'menu_class'      => 'navbar-nav mr-auto',
     'depth'           => 2,
     'fallback_cb'     => 'bs4navwalker::fallback',
     'walker'          => new bs4navwalker()
   ]);
   ?>

    </div>
    </div>
  </nav>
</header>
