<?php
/**
 * Admin Class
 *
 * Handles the Admin side functionality of plugin
 *
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Mtm_Admin {

	function __construct() {

		// Action to register admin menu
		add_action( 'admin_menu', array($this, 'mtm_register_menu') );

		// Action to register plugin settings
		add_action ( 'admin_init', array($this, 'mtm_register_settings') );

		// Action to Maintenance Mode
		add_action( 'wp_loaded', array($this,'mtm_maintenance_mode'), 5);
	}

	/**
	 * Function to register admin menus
	 * 
	 * @package Maintenance Mode with Timer
	 * @since 1.0.0
	 */
	function mtm_register_menu() {
		add_menu_page(__('Maintenance Mode - WPOS', 'maintenance-mode-with-timer'), __('Maintenance Mode - WPOS', 'maintenance-mode-with-timer'), 'manage_options', 'mtm-settings', array($this, 'mtm_main_page') );
	}

	/**
	 * Function to handle the setting page html
	 * 
	 * @package Maintenance Mode with Timer
	 * @since 1.0.0
	 */
	function mtm_main_page() {
		include_once( MTM_DIR . '/includes/admin/settings/mtm-pro-settings.php' );
	}

	/**
	 * Function register setings
	 * 
	 * @package Maintenance Mode with Timer
	 * @since 1.0.0
	 */
	function mtm_register_settings() {
		register_setting( 'mtm_plugin_options', 'mtm_options', array($this, 'mtm_validate_options') );
	}

	/**
	 * Validate Settings Options
	 * 
	 * @package Maintenance Mode with Timer
	 * @since 1.0.0
	 */
	function mtm_validate_options( $input ) {

		//general options
		$input['is_maintenance_mode']   			= !empty($input['is_maintenance_mode']) 				? 1 																		: 0;
		$input['maintenance_mode_company_logo']		= isset($input['maintenance_mode_company_logo']) 		? mtm_slashes_deep($input['maintenance_mode_company_logo']) 				: '';
		$input['maintenance_mode_company_logo_width'] = isset($input['maintenance_mode_company_logo_width']) 		? mtm_slashes_deep($input['maintenance_mode_company_logo_width'])	: '';
		$input['maintenance_mode_title'] 			= isset($input['maintenance_mode_title']) 				? mtm_slashes_deep($input['maintenance_mode_title']) 						: '';
		$input['maintenance_mode_text'] 			= isset($input['maintenance_mode_text']) 				? $input['maintenance_mode_text'] 											: '';
		
		// timer options
		$input['maintenance_mode_expire_time'] 		= isset($input['maintenance_mode_expire_time']) 		? mtm_slashes_deep($input['maintenance_mode_expire_time']) 					: '';
		
		//social options
		$input['mtm_facebook'] 						= isset($input['mtm_facebook']) 						? mtm_slashes_deep($input['mtm_facebook']) 									: '';
		$input['mtm_twitter'] 						= isset($input['mtm_twitter']) 							? mtm_slashes_deep($input['mtm_twitter']) 									: '';
		$input['mtm_linkedin'] 						= isset($input['mtm_linkedin']) 						? mtm_slashes_deep($input['mtm_linkedin']) 									: '';
		$input['mtm_github'] 						= isset($input['mtm_github']) 							? mtm_slashes_deep($input['mtm_github']) 									: '';
		$input['mtm_youtube'] 						= isset($input['mtm_youtube']) 							? mtm_slashes_deep($input['mtm_youtube']) 									: '';
		$input['mtm_pinterest'] 					= isset($input['mtm_pinterest']) 						? mtm_slashes_deep($input['mtm_pinterest']) 								: '';
		$input['mtm_instagram'] 					= isset($input['mtm_instagram']) 						? mtm_slashes_deep($input['mtm_instagram']) 								: '';
		$input['mtm_email'] 						= isset($input['mtm_email']) 							? mtm_slashes_deep($input['mtm_email']) 									: '';
		$input['mtm_google_plus']					= isset($input['mtm_google_plus']) 						? mtm_slashes_deep($input['mtm_google_plus']) 								: '';
		$input['mtm_tumblr'] 						= isset($input['mtm_tumblr']) 							? mtm_slashes_deep($input['mtm_tumblr']) 									: '';
		
		return $input;
	}

	/**
	* Function to add maintenance file
	* 
	* @package Maintenance Mode with Timer
	* @since 1.0.0
	*/
	function mtm_maintenance_mode() {

	    global $pagenow, $mtm_options;
	    
	    $maintenance 			= mtm_get_option('is_maintenance_mode');

		$mtm_date 				= mtm_esc_attr( mtm_get_option('maintenance_mode_expire_time') );

		// Creating compitible date according to UTF GMT time zone formate for older browwser
		$unique 				= mtm_get_unique();
		$mtm_date 				= date('F d, Y H:i:s', strtotime($mtm_date));

		$mtm_company_logo 		= mtm_get_option('maintenance_mode_company_logo');
		$mtm_company_logo_width = mtm_get_option('maintenance_mode_company_logo_width');
		$mtm_company_logo_width = (!empty($mtm_company_logo_width)) ? "style='width:".$mtm_company_logo_width."px'" : 'style="width:250px;"' ;
		$mtm_title 				= mtm_esc_attr( mtm_get_option('maintenance_mode_title') );
		$mtm_content 			= mtm_get_option('maintenance_mode_text');

		$mtm_bgtimer 			= mtm_get_option('maintenance_mode_expire_time');

		$mtm_facebook 			= esc_url(mtm_get_option('mtm_facebook'));
		$mtm_twitter 			= esc_url(mtm_get_option('mtm_twitter'));
		$mtm_linkedin 			= esc_url(mtm_get_option('mtm_linkedin'));
		$mtm_github 			= esc_url(mtm_get_option('mtm_github'));
		$mtm_youtube 			= esc_url(mtm_get_option('mtm_youtube'));
		$mtm_pinterest 			= esc_url(mtm_get_option('mtm_pinterest'));
		$mtm_instagram 			= esc_url(mtm_get_option('mtm_instagram'));
		$mtm_email 				= mtm_get_option('mtm_email');
		$mtm_google_plus 		= esc_url(mtm_get_option('mtm_google_plus'));
		$mtm_tumblr				= esc_url(mtm_get_option('mtm_tumblr'));

		// Compacting Variables
		$date_conf 	= compact('mtm_date');
		
	    if( !empty($maintenance) && $pagenow !== 'wp-login.php' && !is_user_logged_in() ) {
	        require_once( MTM_DIR . '/templates/maintenance-template.php' );
	        die();
	    }
	}
}

$mtm_admin = new Mtm_Admin();