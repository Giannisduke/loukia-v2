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
        'facetwp' => true
    );

    $loop = new WP_Query( $args );
    $counter_outer = -1;
    while ( $loop->have_posts() ) : $loop->the_post(); $counter_outer++ ?>
    <section id="section__<?php echo $counter_outer; ?>" class="section__<?php echo $counter_outer; ?><?php echo $loop->current_post >= 1 ? '' : ' active'; ?>">
      <div class="row">
      <div class="col-24 col-lg-13 left left__<?php echo $counter_outer; ?> p-0">
        <?php
        /* FRONT-PAGE SLIDER WITH ACF GALLERY AND BOOTSTRAP 4 CAROUSEL */

        $images = get_field('collection_gallery');
        $count=0;
        $count1=0;

        if($images) : ?>

            <div id="carousel_<?php echo $counter_outer; ?>" class="carousel slide main_carousel" data-ride="carousel" data-interval="false">
              <!-- Wrapper for slides -->
        	<div class="carousel-inner">
        		<?php foreach( $images as $image ): ?>
        	    <div class="carousel-item <?php if($count1==0) : echo ' active'; endif; ?>">
        	        <img src="<?php echo $image['url']; ?>" class="img-fluid main_img" alt="<?php echo $image['alt']; ?>" />
        	    </div><!-- item -->
        		<?php $count1++; ?>
        		<?php endforeach; ?>
        	</div><!-- carousel inner -->

        	<?php /* CAROUSEL CONTROL PREVIOUS & NEXT */ ?>

        	<a class="carousel-control-prev" href="#carousel_<?php echo $counter_outer; ?>" role="button" data-slide="prev">
        		<span class="carousel-control-prev-icon" aria-hidden="true"></span>
        		<span class="sr-only">Previous</span>
        	</a>
        	<a class="carousel-control-next" href="#carousel_<?php echo $counter_outer; ?>" role="button" data-slide="next">
        		<span class="carousel-control-next-icon" aria-hidden="true"></span>
        		<span class="sr-only">Next</span>
        	</a>
            </div><!-- #carousel -->

        <?php endif; ?>

      </div>

      <div class="col-24 col-lg-10 pb-5 d-flex flex-column right right__<?php echo $counter_outer; ?> p-0">
        <div class="row h-75">
          <div class="col-24"></div>
          <div class="col-24"><h1><?php print the_title(); ?></h1><h2><?php the_field('collection_subtitle'); ?></h2><?php print the_content(); ?></div>
          <div class="col-24"></div>
        </div>
        <div class="row">

        <?php
     $images = get_field('collection_gallery');

     if( $images ): ?>

       <div id="mini-carousel_<?php echo $counter_outer; ?>" class="carousel slide col-21 mini-carousel" data-ride="carousel" data-interval="false">
         <ol class="carousel-indicators">
           <?php $indicators = 0; ?>
           <?php $slide_to = 0; ?>
    <?php foreach( $images as $image ): ?>

    <?php if ( $indicators % 6 === 0 ) : ?>

    <li data-target="#mini-carousel_<?php echo $counter_outer; ?>" data-slide-to="<?php echo $slide_to; ?>" class="<?php if ( $indicators < 1 ) : ?>active<?php endif; ?>">

      <?php $slide_to++; endif; ?>

      <?php if ( $indicators % 6 === 5 ) : ?>
      </li><!-- .row -->

      <?php endif; ?>
    <?php   $indicators++;   // increment the counter
    endforeach;
    ?>
         </ol>
         <div class="carousel-inner">

             <?php $i = 0; ?>
             <?php foreach( $images as $image ): ?>
             <?php if ( $i % 6 === 0 ) : ?>
              <div class="carousel-item <?php if ( $i < 1 ) : ?>active<?php endif; ?>">

          <?php endif; ?>

                <img src="<?php echo $image['sizes']['thumbnail']; ?>" class="img-fluid" data-target="#carousel_<?php echo $counter_outer; ?>" data-slide-to="<?php echo $i; ?>" alt="<?php echo $image['alt']; ?>" />

                  <?php if ( $i % 6 === 5 ) : ?>

                      </div><!-- .carousel-item -->
                  <?php endif; ?>
              <?php $i++; // increment the counter
            endforeach;
              ?>
              <!-- This is needed if the (<number of posts> / 3) does not equal to 3. E.g. 11 posts would require 4 rows -->
              <?php if ( $i % 6 !== 0 ) : ?>
                      </div><!-- .row -->
                  </div><!-- .carousel-item -->
              <?php endif; ?>
          <?php endif; // end have_posts() ?>

</div>
     </div>
      </div>
    </div>
    </section>

    <?php //    the_excerpt();
    endwhile;

    wp_reset_postdata(); ?>
