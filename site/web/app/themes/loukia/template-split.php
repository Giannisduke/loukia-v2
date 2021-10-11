<?php
/**
 * Template Name: Front Page Split
 */
?>

<?php while (have_posts()) : the_post(); ?>
  <div class="row">
    <div class="col-22 facetwp-template p-0">
      <?php get_template_part('templates/page', 'split'); ?>
      <?php //get_template_part('templates/split', 'products'); ?>
      <?php //get_template_part('templates/content', 'page'); ?>
    </div>
  </div>
<?php endwhile; ?>
