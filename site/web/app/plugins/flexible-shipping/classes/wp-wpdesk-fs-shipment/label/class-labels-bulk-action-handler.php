<?php
/**
 * Labels builders.
 *
 * @package Flexible Shipping
 */

/**
 * Can create labels for shipments.
 */
class WPDesk_Flexible_Shipping_Labels_Bulk_Action_Handler {

	/**
	 * Builders.
	 *
	 * @var WPDesk_Flexible_Shipping_Labels_Bulk_Action_Handler
	 */
	private static $labels_builder;

	/**
	 * Labels builders collection.
	 *
	 * @var WPDesk_Flexible_Shipping_Labels_Builder[]
	 */
	private $builders_collection = array();

	/**
	 * Shipments.
	 *
	 * @var WPDesk_Flexible_Shipping_Shipment_Interface[]
	 */
	private $shipments = array();

	/**
	 * Add builder to builders collection.
	 *
	 * @param WPDesk_Flexible_Shipping_Labels_Builder $builder .
	 */
	public function add_builder( WPDesk_Flexible_Shipping_Labels_Builder $builder ) {
		$this->builders_collection[ $builder->get_integration_id() ] = $builder;
	}

	/**
	 * Bulk process orders.
	 *
	 * @param array $orders_ids .
	 */
	public function bulk_process_orders( array $orders_ids ) {
		foreach ( $orders_ids as $order_id ) {
			$shipments = fs_get_order_shipments( $order_id );
			foreach ( $shipments as $shipment ) {
				$this->add_shipment( $shipment );
			}
		}
	}

	/**
	 * Add shipment to labels builder.
	 *
	 * @param WPDesk_Flexible_Shipping_Shipment $shipment .
	 */
	public function add_shipment( WPDesk_Flexible_Shipping_Shipment $shipment ) {
		if ( isset( $this->builders_collection[ $shipment->get_integration() ] ) ) {
			$this->builders_collection[ $shipment->get_integration() ]->add_shipment( $shipment );
		} else {
			$this->shipments[] = $shipment;
		}
	}

	/**
	 * Get labels for shipments from builders.
	 *
	 * @return array
	 */
	private function get_labels_for_shipments_from_builders() {
		$labels = array();
		foreach ( $this->builders_collection as $labels_builder ) {
			$labels += $labels_builder->get_labels_for_shipments();
		}
		return $labels;
	}

	/**
	 * Get labels for shipments.
	 *
	 * @return array
	 */
	public function get_labels_for_shipments() {
		$labels = $this->get_labels_for_shipments_from_builders();
		foreach ( $this->shipments as $shipment ) {
			try {
				$labels[] = $shipment->get_label();
			} catch ( Exception $e ) { // phpcs:ignore
				// do nothing.
			}
		}
		return $labels;
	}

	/**
	 * Get builders.
	 *
	 * @return WPDesk_Flexible_Shipping_Labels_Bulk_Action_Handler
	 */
	public static function get_labels_bulk_actions_handler() {
		if ( empty( static::$labels_builder ) ) {
			static::$labels_builder = new self();
		}
		return static::$labels_builder;
	}

}
