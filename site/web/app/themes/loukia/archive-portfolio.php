<div class="grid facetwp-template">
<?php while (have_posts()) : the_post(); ?>
    <div class="grid-item">
      <a href="<?php echo get_permalink(); ?>">
  <?php if ( has_post_thumbnail() ) {
    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
    $alt = get_the_title();
    if ( ! empty( $large_image_url[0] ) ) {
      //  echo '<a href="' . esc_url( $large_image_url[0] ) . '" title="' . the_title_attribute( array( 'echo' => 0 ) ) . '">';
        echo get_the_post_thumbnail( $post->ID, 'medium', array( 'alt' => $alt ) );
      //  echo '</a>';
    }
} ?>

<div class="title d-flex flex-column justify-content-between w-100">
  <div><h3><?php print the_title(); ?></h3></div>
  <div class="date"><?php echo get_the_date( 'M Y' ); ?></div>
</div>
</a>
  </div>
  <?php endwhile; ?>
  </div>
  <?php wp_reset_postdata(); ?>
