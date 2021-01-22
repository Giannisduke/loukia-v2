<?php
/**
 * Pro Designs and Plugins Feed
 *
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Action to add menu
add_action('admin_menu', 'mtm_register_design_page');

/**
 * Register plugin design page in admin menu
 * 
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */
function mtm_register_design_page() {
	add_submenu_page( 'mtm-settings', __('How it works - Maintenance Mode with Timer', 'maintenance-mode-with-timer'), __('How It Works', 'maintenance-mode-with-timer'), 'manage_options', 'mtm-how-it-work', 'mtm_designs_page' );
}

/**
 * Function to display plugin design HTML
 * 
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */
function mtm_designs_page() {

	$wpos_feed_tabs = mtm_help_tabs();
	$active_tab 	= isset($_GET['tab']) ? $_GET['tab'] : 'how-it-work';
?>
		
	<div class="wrap wpcdt-wrap">

		<h2 class="nav-tab-wrapper">
			<?php
			foreach ($wpos_feed_tabs as $tab_key => $tab_val) {
				$tab_name	= $tab_val['name'];
				$active_cls = ($tab_key == $active_tab) ? 'nav-tab-active' : '';
				$tab_link 	= add_query_arg( array( 'page' => 'mtm-how-it-work', 'tab' => $tab_key), admin_url('admin.php') );
			?>

			<a class="nav-tab <?php echo $active_cls; ?>" href="<?php echo $tab_link; ?>"><?php echo $tab_name; ?></a>

			<?php } ?>
		</h2>
		
		<div class="wpcdt-tab-cnt-wrp">
		<?php
			if( isset($active_tab) && $active_tab == 'how-it-work' ) {
				mtm_howitwork_page();
			}
			else if( isset($active_tab) && $active_tab == 'plugins-feed' ) {
				echo mtm_get_plugin_design( 'plugins-feed' );
			} else {
				echo mtm_get_plugin_design( 'offers-feed' );
			}
		?>
		</div><!-- end .wpcdt-tab-cnt-wrp -->

	</div><!-- end .wpcdt-wrap -->

<?php
}

/**
 * Gets the plugin design part feed
 *
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */
function mtm_get_plugin_design( $feed_type = '' ) {
	
	$active_tab = isset($_GET['tab']) ? $_GET['tab'] : '';
	
	// If tab is not set then return
	if( empty($active_tab) ) {
		return false;
	}

	// Taking some variables
	$wpos_feed_tabs = mtm_help_tabs();
	$transient_key 	= isset($wpos_feed_tabs[$active_tab]['transient_key']) 	? $wpos_feed_tabs[$active_tab]['transient_key'] 	: 'wpcdt_' . $active_tab;
	$url 			= isset($wpos_feed_tabs[$active_tab]['url']) 			? $wpos_feed_tabs[$active_tab]['url'] 				: '';
	$transient_time = isset($wpos_feed_tabs[$active_tab]['transient_time']) ? $wpos_feed_tabs[$active_tab]['transient_time'] 	: 172800;
	$cache 			= get_transient( $transient_key );
	
	if ( false === $cache ) {
		
		$feed 			= wp_remote_get( esc_url_raw( $url ), array( 'timeout' => 120, 'sslverify' => false ) );
		$response_code 	= wp_remote_retrieve_response_code( $feed );
		
		if ( ! is_wp_error( $feed ) && $response_code == 200 ) {
			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$cache = wp_remote_retrieve_body( $feed );
				set_transient( $transient_key, $cache, $transient_time );
			}
		} else {
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the data from the server. Please try again later.', 'maintenance-mode-with-timer' ) . '</div>';
		}
	}
	return $cache;	
}

/**
 * Function to get plugin feed tabs
 *
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */
function mtm_help_tabs() {
	$wpos_feed_tabs = array(
						'how-it-work' 	=> array(
													'name' => __('How It Works', 'maintenance-mode-with-timer'),
												),
						'plugins-feed' 	=> array(
													'name' 				=> __('Our Plugins', 'maintenance-mode-with-timer'),
													'url'				=> 'http://wponlinesupport.com/plugin-data-api/plugins-data.php',
													'transient_key'		=> 'wpos_plugins_feed',
													'transient_time'	=> 172800
												),
						'offers-feed' 	=> array(
													'name'				=> __('WPOS Offers', 'maintenance-mode-with-timer'),
													'url'				=> 'http://wponlinesupport.com/plugin-data-api/wpos-offers.php',
													'transient_key'		=> 'wpos_offers_feed',
													'transient_time'	=> 86400,
												)
					);
	return $wpos_feed_tabs;
}

/**
 * Function to get 'How It Works' HTML
 *
 * @package Maintenance Mode with Timer
 * @since 1.0.0
 */
function mtm_howitwork_page() { ?>
	
	<style type="text/css">
		.wpos-pro-box .hndle{background-color:#0073AA; color:#fff;}
		.wpos-pro-box .postbox{background:#dbf0fa none repeat scroll 0 0; border:1px solid #0073aa; color:#191e23;}
		.postbox-container .wpos-list li:before{font-family: dashicons; content: "\f139"; font-size:20px; color: #0073aa; vertical-align: middle;}
		.wpcdt-wrap .wpos-button-full{display:block; text-align:center; box-shadow:none; border-radius:0;}
		.wpcdt-shortcode-preview{background-color: #e7e7e7; font-weight: bold; padding: 2px 5px; display: inline-block; margin:0 0 2px 0;}
	</style>

	<div class="post-box-container">
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
			
				<!--How it workd HTML -->
				<div id="post-body-content">
					<div class="metabox-holder">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<div class="postbox-header">
									<h2 class="hndle">
										<span><?php _e( 'How It Works - Display and shortcode', 'maintenance-mode-with-timer' ); ?></span>
									</h2>
								</div>
								<div class="inside">
									<table class="form-table">
										<tbody>
											<tr>
												<th>
													<label><?php _e('Getting Started with Maintenance Mode -WPOS', 'maintenance-mode-with-timer'); ?>:</label>
												</th>
												<td>
													<ul>
														<li><?php _e('Step-1: This plugin creates a Maintenance Mode -WPOS tab in WordPress menu section', 'maintenance-mode-with-timer'); ?></li>
														<li><?php _e('Step-2: Go to Maintenance Mode -WPOS enable maintenance mode', 'maintenance-mode-with-timer'); ?></li>
														<li><?php _e('Step-3: you website in on maintenance mode now !!!', 'maintenance-mode-with-timer'); ?></li>
													</ul>
													<strong>NOTE: Maintenance Mode will be not seen to logged in users.</strong>
												</td>
											</tr>
										</tbody>
									</table>
								</div><!-- .inside -->
							</div><!-- #general -->
						</div><!-- .meta-box-sortables ui-sortable -->
					</div><!-- .metabox-holder -->
				</div><!-- #post-body-content -->
				
				<!--Upgrad to Pro HTML -->
				<div id="postbox-container-1" class="postbox-container">
					<div class="metabox-holder wpos-pro-box">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox" style="">
									
								<h3 class="hndle">
									<span><?php _e( 'Upgrade to Pro Version', 'maintenance-mode-with-timer' ); ?></span>
								</h3>
								<div class="inside">
									<p>
									<ul>
											  <li>5 attaractive template</li>
											  <li>Circle Countdown Timer</li>
											  <li>Flip Countdown Timer</li>
											  <li>12+ Timer Design</li>
											  <li>Custom CSS option</li>
											  <li>Newsletter Subscription form Integration.</li>
											  <li>Fully Responsive</li>
											  <li>100% Multilanguage</li>
										 </ul>
  </p> <br/>
									<a class="button button-primary wpos-button-full" href="https://www.wponlinesupport.com/pro-plugin-document/document-maintenance-mode-pro-wpos/?utm_source=hp&event=doc" target="_blank"><?php _e('Documentation', 'maintenance-mode-with-timer'); ?></a>	
									<p><a class="button button-primary wpos-button-full" href="http://demo.wponlinesupport.com/prodemo/maintenance-mode-pro-wpos/?utm_source=hp&event=demo" target="_blank"><?php _e('View PRO Demo ', 'maintenance-mode-with-timer'); ?></a></p>
								</div><!-- .inside -->
							</div><!-- #general -->
						</div><!-- .meta-box-sortables ui-sortable -->
					</div><!-- .metabox-holder -->

					<!-- Help to improve this plugin! -->
					<div class="metabox-holder">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox">
									<h3 class="hndle">
										<span><?php _e( 'Help to improve this plugin!', 'maintenance-mode-with-timer' ); ?></span>
									</h3>
									<div class="inside">
										<p>Enjoyed this plugin? You can help by rate this plugin <a href="https://wordpress.org/support/plugin/maintenance-mode-with-timer/reviews/?filter=5" target="_blank">5 stars!</a></p>
									</div><!-- .inside -->
							</div><!-- #general -->
						</div><!-- .meta-box-sortables ui-sortable -->
					</div><!-- .metabox-holder -->
				</div><!-- #post-container-1 -->

			</div><!-- #post-body -->
		</div><!-- #poststuff -->
	</div><!-- #post-box-container -->
<?php }