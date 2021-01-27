<article <?php post_class(''); ?>>
  <header>
    <?php get_template_part('templates/entry-meta'); ?>
    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <div class="entry-summary">
      <?php the_excerpt(); ?>
    </div>
  </header>
  <featured>
    <?php if ( has_post_thumbnail()) : ?>
    <a href="<?php the_permalink(); ?>" alt="<?php the_title_attribute(); ?>">
        <?php the_post_thumbnail('large', array( 'class' => 'img-fluid' )); ?>
    </a>
<?php endif; ?>
  </featured>

</article>
