<?php
/**
 * Plugin main class.
 *
 * @package WPDesk\FlexibleShippingDhl
 */

namespace WPDesk\FlexibleShippingDhl;

use DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValuesAsArray;
use DhlVendor\WPDesk\DhlExpressShippingService\DhlShippingService;
use DhlVendor\WPDesk\Logger\WPDeskLoggerFactory;
use DhlVendor\WPDesk\Notice\AjaxHandler;
use DhlVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
use DhlVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use DhlVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use DhlVendor\WPDesk\RepositoryRating\RatingPetitionNotice;
use DhlVendor\WPDesk\RepositoryRating\TimeWatcher\ShippingMethodGlobalSettingsWatcher;
use DhlVendor\WPDesk\Tracker\Deactivation\TrackerFactory;
use DhlVendor\WPDesk\WooCommerceShipping\ActivePayments;
use DhlVendor\WPDesk\WooCommerceShipping\Assets;
use DhlVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus\FieldApiStatusAjax;
use DhlVendor\WPDesk\WooCommerceShipping\OrderMetaData\AdminOrderMetaDataDisplay;
use DhlVendor\WPDesk\WooCommerceShipping\OrderMetaData\FrontOrderMetaDataDisplay;
use DhlVendor\WPDesk\WooCommerceShipping\OrderMetaData\SingleAdminOrderMetaDataInterpreterImplementation;
use DhlVendor\WPDesk\WooCommerceShipping\PluginShippingDecisions;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder;
use DhlVendor\WPDesk\WooCommerceShipping\ShopSettings;
use DhlVendor\WPDesk\WooCommerceShipping\Ups\MetaDataInterpreters\FallbackAdminMetaDataInterpreter;
use DhlVendor\WPDesk\WooCommerceShipping\Ups\MetaDataInterpreters\PackedPackagesAdminMetaDataInterpreter;
use DhlVendor\WPDesk_Plugin_Info;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * Main plugin class. The most important flow decisions are made here.
 *
 * @package WPDesk\FlexibleShippingDhl
 */
class Plugin extends AbstractPlugin implements LoggerAwareInterface, HookableCollection {

	use LoggerAwareTrait;
	use HookableParent;

	/**
	 * Plugin constructor.
	 *
	 * @param WPDesk_Plugin_Info $plugin_info Plugin info.
	 */
	public function __construct( WPDesk_Plugin_Info $plugin_info ) {
		parent::__construct( $plugin_info );
		$this->setLogger( $this->is_debug_mode() ? ( new WPDeskLoggerFactory() )->createWPDeskLogger( 'dhl' ) : new NullLogger() );

		$this->plugin_url       = $this->plugin_info->get_plugin_url();
		$this->plugin_namespace = $this->plugin_info->get_text_domain();
	}

	/**
	 * Returns true when debug mode is on.
	 *
	 * @return bool
	 */
	private function is_debug_mode() {
		$global_dhl_settings = $this->get_global_dhl_settings();
		return isset( $global_dhl_settings['debug_mode'] ) && 'yes' === $global_dhl_settings['debug_mode'];
	}


	/**
	 * Get global DHL settings.
	 *
	 * @return array
	 */
	private function get_global_dhl_settings() {
		return get_option( 'woocommerce_' . DhlShippingService::UNIQUE_ID . '_settings', array() );
	}

	/**
	 * Init plugin
	 */
	public function init() {
		$global_dhl_settings = new SettingsValuesAsArray( $this->get_global_dhl_settings() );

		$dhl_service = apply_filters( 'flexible_shipping_dhl_express_shipping_service', new DhlShippingService( $this->logger, new ShopSettings( DhlShippingService::UNIQUE_ID ) ) );

		$this->add_hookable(
			new Assets( $this->get_plugin_url() . 'vendor_prefixed/wpdesk/wp-woocommerce-shipping/assets', 'dhl' )
		);
		$this->init_repository_rating();

		$admin_meta_data_interpreter = new AdminOrderMetaDataDisplay( DhlShippingMethod::UNIQUE_ID );
		$admin_meta_data_interpreter->add_interpreter(
			new SingleAdminOrderMetaDataInterpreterImplementation(
				WooCommerceShippingMetaDataBuilder::SERVICE_TYPE,
				__( 'Service Code', 'flexible-shipping-dhl-express' )
			)
		);
		$admin_meta_data_interpreter->add_interpreter( new FallbackAdminMetaDataInterpreter() );
		$admin_meta_data_interpreter->add_hidden_order_item_meta_key( WooCommerceShippingMetaDataBuilder::COLLECTION_POINT );
		$admin_meta_data_interpreter->add_interpreter( new PackedPackagesAdminMetaDataInterpreter() );
		$this->add_hookable( $admin_meta_data_interpreter );

		$meta_data_interpreter = new FrontOrderMetaDataDisplay( DhlShippingMethod::UNIQUE_ID );
		$this->add_hookable( $meta_data_interpreter );

		/**
		 * Handles API Status AJAX requests.
		 *
		 * @var FieldApiStatusAjax $api_ajax_status_handler .
		 */
		$api_ajax_status_handler = new FieldApiStatusAjax( $dhl_service, $global_dhl_settings, $this->logger );
		$this->add_hookable( $api_ajax_status_handler );

		$plugin_shipping_decisions = new PluginShippingDecisions( $dhl_service, $this->logger );
		$plugin_shipping_decisions->set_field_api_status_ajax( $api_ajax_status_handler );

		DhlShippingMethod::set_plugin_shipping_decisions( $plugin_shipping_decisions );

		$this->add_hookable(
			new Beacon(
				'65dc4b83-7e99-4d44-b682-090048ce82db',
				array(
					'page'    => 'wc-settings',
					'tab'     => 'shipping',
					'section' => 'flexible_shipping_dhl_express',
				),
				$this->get_plugin_assets_url()
			)
		);

		$this->add_hookable( new ActivePayments\Integration( DhlShippingMethod::UNIQUE_ID ) );

		$this->add_hookable( new SettingsSidebar() );

		parent::init();
	}

	/**
	 * Show repository rating notice when time comes.
	 */
	private function init_repository_rating() {
		$this->add_hookable( new AjaxHandler( trailingslashit( $this->get_plugin_url() ) . 'vendor_prefixed/wpdesk/wp-notice/assets' ) );

		$time_tracker = new ShippingMethodGlobalSettingsWatcher( DhlShippingService::UNIQUE_ID );
		$this->add_hookable( $time_tracker );
		$this->add_hookable(
			new RatingPetitionNotice(
				$time_tracker,
				DhlShippingService::UNIQUE_ID,
				$this->plugin_info->get_plugin_name(),
				'https://wpde.sk/fs-dhl-rate'
			)
		);
	}

	/**
	 * Init hooks.
	 */
	public function hooks() {
		parent::hooks();

		add_filter( 'woocommerce_shipping_methods', array( $this, 'woocommerce_shipping_methods_filter' ), 20, 1 );

		add_action( 'admin_init', array( $this, 'init_deactivation_tracker' ) );

		$this->hooks_on_hookable_objects();
	}

	/**
	 * Init deactivation tracker.
	 */
	public function init_deactivation_tracker() {
		$deactivation_tracker = TrackerFactory::createDefaultTracker(
			'flexible-shipping-dhl-express',
			'flexible-shipping-dhl-express/flexible-shipping-dhl-express.php',
			__( 'DHL Express for WooCommerce', 'flexible-shipping-dhl-express' )
		);
		$deactivation_tracker->hooks();
	}

	/**
	 * Adds shipping method to Woocommerce.
	 *
	 * @param array $methods Methods.
	 *
	 * @return array
	 */
	public function woocommerce_shipping_methods_filter( $methods ) {
		$methods[ DhlShippingService::UNIQUE_ID ] = DhlShippingMethod::class;

		return $methods;
	}

	/**
	 * Quick links on plugins page.
	 *
	 * @param array $links .
	 *
	 * @return array
	 */
	public function links_filter( $links ) {
		$docs_link    = 'https://docs.flexibleshipping.com/category/176-dhl-express/';
		$docs_link   .= '?utm_source=dhl-express&utm_medium=quick-link&utm_campaign=docs-quick-link';
		$support_link = 'https://wpde.sk/dhl-express-repo-support';
		$settings_url = admin_url( 'admin.php?page=wc-settings&tab=shipping&section=flexible_shipping_dhl_express' );

		$external_attributes = ' target="_blank" ';

		$plugin_links = array(
			'<a href="' . $settings_url . '">' . __( 'Settings', 'flexible-shipping-dhl-express' ) . '</a>',
			'<a href="' . $docs_link . '"' . $external_attributes . '>' . __( 'Docs', 'flexible-shipping-dhl-express' ) . '</a>',
			'<a href="' . $support_link . '"' . $external_attributes . '>' . __( 'Support', 'flexible-shipping-dhl-express' ) . '</a>',
		);

		if ( ! defined( 'FLEXIBLE_SHIPPING_DHL_EXPRESS_PRO_VERSION' ) ) {
			$upgrade_link   = 'https://flexibleshipping.com/products/dhl-express-woocommerce-live-rates-pro/?utm_source=dhl-express&utm_medium=quick-link&utm_campaign=upgrade-quick-link';
			$plugin_links[] = '<a target="_blank" href="' . $upgrade_link . '" style="color:#d64e07;font-weight:bold;">' . __( 'Upgrade', 'flexible-shipping-dhl-express' ) . '</a>';
		}

		return array_merge( $plugin_links, $links );
	}

}
