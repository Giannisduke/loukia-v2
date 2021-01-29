<?php
/**
 * Plugin Name: DHL Express for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/flexible-shipping-dhl-express/
 * Description: WooCommerce DHL Express Shipping Method and live rates.
 * Version: 1.4.0
 * Author: WP Desk
 * Author URI: https://flexibleshipping.com/
 * Text Domain: flexible-shipping-dhl-express
 * Domain Path: /lang/
 * Requires at least: 4.9
 * Tested up to: 5.6
 * WC requires at least: 4.6
 * WC tested up to: 5.0
 * Requires PHP: 7.0
 *
 * Copyright 2019 WP Desk Ltd.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package WPDesk\FlexibleShippingDhl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** Dummy plugin name and description - for translations only. */
$dummy_name       = __( 'DHL Express for WooCommerce', 'flexible-shipping-dhl-express' );
$dummy_desc       = __( 'WooCommerce DHL Express Shipping Method and live rates.', 'flexible-shipping-dhl-express' );
$dummy_author_uri = __( 'https://flexibleshipping.com/', 'flexible-shipping-dhl-express' );


/* THIS VARIABLE CAN BE CHANGED AUTOMATICALLY */
$plugin_version = '1.4.0';

$plugin_name        = 'DHL Express for WooCommerce';
$plugin_class_name  = '\WPDesk\FlexibleShippingDhl\Plugin';
$plugin_text_domain = 'flexible-shipping-dhl-express';
$product_id         = 'Flexible Shipping DHL Express';
$plugin_file        = __FILE__;
$plugin_dir         = dirname( __FILE__ );

define( $plugin_class_name, $plugin_version );
define( 'FLEXIBLE_SHIPPING_DHL_EXPRESS_VERSION', $plugin_version );

$requirements = array(
	'php'          => '7.0',
	'wp'           => '4.9',
	'repo_plugins' => array(
		array(
			'name'      => 'woocommerce/woocommerce.php',
			'nice_name' => 'WooCommerce',
			'version'   => '3.8',
		),
	),
);

require __DIR__ . '/vendor_prefixed/wpdesk/wp-plugin-flow/src/plugin-init-php52-free.php';
