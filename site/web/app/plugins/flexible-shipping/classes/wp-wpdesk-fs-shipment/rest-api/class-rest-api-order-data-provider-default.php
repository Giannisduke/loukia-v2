<?php

/**
 * Default data provider. Can get data from shipment.
 */
class WPDesk_Flexible_Shipping_Rest_Api_Order_Data_Provider_Default implements WPDesk_Flexible_Shipping_Rest_Api_Order_Data_Provider {

	const COMMON_KEYS_TO_REMOVE = array(
		'_fs_method',
		'_shipping_method',
		'_package',
		'_packages',
	);

	/**
	 * Keys to remove.
	 *
	 * @var array
	 */
	protected $keys_to_remove = array();

	/**
	 * Get data from shipment.
	 *
	 * @param WPDesk_Flexible_Shipping_Shipment $shipment .
	 *
	 * @return array
	 */
	public function get_data_from_shipment( WPDesk_Flexible_Shipping_Shipment $shipment ) {
		return $this->remove_internal_data_from_shipment_data( get_post_meta( $shipment->get_id() ) );
	}

	/**
	 * Filter data.
	 *
	 * @param array $data .
	 *
	 * @return array
	 */
	protected function remove_internal_data_from_shipment_data( array $data ) {

		$keys_to_remove = array_merge( self::COMMON_KEYS_TO_REMOVE, $this->keys_to_remove );

		foreach ( $keys_to_remove as $key ) {
			if ( isset( $data[ $key ] ) ) {
				unset( $data[ $key ] );
			}
		}

		return $this->format_data( $data );
	}

	/**
	 * Format data.
	 *
	 * @param array $data .
	 *
	 * @return array
	 */
	private function format_data( array $data ) {
		$formatted_data = array();
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) && isset( $value[0] ) ) {
				$formatted_data[ $key ] = $value[0];
			} else {
				$formatted_data[ $key ] = $value;
			}
		}
		return $formatted_data;
	}

}
