<?php
/**
 * Plugin generic functions file
 *
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Update default settings
 * 
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */
function mtm_default_settings() {
    
    global $mtm_options;
    
    $mtm_options = array(

        // Genaral
        'is_maintenance_mode'                   => 0,
        'maintenance_mode_company_logo'         => MTM_URL.'assets/images/sample-logo.png',
        'maintenance_mode_company_logo_width'   => 250,
        'maintenance_mode_title'                => 'This Site is under construction',
        'maintenance_mode_text'                 => 'Thank you for visiting! We are currently performing scheduled maintenance and updates on the website.We will be back online to serve you in short. Thank you for your patience.',
        
        // Timer
        'maintenance_mode_expire_time'          => date( 'Y-m-d H:i:s', strtotime('+30 day', current_time('timestamp')) ),
        
        // Socials
        'mtm_facebook'                          => '',
        'mtm_twitter'                           => '',
        'mtm_linkedin'                          => '',
        'mtm_github'                            => '',
        'mtm_youtube'                           => '',
        'mtm_pinterest'                         => '',
        'mtm_instagram'                         => '',
        'mtm_email'                             => '',
        'mtm_google_plus'                       => '',
        'mtm_tumblr'                            => '',
    );
    
    $default_options = apply_filters('mtm_options_default_values', $mtm_options );
    
    // Update default options
    update_option( 'mtm_options', $default_options );

    // Overwrite global variable when option is update
    $mtm_options = mtm_get_settings();
}

/**
 * Escape Tags & Slashes
 *
 * Handles escapping the slashes and tags
 *
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */
function mtm_esc_attr($data) {
    return esc_attr( stripslashes($data) );
}

/**
 * Get an option
 * Looks to see if the specified setting exists, returns default if not
 * 
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */
function mtm_get_option( $key = '', $default = false ) {
    global $mtm_options;

    $value = ! empty( $mtm_options[ $key ] ) ? $mtm_options[ $key ] : $default;
    $value = apply_filters( 'mtm_get_option', $value, $key, $default );
    
    return apply_filters( 'mtm_get_option_' . $key, $value, $key, $default );
}

/**
 * Get Settings From Option Page
 * 
 * Handles to return all settings value
 * 
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */
function mtm_get_settings() {
  
    $options    = get_option('mtm_options');
    $settings   = is_array($options)  ? $options : array();
    
    return $settings;
}

/**
 * Strip Slashes From Array
 *
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */
function mtm_slashes_deep($data = array(), $flag = false) {
  
    if($flag != true) {
        $data = mtm_nohtml_kses($data);
    }
    $data = stripslashes_deep($data);
    return $data;
}

/**
 * Strip Html Tags 
 * 
 * It will sanitize text input (strip html tags, and escape characters)
 * 
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */

function mtm_nohtml_kses($data = array()) {
  
  if ( is_array($data) ) {
    
    $data = array_map('mtm_nohtml_kses', $data);
    
  } elseif ( is_string( $data ) ) {
    $data = trim( $data );
    $data = wp_filter_nohtml_kses($data);
  }
  
  return $data;
}

/**
 * Function to unique number value
 * 
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */
function mtm_get_unique() {
    static $unique = 0;
    $unique++;

    return $unique;
}