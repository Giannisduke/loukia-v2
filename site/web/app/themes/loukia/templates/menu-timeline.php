<?php
/**
 * Setup query to show the ‘services' post type with ‘8' posts.
 * Output the title with an excerpt.
 */
 $args = array(
   'post_type' => 'collection',
   'post_status' => 'publish',
   'posts_per_page' => -1,
   'orderby' => 'date',
   'order' => 'DESC',
);

$loop = new WP_Query( $args ); $counter_menu = -1;
//$post_date = the_time('F Y');
?>
<nav class="navbar navbar-expand-lg navbar-light navbar-right first">
<div id="list-example" class="list-group">
<?php while ( $loop->have_posts() ) : $loop->the_post(); $counter_menu++; ?>
   <a class="list-group-item list-group-item-action" href="#section__<?php echo $counter_menu; ?>"><?php the_time('Y'); ?></a>
<?php endwhile; ?>
</div>
</nav>
<?php wp_reset_postdata();
