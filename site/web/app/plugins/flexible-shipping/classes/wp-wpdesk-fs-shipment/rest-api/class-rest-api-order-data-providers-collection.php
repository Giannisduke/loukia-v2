<?php

/**
 * Data providers.
 * Collects data providers and can return provider per integration or default provider.
 */
class WPDesk_Flexible_Shipping_Rest_Api_Order_Data_Providers_Collection {

	/**
	 * Providers.
	 *
	 * @var WPDesk_Flexible_Shipping_Rest_Api_Order_Data_Provider[]
	 */
	private $providers = array();

	/**
	 * Add provider.
	 *
	 * @param string                                                $integration .
	 * @param WPDesk_Flexible_Shipping_Rest_Api_Order_Data_Provider $provider .
	 */
	public function set_provider( $integration, WPDesk_Flexible_Shipping_Rest_Api_Order_Data_Provider $provider ) {
		$this->providers[ $integration ] = $provider;
	}

	/**
	 * Get provider for integration.
	 *
	 * @param string $integration .
	 *
	 * @return WPDesk_Flexible_Shipping_Rest_Api_Order_Data_Provider
	 */
	public function get_provider_for_integration( $integration ) {
		if ( isset( $this->providers[ $integration ] ) ) {
			return $this->providers[ $integration ];
		}
		return new WPDesk_Flexible_Shipping_Rest_Api_Order_Data_Provider_Default();
	}

}
