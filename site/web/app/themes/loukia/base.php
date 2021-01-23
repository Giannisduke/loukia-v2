<?php

use Roots\Sage\Setup;
use Roots\Sage\Wrapper;

?>

<!doctype html>
<html <?php language_attributes(); ?>>
  <?php get_template_part('templates/head'); ?>
  <body <?php body_class(); ?>>
    <!--[if IE]>
      <div class="alert alert-warning">
        <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'loukia'); ?>
      </div>
    <![endif]-->
    <?php
      do_action('get_header');
      get_template_part('templates/header');
    ?>
    <div class="wrap container-fluid" role="document">
      <div class="row">

</div>
      <div class="content row">

        <?php if (Setup\display_sidebar()) : ?>

          <aside class="sidebar" id="collapseExample">
            <?php
            if ( !is_shop() ) {
            include Wrapper\sidebar_path();
            }
            elseif ( is_shop() ) {
              dynamic_sidebar('sidebar-shop');
            }

            ?>
          </aside><!-- /.sidebar -->
        <?php endif; ?>
        <main class="main">
          <?php include Wrapper\template_path(); ?>
        </main><!-- /.main -->
          <aside class="main-sidebar">
            <?php
            if ( is_page_template('template-split.php') ) {
            get_template_part('templates/menu', 'timeline');
            }
            else { }
            ?>
            <nav class="navbar navbar-expand-lg navbar-light bg-primary navbar-right">
            <?php get_template_part('templates/sidebar', 'main'); ?>
            </nav>
          </aside>
      </div><!-- /.content -->
    </div><!-- /.wrap -->
    <?php
      do_action('get_footer');
      get_template_part('templates/footer');
      wp_footer();
    ?>
  </body>
</html>
