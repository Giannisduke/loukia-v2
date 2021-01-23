/**
 * Viva Wallet for WooCommerce
 *
 * Copyright: (c) 2020 VivaWallet.com
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * May 2020
 *
 * @package VivaWallet
 */

jQuery( document ).ready(
	function () {

		var $test_mode_checkbox = jQuery( '#woocommerce_vivawallet_native_test_mode' );
		var demo_mode;

		var $test_advanced_settings_checkbox = jQuery( '#woocommerce_vivawallet_native_advanced_settings_enabled' );
		var advancedSettingsSelected;

		if ( $test_mode_checkbox.is( ':checked' )) {
			woo_vivawallet_show_test_mode_inputs();
			woo_vivawallet_hide_live_mode_inputs();
			demo_mode = true;
		} else {
			woo_vivawallet_hide_test_mode_inputs();
			woo_vivawallet_show_live_mode_inputs();
			demo_mode = false;
		}

		if ( $test_advanced_settings_checkbox.is( ':checked' )) {
			advancedSettingsSelected = true;
			woo_vivawallet_show_advanced_settings_inputs();

		} else {
			advancedSettingsSelected = false;
			woo_vivawallet_hide_advanced_settings_inputs();
		}

		$test_mode_checkbox.change(
			function () {
				if ( this.checked ) {
					woo_vivawallet_show_test_mode_inputs();
					woo_vivawallet_hide_live_mode_inputs();
					demo_mode = true;
				} else {
					woo_vivawallet_hide_test_mode_inputs();
					woo_vivawallet_show_live_mode_inputs();
					demo_mode = false;
				}
			}
		);

		$test_advanced_settings_checkbox.change(
			function () {
				if ( this.checked ) {
					advancedSettingsSelected = true;
					woo_vivawallet_show_advanced_settings_inputs();
				} else {
					advancedSettingsSelected = false;
					woo_vivawallet_hide_advanced_settings_inputs();
				}
			}
		);

		function woo_vivawallet_show_test_mode_inputs()
		{
			jQuery( '#woocommerce_vivawallet_native_title_3' ).show();
			jQuery( '#woocommerce_vivawallet_native_test_client_id' ).parent().parent().parent().show();
			jQuery( '#woocommerce_vivawallet_native_test_client_secret' ).parent().parent().parent().show();
			if ( advancedSettingsSelected ) {
				jQuery( '#woocommerce_vivawallet_native_test_source_code' ).parent().parent().parent().show();
			}
		}

		function woo_vivawallet_hide_test_mode_inputs()
		{
			jQuery( '#woocommerce_vivawallet_native_title_3' ).hide();
			jQuery( '#woocommerce_vivawallet_native_test_client_id' ).parent().parent().parent().hide();
			jQuery( '#woocommerce_vivawallet_native_test_client_secret' ).parent().parent().parent().hide();

			jQuery( '#woocommerce_vivawallet_native_test_source_code' ).parent().parent().parent().hide();
		}

		function woo_vivawallet_show_live_mode_inputs()
		{
			jQuery( '#woocommerce_vivawallet_native_title_2' ).show();
			jQuery( '#woocommerce_vivawallet_native_client_id' ).parent().parent().parent().show();
			jQuery( '#woocommerce_vivawallet_native_client_secret' ).parent().parent().parent().show();

			if ( advancedSettingsSelected ) {
				jQuery( '#woocommerce_vivawallet_native_source_code' ).parent().parent().parent().show();
			}
		}

		function woo_vivawallet_hide_live_mode_inputs()
		{
			jQuery( '#woocommerce_vivawallet_native_title_2' ).hide();
			jQuery( '#woocommerce_vivawallet_native_client_id' ).parent().parent().parent().hide();
			jQuery( '#woocommerce_vivawallet_native_client_secret' ).parent().parent().parent().hide();

			jQuery( '#woocommerce_vivawallet_native_source_code' ).parent().parent().parent().hide();
		}

		function woo_vivawallet_show_advanced_settings_inputs()
		{
			jQuery( '#woocommerce_vivawallet_native_main_descr' ).show();
			jQuery( '#woocommerce_vivawallet_native_title' ).parent().parent().parent().show();
			jQuery( '#woocommerce_vivawallet_native_description' ).parent().parent().parent().show();
			if ( "1" === vivawallet_admin_params.allowInstalments ) {
				jQuery( '#woocommerce_vivawallet_native_instalments' ).parent().parent().parent().show();
			} else {
				jQuery( '#woocommerce_vivawallet_native_instalments' ).parent().parent().parent().hide();
			}

			if ( demo_mode ) {
				jQuery( '#woocommerce_vivawallet_native_test_source_code' ).parent().parent().parent().show();
				jQuery( '#woocommerce_vivawallet_native_source_code' ).parent().parent().parent().hide();
			} else {
				jQuery( '#woocommerce_vivawallet_native_test_source_code' ).parent().parent().parent().hide();
				jQuery( '#woocommerce_vivawallet_native_source_code' ).parent().parent().parent().show();
			}

			jQuery( '#woocommerce_vivawallet_native_logo_enabled' ).parent().parent().parent().parent().show();
			jQuery( '#woocommerce_vivawallet_native_cc_logo_enabled' ).parent().parent().parent().parent().show();
			jQuery( '#woocommerce_vivawallet_native_order_status' ).parent().parent().parent().show();
		}

		function woo_vivawallet_hide_advanced_settings_inputs()
		{
			jQuery( '#woocommerce_vivawallet_native_main_descr' ).hide();
			jQuery( '#woocommerce_vivawallet_native_title' ).parent().parent().parent().hide();
			jQuery( '#woocommerce_vivawallet_native_description' ).parent().parent().parent().hide();
			jQuery( '#woocommerce_vivawallet_native_instalments' ).parent().parent().parent().hide();

			jQuery( '#woocommerce_vivawallet_native_test_source_code' ).parent().parent().parent().hide();
			jQuery( '#woocommerce_vivawallet_native_source_code' ).parent().parent().parent().hide();

			jQuery( '#woocommerce_vivawallet_native_logo_enabled' ).parent().parent().parent().parent().hide();
			jQuery( '#woocommerce_vivawallet_native_cc_logo_enabled' ).parent().parent().parent().parent().hide();
			jQuery( '#woocommerce_vivawallet_native_order_status' ).parent().parent().parent().hide();
		}
	}
);
