<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>

      <?php if( get_field('portfolio_video') ) { ?>
        <div id="trigger1" class="spacer s0"></div>
        <div class="jumbotron jumbotron-fluid video" id="pin1">
<video autoplay muted loop poster="https://dummyimage.com/900x400/000/fff" >
    <source src="" data-src="<?php echo get_field('portfolio_video'); ?>" type="video/mp4">
    <source src="" data-src="https://upload.wikimedia.org/wikipedia/commons/7/79/Big_Buck_Bunny_small.webm" type="video/webm">
    <source src="" data-src="https://upload.wikimedia.org/wikipedia/commons/7/79/Big_Buck_Bunny_small.ogv" type="video/ogg">
</video>

<?php } else { ?>
  <div class="jumbotron jumbotron-fluid test" id="pin1">
    <div id="trigger1" class="spacer s0"></div>
<?php } ?>
  <div class="container text-white">
    <header>
    <h1 class="display-4"><?php the_title(); ?></h1>
    <?php if( get_field('portfolio_subtitle') ) { ?>
    <h2><?php echo get_field('portfolio_subtitle'); ?></h2>
    <?php } ?>
    </header>
    <p><?php get_template_part('templates/entry-meta'); ?></p>
  </div>
  <!-- /.container -->
</div>
<!-- /.jumbotron -->

    <div class="entry-content">
      <?php
      if ( get_the_content() ) {
      the_content();
      echo '<hr>';
      }
      ?>
      <?php get_template_part('templates/portfolio-galery'); ?>
    </div>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
