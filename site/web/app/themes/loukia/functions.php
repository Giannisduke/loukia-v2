<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php', // Theme customizer
  'lib/sidebar_widget.php', // widget sidebar
  'lib/cpt.php', // Theme Custom Post Types
  'lib/bs4navwalker.php', // Bootstrap Menu
  'lib/woocommerce.php' // eboy custom woocommerce
  //'lib/custom-product-variations_light.php'
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'loukia'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

remove_action('wp_head', '_admin_bar_bump_cb');

####################################################
#    Add svg & swf support
####################################################
function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
  //  $mimes['swf']  = 'application/x-shockwave-flash';
    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

####################################################
#    Theme customizer Logo setup
####################################################
  function themename_custom_logo_setup() {
   $defaults = array(
   'height'      => 100,
   'width'       => 400,
   'flex-height' => true,
   'flex-width'  => true,
   'header-text' => array( 'site-title', 'site-description' ),
  'unlink-homepage-logo' => false,
   );
   add_theme_support( 'custom-logo', $defaults );
  }
  add_action( 'after_setup_theme', 'themename_custom_logo_setup' );

add_filter('acf/save_post', 'gallery_to_thumbnail');
function gallery_to_thumbnail($post_id) {
	$gallery = get_field('collection_gallery', $post_id, false);
	if (!empty($gallery)) {
		$image_id = $gallery[0];
		set_post_thumbnail($post_id, $image_id);
	}
}
add_filter('acf/save_post', 'portfolio_gallery_to_thumbnail');
function portfolio_gallery_to_thumbnail($post_id) {
	$portfolio_gallery = get_field('portfolio_photos', $post_id, false);
	if (!empty($portfolio_gallery)) {
		$image_id = $portfolio_gallery[0];
		set_post_thumbnail($post_id, $image_id);
	}
}

add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
	if ( isset( $query->query_vars['facetwp'] ) ) {
		$is_main_query = (bool) $query->query_vars['facetwp'];
	}
	return $is_main_query;
}, 10, 2 );

function facet_pager() {
  echo facetwp_display( 'facet', 'products_pager' );
}
add_action( 'woocommerce_after_shop_loop', 'facet_pager', 20 );

  add_filter( 'facetwp_preload_url_vars', function( $url_vars ) {
      if ( 'loukia/shop' == FWP()->helper->get_uri() ) {
          if ( empty( $url_vars['availability'] ) ) {
              $url_vars['availability'] = [ '39dc2a508a94d621efa3c94005e639d0' ];
          }
      }
      return $url_vars;
  } );



add_filter( 'get_the_archive_title', function ($title) {
    if ( is_category() ) {
            $title = single_cat_title( '', false );
        } elseif ( is_tag() ) {
            $title = single_tag_title( '', false );
        } elseif ( is_author() ) {
            $title = '<span class="vcard">' . get_the_author() . '</span>' ;
        } elseif ( is_tax() ) { //for custom post types
            $title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
        } elseif (is_post_type_archive()) {
            $title = post_type_archive_title( '', false );
        }
    return $title;
});
