<?php
/**
 * DHL Shipping Method.
 *
 * @package WPDesk\FlexibleShippingDhl
 */

namespace WPDesk\FlexibleShippingDhl;

use DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition;
use DhlVendor\WPDesk\DhlExpressShippingService\DhlShippingService;
use DhlVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus\FieldApiStatusAjax;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod;
use DhlVendor\WPDesk\WooCommerceShippingPro\Packer\PackerFactory;
use DhlVendor\WPDesk\WooCommerceShippingPro\Packer\PackerSettings;
use DhlVendor\WPDesk\WooCommerceShippingPro\ProShippingMethod\ProMethodFieldsFactory;
use DhlVendor\WPDesk\WooCommerceShippingPro\ShippingBuilder\WooCommerceShippingBuilder;
use WPDesk\FlexibleShippingDhl\PackerBox\BoxFactory;

/**
 * DHL Shipping Method.
 */
class DhlShippingMethod extends ShippingMethod {

	const UNIQUE_ID = DhlShippingService::UNIQUE_ID;

	/**
	 * .
	 *
	 * @var FieldApiStatusAjax
	 */
	protected static $api_status_ajax_handler;

	/**
	 * .
	 *
	 * @param int $instance_id Instance ID.
	 */
	public function __construct( $instance_id = 0 ) {
		parent::__construct( $instance_id );
		$this->title = $this->get_option( 'title', $this->title );
	}

	/**
	 * Is unit metric?
	 *
	 * @return bool
	 */
	private function is_unit_metric() {
		return isset( $this->settings[ DhlSettingsDefinition::FIELD_UNITS ] )
			? DhlSettingsDefinition::UNITS_METRIC === $this->settings[ DhlSettingsDefinition::FIELD_UNITS ]
			: true;
	}

	/**
	 * Init.
	 */
	protected function init() {
		parent::init();

		$packer_settings  = new PackerSettings( '' );
		$packaging_method = $packer_settings->get_packaging_method( $this );

		$packer_factory = new PackerFactory( $packaging_method );
		$packer         = $packer_factory->create_packer( [] );

		$this->shipping_builder = new WooCommerceShippingBuilder( $packer, $packaging_method, $this->is_unit_metric() );
	}

	/**
	 * Render shipping method settings.
	 *
	 * @throws \Exception .
	 */
	public function admin_options() {
		parent::admin_options();
		include __DIR__ . '/views/html-payment-account-number.php';
	}

}
