<?php

/**
 * Defines interface that REST API Order Data Provider should implement.
 */
interface WPDesk_Flexible_Shipping_Rest_Api_Order_Data_Provider {

	/**
	 * Get data from shipment.
	 *
	 * @param WPDesk_Flexible_Shipping_Shipment $shipment .
	 *
	 * @return array
	 */
	public function get_data_from_shipment( WPDesk_Flexible_Shipping_Shipment $shipment );

}
