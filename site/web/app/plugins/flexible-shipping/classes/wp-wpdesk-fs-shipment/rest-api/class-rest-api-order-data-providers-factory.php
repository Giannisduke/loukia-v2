<?php

/**
 * Data providers factory.
 */
class WPDesk_Flexible_Shipping_Rest_Api_Order_Data_Providers_Factory {

	/**
	 * Providers.
	 *
	 * @var WPDesk_Flexible_Shipping_Rest_Api_Order_Data_Providers_Collection
	 */
	private static $data_providers = null;

	/**
	 * Get data providers.
	 *
	 * @return WPDesk_Flexible_Shipping_Rest_Api_Order_Data_Providers_Collection
	 */
	public static function get_providers() {
		if ( empty( self::$data_providers ) ) {
			self::$data_providers = new WPDesk_Flexible_Shipping_Rest_Api_Order_Data_Providers_Collection();
		}
		return self::$data_providers;
	}

}
