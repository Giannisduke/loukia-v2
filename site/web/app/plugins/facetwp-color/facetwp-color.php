<?php
/*
Plugin Name: FacetWP - Color
Description: Filter results by color
Version: 1.4.1
Author: FacetWP, LLC
Author URI: https://facetwp.com/
GitHub URI: facetwp/facetwp-color
*/

defined( 'ABSPATH' ) or exit;


/**
 * FacetWP registration hook
 */
add_filter( 'facetwp_facet_types', function( $types ) {
    include( dirname( __FILE__ ) . '/class-color.php' );
    $types['color'] = new FacetWP_Facet_Color_Addon();
    return $types;
});
