<?php
/**
 * Setup query to show the 'products' post type with ‘featured' posts.
 * Output the title with an excerpt.
 */
 $meta_query   = WC()->query->get_meta_query();
 $meta_query[] = array(
     'key'   => '_featured',
     'value' => 'yes'
 );
 $args = array(
     'post_type'   =>  'product',
     'stock'       =>  1,
     'showposts'   =>  6,
     'orderby'     =>  'date',
     'order'       =>  'DESC',
     'meta_query'  =>  $meta_query
 );

 $loop = new WP_Query( $args );
 $counter_outer = -1;

   while ( $loop->have_posts() ) : $loop->the_post(); $counter_outer++ ?>
   <section id="section__<?php echo $counter_outer; ?>" class="section__<?php echo $counter_outer; ?><?php echo $loop->current_post >= 1 ? '' : ' active'; ?>">
     <article>
         <div class="col-24 col-lg-13 left left__<?php echo $counter_outer; ?> p-0">
            <div id="carousel_<?php echo $counter_outer; ?>" class="carousel slide main_carousel" data-ride="carousel">
              <!-- Wrapper for slides -->
              <div class="carousel-inner">
                              <?php if ( $attachment_ids = $product->get_gallery_image_ids() ) {
                  foreach ( $attachment_ids as $attachment_id ) {
                      echo wc_get_gallery_image_html( $attachment_id );
                  }
              }
              ?>
              </div>
            </div>
         </div>
     </article>
   </section>
   <?php //    the_excerpt();
   endwhile;

   wp_reset_postdata(); ?>
