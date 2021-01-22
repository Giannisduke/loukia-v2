<?php
/**
 * Settings Page
 *
 * @package Maintenance Mode with Timer
 * @since 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap mtm-settings">

	<h2><?php _e( 'Maintenanace Mode Settings', 'maintenance-mode-with-timer' ); ?></h2><br />

	<?php
	// Success message
	if( isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' ) {
		echo '<div id="message" class="updated notice notice-success is-dismissible">
		<p>'.__("Your changes saved successfully.", "maintenance-mode-with-timer").'</p>
			  </div>';
	}
	?>

	<form action="options.php" method="POST" id="mtm-settings-form" class="mtm-settings-form">
		
		<?php
		    settings_fields( 'mtm_plugin_options' );
		?>

		<!-- General Settings Starts -->
		<div id="mtm-general-sett" class="post-box-container mtm-general-sett">
			<div class="metabox-holder">
				<div class="meta-box-sortables ui-sortable">
					<div id="general" class="postbox">
						<div class="postbox-header">
							<!-- Settings box title -->
							<h2 class="hndle">
								<span><?php _e( 'General Settings', 'maintenance-mode-with-timer' ); ?></span>
							</h2>
						</div>
						<div class="inside" id="123">
						
							<table class="form-table mtm-general-sett-tbl">
								<tbody>
									<tr>
										<th scope="row">
											<label for="mtm-enable-maintenance"><?php _e('Enable Maintenance Mode', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-enable-maintenance" type="checkbox" name="mtm_options[is_maintenance_mode]" value="1" <?php checked(mtm_get_option('is_maintenance_mode'),1); ?>/><br/>
											<span class="description"><?php _e('Check this box to enable maintenance mode.','maintenance-mode-with-timer'); ?></span>
										</td>
									</tr>

									<tr>
										<th scope="row">
											<label for="mtm-web-logo"><?php _e('Website Logo', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-web-logo" type="text" name="mtm_options[maintenance_mode_company_logo]" value="<?php echo mtm_esc_attr( mtm_get_option('maintenance_mode_company_logo') ); ?>" id="maintenance-mode-company-logo" class="regular-text mtm-default-img mtm-img-upload-input" />
											<input type="button" name="mtm_default_img" class="button-secondary mtm-img-uploader" value="<?php _e( 'Upload Image', 'maintenance-mode-with-timer'); ?>" />
											<input type="button" name="mtm_default_img_clear" id="mtm-default-img-clear" class="button button-secondary mtm-image-clear" value="<?php _e( 'Clear', 'maintenance-mode-with-timer'); ?>" /> <br />
											<span class="description"><?php _e( 'Upload website logo.', 'maintenance-mode-with-timer' ); ?></span>
											<?php
												$maintenance_mode_company_logo = '';
												if( mtm_get_option('maintenance_mode_company_logo') ) { 
													$maintenance_mode_company_logo = '<img src="'.mtm_get_option('maintenance_mode_company_logo').'" alt="" />';
												}
											?>
											<div class="mtm-imgs-preview"><?php echo $maintenance_mode_company_logo; ?></div>
										</td>
									</tr>

									<tr>
										<th scope="row">
											<label for="mtm-logo-width"><?php _e('Website Logo Width', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-logo-width" type="number" step="10" name="mtm_options[maintenance_mode_company_logo_width]" value="<?php echo mtm_esc_attr( mtm_get_option('maintenance_mode_company_logo_width') ); ?>"/> <?php _e('Px', 'maintenance-mode-with-timer'); ?><br/>
											<span class="description"><?php _e('Enter website logo width.','maintenance-mode-with-timer'); ?></span>
										</td>
									</tr>

									<tr>
										<th scope="row">
											<label for="mtm-title"><?php _e('Maintenance Mode Title', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-title" type="text" name="mtm_options[maintenance_mode_title]" value="<?php echo mtm_esc_attr( mtm_get_option('maintenance_mode_title') ); ?>" class="large-text" /><br/>
											<span class="description"><?php _e('Enter maintenance mode title.','maintenance-mode-with-timer'); ?></span>
										</td>
									</tr>

									<tr>
										<th scope="row">
											<label for="maintenance-mode-text"><?php _e('Enter Maintenance Mode Message', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<?php 
											$content 	= mtm_get_option('maintenance_mode_text');
											$editor_id 	= 'maintenance-mode-text';
											$settings 	= array(
																'media_buttons'	=> false,
																'textarea_rows'	=> 8,
																'textarea_name'	=> 'mtm_options[maintenance_mode_text]',
															);
											wp_editor($content, $editor_id, $settings); ?>
											<span class="description"><?php _e('Enter maintenance mode message.','maintenance-mode-with-timer'); ?></span>
										</td>
									</tr>

									<tr>
										<td colspan="2" valign="top" scope="row">
											<input type="submit" id="mtm-settings-submit" name="mtm-settings-submit" class="button button-primary right" value="<?php _e('Save Changes','maintenance-mode-with-timer'); ?>" />
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Timer Settings Starts -->
		<div id="mtm-general-sett" class="post-box-container mtm-general-sett">
			<div class="metabox-holder">
				<div class="meta-box-sortables ui-sortable">
					<div id="general" class="postbox">

						<button class="handlediv button-link" type="button"><span class="toggle-indicator"></span></button>

						<!-- Settings box title -->
						<h3 class="hndle">
							<span><?php _e( 'Timer Settings', 'maintenance-mode-with-timer' ); ?></span>
						</h3>
						
						<div class="inside">
							
							<table class="form-table mtm-general-sett-tbl">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="mtm-countdown-time-date"><?php _e('Expiry Date & Time', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<?php 	$date  = mtm_get_option('maintenance_mode_expire_time');
													$date  = ($date != '') ? $date : current_time('Y-m-d H:m:s'); ?>
											<input type="text" name="mtm_options[maintenance_mode_expire_time]" value="<?php echo mtm_esc_attr($date); ?>" class="regular-text mtm-countdown-time-date mtm-countdown-datepicker" id="mtm-countdown-time-date" /><br/>
											<span class="description"><?php _e('Select timer expiry Date and Time.', 'maintenance-mode-with-timer'); ?></span>
										</td>
									</tr>

									<tr>
										<td colspan="2" valign="top" scope="row">
											<input type="submit" id="mtm-settings-submit" name="mtm-settings-submit" class="button button-primary right" value="<?php _e('Save Changes','maintenance-mode-with-timer'); ?>" />
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Timer Settings Ends -->
		
		<!-- Socials Settings Starts -->
		<div id="mtm-general-sett" class="post-box-container mtm-general-sett">
			<div class="metabox-holder">
				<div class="meta-box-sortables ui-sortable">
					<div id="general" class="postbox">

						<button class="handlediv button-link" type="button"><span class="toggle-indicator"></span></button>

						<!-- Settings box title -->
						<h3 class="hndle">
							<span><?php _e( 'Socials Settings', 'maintenance-mode-with-timer' ); ?></span>
						</h3>
						
						<div class="inside">
						
							<table class="form-table mtm-general-sett-tbl">
								<tbody>
									<tr>
										<th scope="row">
											<label for="mtm-pro-fb"><?php _e('Facebook', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-pro-fb" type="url" name="mtm_options[mtm_facebook]" value="<?php echo mtm_esc_attr(mtm_get_option('mtm_facebook')); ?>" class="regular-text" /><br/>
											<span class="description"><?php _e('Enter facebook URl.','maintenance-mode-with-timer'); ?></span>
										</td>
										
										<th scope="row">
											<label for="mtm-pro-twitter"><?php _e('Twitter', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-pro-twitter" type="url" name="mtm_options[mtm_twitter]" value="<?php echo mtm_esc_attr(mtm_get_option('mtm_twitter')); ?>" class="regular-text" /><br/>
											<span class="description"><?php _e('Enter twitter URl.','maintenance-mode-with-timer'); ?></span>
										</td>
									</tr>

									<tr>
										<th scope="row">
											<label for="mtm-pro-linkedin"><?php _e('Linkedin', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-pro-linkedin" type="url" name="mtm_options[mtm_linkedin]" value="<?php echo mtm_esc_attr(mtm_get_option('mtm_linkedin')); ?>" class="regular-text" /><br/>
											<span class="description"><?php _e('Enter linkedin URl.','maintenance-mode-with-timer'); ?></span>
										</td>
								
										<th scope="row">
											<label for="mtm-pro-github"><?php _e('Github', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-pro-github" type="url" name="mtm_options[mtm_github]" value="<?php echo mtm_esc_attr(mtm_get_option('mtm_github')); ?>" class="regular-text" /><br/>
											<span class="description"><?php _e('Enter github URl.','maintenance-mode-with-timer'); ?></span>
										</td>
									</tr>

									<tr>
										<th scope="row">
											<label for="mtm-pro-yoututbe"><?php _e('Youtube', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-pro-yoututbe" type="url" name="mtm_options[mtm_youtube]" value="<?php echo mtm_esc_attr(mtm_get_option('mtm_youtube')); ?>" class="regular-text" /><br/>
											<span class="description"><?php _e('Enter github URl.','maintenance-mode-with-timer'); ?></span>
										</td>
									
										<th scope="row">
											<label for="mtm-pro-pinterest"><?php _e('Pinterest', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-pro-pinterest" type="url" name="mtm_options[mtm_pinterest]" value="<?php echo mtm_esc_attr(mtm_get_option('mtm_pinterest')); ?>" class="regular-text" /><br/>
											<span class="description"><?php _e('Enter pinterest URl.','maintenance-mode-with-timer'); ?></span>
										</td>
									</tr>

									<tr>
										<th scope="row">
											<label for="mtm-pro-insta"><?php _e('Instagram', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-pro-insta" type="url" name="mtm_options[mtm_instagram]" value="<?php echo mtm_esc_attr(mtm_get_option('mtm_instagram')); ?>" class="regular-text" /><br/>
											<span class="description"><?php _e('Enter instagram URl.','maintenance-mode-with-timer'); ?></span>
										</td>
									
										<th scope="row">
											<label for="mtm-pro-email"><?php _e('Email', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-pro-email" type="email" name="mtm_options[mtm_email]" value="<?php echo mtm_esc_attr(mtm_get_option('mtm_email')); ?>" class="regular-text" /><br/>
											<span class="description"><?php _e('Enter Your Email Address.','maintenance-mode-with-timer'); ?></span>
										</td>
									</tr>

									<tr>
										<th scope="row">
											<label for="mtm-pro-gplus"><?php _e('Google+', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-pro-gplus" type="url" name="mtm_options[mtm_google_plus]" value="<?php echo mtm_esc_attr(mtm_get_option('mtm_google_plus')); ?>" class="regular-text" /><br/>
											<span class="description"><?php _e('Enter google plus URl.','maintenance-mode-with-timer'); ?></span>
										</td>
									
										<th scope="row">
											<label for="mtm-pro-tubmlr"><?php _e('Tumblr', 'maintenance-mode-with-timer'); ?></label>
										</th>
										<td>
											<input id="mtm-pro-tubmlr" type="url" name="mtm_options[mtm_tumblr]" value="<?php echo mtm_esc_attr(mtm_get_option('mtm_tumblr')); ?>" class="regular-text" /><br/>
											<span class="description"><?php _e('Enter tumblr URL.','maintenance-mode-with-timer'); ?></span>
										</td>
									</tr>
									<tr>
										<td colspan="4" valign="top" scope="row">
											<input type="submit" id="mtm-settings-submit" name="mtm-settings-submit" class="button button-primary right" value="<?php _e('Save Changes','maintenance-mode-with-timer'); ?>" />
										</td>
									</tr>
								</tbody>
						 	</table>
						</div><!-- .inside -->
					</div><!-- #general -->
				</div><!-- .meta-box-sortables ui-sortable -->
			</div><!-- .metabox-holder -->
		</div><!-- #mtm-general-sett -->
		<!-- Socials Settings Ends -->
	</form><!-- end .mtm-settings-form -->
</div><!-- end .mtm-settings -->