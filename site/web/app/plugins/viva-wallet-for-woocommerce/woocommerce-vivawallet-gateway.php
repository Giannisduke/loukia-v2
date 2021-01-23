<?php
/**
 * Plugin Name: Viva Wallet for WooCommerce
 * Plugin URI: http://www.vivawallet.com/
 * Description: Connects WooCommerce to Viva Wallet payment gateway (native checkout) to process and sync your payments and help you sell more.
 * ShortDescription: Viva Wallet for WooCommerce
 * Version: 1.0.3
 * Author: Viva Wallet
 * Author URI: http://www.vivawallet.com/
 * Text Domain: woocommerce_vivawallet
 * Domain Path: /languages
 * WC tested up to: 4.4.1
 * Woo: 6137160:02eafc4556bd66f7c9fc73fd3a51749c
 *
 * @package VivaWalletForWooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}


/**
 * Required minimums and constants
 */
define( 'WC_VIVAWALLET_VERSION', '1.0.3' );


add_action( 'plugins_loaded', 'woocommerce_gateway_vivawallet_init' );

/**
 * Woocommerce_vivawallet_missing_wc_notice
 */
function woocommerce_vivawallet_missing_wc_notice() {
	/* translators: error message */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'The Viva Wallet payment gateway requires WooCommerce to work. You can download %s here.', 'woocommerce_vivawallet' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/**
 * Woocommerce_gateway_vivawallet INIT
 */
function woocommerce_gateway_vivawallet_init() {
	load_plugin_textdomain( 'woocommerce_vivawallet', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_vivawallet_missing_wc_notice' );
		return;
	}

	if ( ! class_exists( 'PGWC_Vivawallet' ) ) :

		define( 'WC_VIVAWALLET_MIN_PHP_VER', '5.6.0' );
		define( 'WC_VIVAWALLET_MIN_WC_VER', '3.0.0' );
		define( 'WC_VIVAWALLET_FUTURE_MIN_WC_VER', '4.0' );
		define( 'WC_VIVAWALLET_MAIN_FILE', __FILE__ );
		define( 'WC_VIVAWALLET_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
		define( 'WC_VIVAWALLET_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

		/**
		 * PGWC_Vivawallet
		 */
		class PGWC_Vivawallet {

			/**
			 * Instance
			 *
			 * @var Singleton The reference the *Singleton* instance of this class
			 */
			private static $instance;

			/**
			 * Get instance
			 *
			 * @return Singleton The *Singleton* instance.
			 */
			public static function get_instance() {
				if ( null === self::$instance ) {
					self::$instance = new self();
				}
				return self::$instance;
			}
			/**
			 * Clone
			 */
			private function __clone() {
			}
			/**
			 * Wakeup
			 */
			private function __wakeup() {
			}
			/**
			 * Construct
			 */
			private function __construct() {
				$this->init();
			}

			/**
			 * Init
			 */
			public function init() {
				if ( is_admin() ) {
					include_once WC_VIVAWALLET_PLUGIN_PATH . '/includes/admin/class-wc-vivawallet-source.php';
					include_once WC_VIVAWALLET_PLUGIN_PATH . '/includes/admin/vivawallet-error-page.php';
				}

				include_once WC_VIVAWALLET_PLUGIN_PATH . '/includes/class-wc-vivawallet-credentials.php';
				include_once WC_VIVAWALLET_PLUGIN_PATH . '/includes/class-wc-vivawallet.php';
				include_once WC_VIVAWALLET_PLUGIN_PATH . '/includes/admin/vivawallet-settings.php';
				include_once WC_VIVAWALLET_PLUGIN_PATH . '/includes/class-wc-vivawallet-helper.php';
				include_once WC_VIVAWALLET_PLUGIN_PATH . '/includes/class-wc-vivawallet-refund.php';

				add_filter(
					'woocommerce_payment_gateways',
					array(
						$this,
						'add_gateways',
					)
				);

				add_filter(
					'plugin_action_links_' . plugin_basename( __FILE__ ),
					array(
						$this,
						'plugin_action_links',
					)
				);

			}

			/**
			 * Add gateways
			 *
			 * @param array $gateways add vivawallet gateways.
			 *
			 * @return array
			 */
			public function add_gateways( $gateways ) {
				$gateways [] = 'WC_Vivawallet_Payment_Gateway';
				return $gateways;
			}

			/**
			 * Add plugin action links.
			 *
			 * @param array $links Links.
			 *
			 * @return array
			 */
			public function plugin_action_links( $links ) {
				$plugin_links = array(
					'<a href="admin.php?page=wc-settings&tab=checkout&section=vivawallet_native">' . esc_html__( 'Settings', 'woocommerce_vivawallet' ) . '</a>',
				);
				return array_merge( $plugin_links, $links );
			}
		}
		PGWC_Vivawallet::get_instance();
	endif;
}
