<header class="banner fixed-top">
  <nav class="navbar navbar-expand-lg navbar-light logo">
    <div class="col-22 col-lg-13 p-0">
      <?php if ( function_exists( 'the_custom_logo' ) ) {
       the_custom_logo();
      } ?>
  </div>
  <div class="col-22 col-lg-11 text-center">
<?php
   wp_nav_menu([
     'menu'            => 'Primary Navigation',
//     'theme_location'  => 'Primary Navigation',
     'container'       => 'div',
     //'container_id'    => 'navbarNav',
     'container_class' => 'navbar',
     'menu_id'         => false,
     'menu_class'      => 'list-inline',
     'depth'           => 2,
     'fallback_cb'     => 'bs4navwalker::fallback',
     'walker'          => new bs4navwalker()
   ]);
   ?>
   <?php if ( is_shop() ) { ?>
     <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidebarcollapse" aria-controls="sidebarcollapse" aria-expanded="true" aria-label="Toggle navigation">
       <span class="navbar-toggler-icon"></span>
     </button>

<?php } ?>


    </div>
  </nav>
</header>
