<?php
/*
Plugin Name: FacetWP - Multilingual support
Description: Multilingual support for FacetWP
Version: 0.2.1
Author: FacetWP, LLC
Author URI: https://facetwp.com/
GitHub URI: facetwp/facetwp-i18n
*/

defined( 'ABSPATH' ) or exit;

class FWP_i18n
{

    function __construct() {
        add_action( 'init' , array( $this, 'init' ) );
    }


    /**
     * Intialize
     */
    function init() {
        if ( function_exists( 'FWP' ) ) {
            if ( function_exists( 'pll_register_string' ) ) {
                include( dirname( __FILE__ ) . '/includes/class-polylang.php' );
            }

            if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
                include( dirname( __FILE__ ) . '/includes/class-wpml.php' );
            }
        }
    }
}

new FWP_i18n();
