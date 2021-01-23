<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$vivawallet_woo_docs_url = 'https://developer.vivawallet.com/e-commerce-plugins/woocommerce/';
$vivawallet_demo_url     = 'https://demo.vivapayments.com/';
$vivawallet_live_url     = 'https://www.vivapayments.com/';

/* translators: credentials */
$main_desc = __(
	'Set the title and description of the payment gateway. Title and description are visible to end users in the checkout page.',
	'woocommerce_vivawallet'
);

/* translators: credentials */
$credentials_desc = sprintf( __( 'To find out how to retrieve your credentials for the payment gateway, please visit the Viva Wallet for WooCommerce <a target="_blank" href="%s">installation guide</a>.', 'woocommerce_vivawallet' ), $vivawallet_woo_docs_url );
/* translators: Demo Mode */
$test_mode_desc = sprintf( __( 'If Demo Mode is enabled, please use the credentials you got from <a target="_blank" href="%s">demo.vivapayments.com</a>.', 'woocommerce_vivawallet' ), $vivawallet_demo_url );


return apply_filters(
	'wc_vivawallet_settings',
	array(

		'main_title'                => array(
			'title' => __( 'Viva Wallet for WooCommerce settings', 'woocommerce_vivawallet' ),
			'type'  => 'title',
		),

		'enabled'                   => array(
			'title'   => __( 'Enable Viva Wallet', 'woocommerce_vivawallet' ),
			'type'    => 'checkbox',
			'label'   => __( 'Enable Viva Wallet Gateway to receive payments from all major credit cards through your Viva Wallet account.', 'woocommerce_vivawallet' ),
			'default' => 'no',
		),


		'sep'                       => array(
			'title'       => '',
			'type'        => 'title',
			'description' => '<hr>',
		),

		'credentials'               => array(
			'title'       => __( 'Set Viva Wallet API credentials', 'woocommerce_vivawallet' ),
			'type'        => 'title',
			'description' => $credentials_desc,
		),
		'test_mode'                 => array(
			'title'       => __( 'Demo mode', 'woocommerce_vivawallet' ),
			'type'        => 'checkbox',
			'label'       => __( 'Enable demo mode', 'woocommerce_vivawallet' ),
			'description' => $test_mode_desc,
			'default'     => 'yes',
		),



		'title_2'                   => array(
			'title' => __( 'Live mode credentials', 'woocommerce_vivawallet' ),
			'type'  => 'title',
		),

		'title_3'                   => array(
			'title' => __( 'Demo mode credentials', 'woocommerce_vivawallet' ),
			'type'  => 'title',
		),

		'client_id'                 => array(
			'title'       => __( 'Live Client ID', 'woocommerce_vivawallet' ),
			'type'        => 'text',
			'description' => __( 'Client ID provided by Viva Wallet.', 'woocommerce_vivawallet' ),
			'default'     => '',
		),
		'test_client_id'            => array(
			'title'       => __( 'Demo Client ID', 'woocommerce_vivawallet' ),
			'type'        => 'text',
			'description' => __( 'Client ID provided by Viva Wallet. ', 'woocommerce_vivawallet' ),
			'default'     => '',
		),



		'client_secret'             => array(
			'title'       => __( 'Live Client Secret', 'woocommerce_vivawallet' ),
			'type'        => 'text',
			'description' => __( 'Client Secret provided by Viva Wallet.', 'woocommerce_vivawallet' ),
			'default'     => '',
		),
		'test_client_secret'        => array(
			'title'       => __( 'Demo Client Secret', 'woocommerce_vivawallet' ),
			'type'        => 'text',
			'description' => __( 'Client Secret provided by Viva Wallet.', 'woocommerce_vivawallet' ),
			'default'     => '',
		),




		'sep_2'                     => array(
			'title'       => '',
			'type'        => 'title',
			'description' => '<hr>',
		),



		'advanced_settings_title'   => array(
			'title' => __( 'Advanced settings', 'woocommerce_vivawallet' ),
			'type'  => 'title',
		),

		'advanced_settings_enabled' => array(
			'title'   => __( 'Show advanced settings', 'woocommerce_vivawallet' ),
			'type'    => 'checkbox',
			'label'   => __( 'Show advanced settings. If this checkbox is unchecked, the plugin will use default settings.', 'woocommerce_vivawallet' ),
			'default' => 'no',
		),

		'sep_3'                     => array(
			'title'       => '',
			'type'        => 'title',
			'description' => '<hr>',
		),


		'main_descr'                => array(
			'title' => $main_desc,
			'type'  => 'title',
		),
		'title'                     => array(
			'title'       => __( 'Title', 'woocommerce_vivawallet' ),
			'type'        => 'text',
			'description' => __( 'This controls the title which the user sees on checkout page.', 'woocommerce_vivawallet' ),
			'default'     => __( 'Payment card (Viva Wallet)', 'woocommerce_vivawallet' ),
		),
		'description'               => array(
			'title'       => __( 'Description', 'woocommerce_vivawallet' ),
			'type'        => 'text',
			'description' => __( 'This controls the description which the user sees on checkout page.', 'woocommerce_vivawallet' ),
			'default'     => __( 'Cards accepted: Visa, MasterCard, Maestro, Amex and more.', 'woocommerce_vivawallet' ),
		),


		'instalments'               => array(
			'title'       => __( 'Installments', 'woocommerce_vivawallet' ),
			'type'        => 'text',
			'description' => __( 'WARNING: Only available to Greek Viva Wallet accounts. <br>Example: 90:3,180:6<br>Order total 90 euro -> allow 0 and 3 installments <br>Order total 180 euro -> allow 0, 3 and 6 installments<br>Leave empty in case you do not want to offer installments.', 'woocommerce_vivawallet' ),
			'default'     => '',
		),

		'source_code'               => array(
			'title'       => __( 'Live Source Code List', 'woocommerce_vivawallet' ),
			'type'        => 'select',
			'description' => __( 'Provides a list with all source codes that are set in your Viva Wallet banking app.', 'woocommerce_vivawallet' ),
			'default'     => '',
			'options'     => array(),
		),
		'test_source_code'          => array(
			'title'       => __( 'Demo Source Code List', 'woocommerce_vivawallet' ),
			'type'        => 'select',
			'description' => __( 'Provides a list with all source codes that are set in the Viva Wallet banking app', 'woocommerce_vivawallet' ),
			'default'     => '',
			'options'     => array(),
		),

		'logo_enabled'              => array(
			'title'   => __( 'Show Viva Wallet logo.', 'woocommerce_vivawallet' ),
			'type'    => 'checkbox',
			'label'   => __( 'Enable Viva Wallet logo on checkout page (default = yes).', 'woocommerce_vivawallet' ),
			'default' => 'yes',
		),
		'cc_logo_enabled'           => array(
			'title'   => __( 'Show credit card logo on checkout page. ', 'woocommerce_vivawallet' ),
			'type'    => 'checkbox',
			'label'   => __( 'Enable credit card logo in the form element input as the user types credit card number info (default = yes).', 'woocommerce_vivawallet' ),
			'default' => 'yes',
		),
		'order_status'              => array(
			'title'       => __( 'Order status after successful payment.', 'woocommerce_vivawallet' ),
			'description' => __( 'Your WooCommerce will be updated to this status after successful payment on Viva Wallet (default = completed).', 'woocommerce_vivawallet' ),
			'default'     => 'completed',
			'type'        => 'select',
			'options'     => array(
				'completed'  => __( 'Completed', 'woocommerce_vivawallet' ),
				'processing' => __( 'Processing', 'woocommerce_vivawallet' ),
			),
		),

		// helpers.. dont delete..

		'source_error'              => array(
			'default' => '',
			'title'   => '',
			'type'    => 'title',
		),

	)
);
