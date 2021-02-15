<?php // eboy custom woocommerce

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

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
function facet_selections() {
  echo facetwp_display('selections');
}
add_action( 'woocommerce_before_shop_loop', 'facet_selections', 40 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );


####################################################
#    Woocommerce Forms
####################################################

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


  ####################################################
  #    Woocommerce Tabs
  ####################################################

  add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

  function woo_remove_product_tabs( $tabs ) {
      unset( $tabs['description'] );          // Remove the description tab
      unset( $tabs['reviews'] );          // Remove the reviews tab
      unset( $tabs['additional_information'] );   // Remove the additional information tab
      return $tabs;
  }

  ####################################################
  #    Woocommerce Single Product
  ####################################################

function get_product_description() {
  the_content();
}
add_action ('woocommerce_single_product_summary', 'get_product_description', 35 );

function eboy_get_product_tag_original() {
  $output = array();

// get an array of the WP_Term objects for a defined product ID
$terms = wp_get_post_terms( get_the_id(), 'product_tag' );

// Loop through each product tag for the current product
if( count($terms) > 0 ){
    foreach($terms as $term){
        $term_id = $term->term_id; // Product tag Id
        $term_name = $term->name; // Product tag Name
        $term_slug = $term->slug; // Product tag slug
        $term_link = get_term_link( $term, 'product_tag' ); // Product tag link

        // Set the product tag names in an array
        $output[] = '<li class="list-group-item">#<a href="'.$term_link.'"><h3>'.$term_name.'</h3></a></li>';
    }
    // Set the array in a coma separated string of product tags for example
    $output = implode( ' ', $output );

    // Display the coma separated string of the product tags
    //echo $output;
    echo '<nav aria-label="breadcrumb">';
    echo '<ol class="breadcrumb">' . $output . '</ol>';
    echo '</nav>';
}
}
add_action ('woocommerce_single_product_summary', 'eboy_get_product_tag_original', 6 );

function eboy_get_product_tag() {

// The input(s).
$product_id = null;
$sep = '<li class="breadcrumb-item"># ';
$before = '<ol class="breadcrumb">';
$after = '</ol>';
$sep_2 = '</li>';

// NOTICE! Understand what this does before running.
$result = wc_get_product_tag_list( $product_id, $sep, $before, $after, $sep_2 );

echo '<nav aria-label="breadcrumb">';
//echo '<ol class="breadcrumb">' . $result . '</ol>';
echo $result;
echo '</nav>">';

}
//add_action ('woocommerce_single_product_summary', 'eboy_get_product_tag', 6);

/**
* Change number of related products output
*/
function woo_related_products_limit() {
global $product;

$args['posts_per_page'] = 6;
return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args', 20 );
function jk_related_products_args( $args ) {
$args['posts_per_page'] = 6; // 4 related products
$args['columns'] = 2; // arranged in 2 columns
return $args;
}

function eboy_get_sku() {
  global $product;
    if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

      <span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>

<?php endif;
}
add_action( 'woocommerce_single_product_summary', 'eboy_get_sku', 31 );

function eboy_get_product_category() {
  global $product;
  echo '<ul class="nav navbar-nav list-inline">';
  //echo wc_get_product_category_list( $product->get_id(), '<li class="posted_in">' . _n( '', '', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</li>' );
  echo wc_get_product_category_list( $product->get_id(), ' # ', '<li class="list-inline-item">' . _n( '', '', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</li>' );
  echo '</ul>';
  }
add_action( 'woocommerce_single_product_summary', 'eboy_get_product_category', 2 );

function eboy_get_product_attributes_values() {

  global $product;
  $product_size = $product->get_attribute( 'pa_size' );

}
//add_action( 'woocommerce_single_product_summary', 'eboy_get_product_attributes_values', 7 );


function nt_product_attributes() {
global $product;
    if ( $product->has_attributes() ) {

        $attributes = ( object ) array (
        'color'              => $product->get_attribute( 'pa_color' ),
        'size'            => $product->get_attribute( 'pa_size' ),
        );
    return $attributes;
    }
    echo 'test';
    echo $attributes->color;
    echo $attributes->size;
    echo 'test end';
}
add_action( 'woocommerce_single_product_summary', 'nt_product_attributes', 8 );
