<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php get_template_part('templates/entry-meta'); ?>
    </header>
    <div class="entry-content">
      <div class="col-24 p-0">
      <?php
      if ( get_the_content() ) {
      the_content();
      echo '<hr>';
      }
      ?>
      </div>
      <div class="col-24 p-0">

      <?php get_template_part('templates/portfolio-galery'); ?>
    </div>
    </div>
    <footer>
      <?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'loukia'), 'after' => '</p></nav>']); ?>
    </footer>
    <?php //comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
