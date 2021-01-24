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
  'lib/bs4navwalker.php' // Bootstrap Menu
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'loukia'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

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

add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
	if ( isset( $query->query_vars['facetwp'] ) ) {
		$is_main_query = (bool) $query->query_vars['facetwp'];
	}
	return $is_main_query;
}, 10, 2 );

####################################################
#    Woocommerce remove css
####################################################
function loukia_dequeue_styles( $enqueue_styles ) {
  unset( $enqueue_styles['woocommerce-general'] );	// Remove the gloss
  unset( $enqueue_styles['woocommerce-layout'] );		// Remove the layout
  unset( $enqueue_styles['woocommerce-smallscreen'] );	// Remove the smallscreen optimisation
  return $enqueue_styles;
}
add_filter( 'woocommerce_enqueue_styles', 'loukia_dequeue_styles' );


####################################################
#    Woocommerce Actions
####################################################

remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
//remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_main_content_product', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

function facet_selections() {
  echo facetwp_display('selections');
}
add_action( 'woocommerce_before_shop_loop', 'facet_selections', 40 );



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


  add_filter('woocommerce_form_field_country', 'clean_checkout_fields_class_attribute_values', 20, 4);
  add_filter('woocommerce_form_field_state', 'clean_checkout_fields_class_attribute_values', 20, 4);
  add_filter('woocommerce_form_field_textarea', 'clean_checkout_fields_class_attribute_values', 20, 4);
  add_filter('woocommerce_form_field_checkbox', 'clean_checkout_fields_class_attribute_values', 20, 4);
  add_filter('woocommerce_form_field_password', 'clean_checkout_fields_class_attribute_values', 20, 4);
  add_filter('woocommerce_form_field_text', 'clean_checkout_fields_class_attribute_values', 20, 4);
  add_filter('woocommerce_form_field_email', 'clean_checkout_fields_class_attribute_values', 20, 4);
  add_filter('woocommerce_form_field_tel', 'clean_checkout_fields_class_attribute_values', 20, 4);
  add_filter('woocommerce_form_field_number', 'clean_checkout_fields_class_attribute_values', 20, 4);
  add_filter('woocommerce_form_field_select', 'clean_checkout_fields_class_attribute_values', 20, 4);
  add_filter('woocommerce_form_field_radio', 'clean_checkout_fields_class_attribute_values', 20, 4);
  function clean_checkout_fields_class_attribute_values( $field, $key, $args, $value ){
      if( is_checkout() ){
          // remove "form-row"
          $field = str_replace( array('<p class="form-row ', '<p class="form-row'), array('<p class="', '<p class="'), $field);
      }

      return $field;
  }

  add_filter('woocommerce_checkout_fields', 'custom_checkout_fields_class_attribute_value', 20, 1);
  function custom_checkout_fields_class_attribute_value( $fields ){
      foreach( $fields as $fields_group_key => $group_fields_values ){
          foreach( $group_fields_values as $field_key => $field ){
              // Remove other classes (or set yours)
              $fields[$fields_group_key][$field_key]['class'] = array('');
          }
      }

      return $fields;
  }



  function lv2_add_bootstrap_input_classes( $args, $key, $value = null ) {

  	/* This is not meant to be here, but it serves as a reference
  	of what is possible to be changed.
  	$defaults = array(
  		'type'			  => 'text',
  		'label'			 => '',
  		'description'	   => '',
  		'placeholder'	   => '',
  		'maxlength'		 => false,
  		'required'		  => false,
  		'id'				=> $key,
  		'class'			 => array(),
  		'label_class'	   => array(),
  		'input_class'	   => array(),
  		'return'			=> false,
  		'options'		   => array(),
  		'custom_attributes' => array(),
  		'validate'		  => array(),
  		'default'		   => '',
  	); */

  	// Start field type switch case
  	switch ( $args['type'] ) {

  		case "select" :  /* Targets all select input type elements, except the country and state select input types */
  			$args['class'][] = 'form-group'; // Add a class to the field's html element wrapper - woocommerce input types (fields) are often wrapped within a <p></p> tag
  			$args['input_class'] = array('form-control', 'input-lg'); // Add a class to the form input itself
  			//$args['custom_attributes']['data-plugin'] = 'select2';
  			$args['label_class'] = array('control-label');
  			$args['custom_attributes'] = array( 'data-plugin' => 'select2', 'data-allow-clear' => 'true', 'aria-hidden' => 'true',  ); // Add custom data attributes to the form input itself
  		break;

  		case 'country' : /* By default WooCommerce will populate a select with the country names - $args defined for this specific input type targets only the country select element */
  			$args['class'][] = 'form-group single-country';
  			$args['label_class'] = array('control-label');
  		break;

  		case "state" : /* By default WooCommerce will populate a select with state names - $args defined for this specific input type targets only the country select element */
  			$args['class'][] = 'form-group'; // Add class to the field's html element wrapper
  			$args['input_class'] = array('form-control', 'input-lg'); // add class to the form input itself
  			//$args['custom_attributes']['data-plugin'] = 'select2';
  			$args['label_class'] = array('control-label');
  			$args['custom_attributes'] = array( 'data-plugin' => 'select2', 'data-allow-clear' => 'true', 'aria-hidden' => 'true',  );
  		break;


  		case "password" :
  		case "text" :
  		case "email" :
  		case "tel" :
  		case "number" :
  			$args['class'][] = 'form-group';
  			//$args['input_class'][] = 'form-control input-lg'; // will return an array of classes, the same as bellow
  			$args['input_class'] = array('form-control', 'input-lg');
  			$args['label_class'] = array('control-label');
  		break;

  		case 'textarea' :
  			$args['input_class'] = array('form-control', 'input-lg');
  			$args['label_class'] = array('control-label');
  		break;

  		case 'checkbox' :
  		break;

  		case 'radio' :
  		break;

  		default :
  			$args['class'][] = 'form-group';
  			$args['input_class'] = array('form-control', 'input-lg');
  			$args['label_class'] = array('control-label');
  		break;
  	}

  	return $args;
  }
  add_filter('woocommerce_form_field_args','lv2_add_bootstrap_input_classes',10,3);

  add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

  function woo_remove_product_tabs( $tabs ) {
      unset( $tabs['description'] );          // Remove the description tab
      unset( $tabs['reviews'] );          // Remove the reviews tab
      unset( $tabs['additional_information'] );   // Remove the additional information tab
      return $tabs;
  }

function get_product_description() {
  the_content();
}
add_action ('woocommerce_single_product_summary', 'get_product_description', 35 );
