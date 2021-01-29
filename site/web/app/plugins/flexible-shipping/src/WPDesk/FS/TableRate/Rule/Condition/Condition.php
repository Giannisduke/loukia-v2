<?php
/**
 * Interface Condition
 *
 * @package WPDesk\FS\TableRate\Rule\Condition
 */

namespace WPDesk\FS\TableRate\Rule\Condition;

use FSVendor\WPDesk\Forms\Field;
use Psr\Log\LoggerInterface;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContents;

/**
 * Condition
 */
interface Condition {

	/**
	 * @return string
	 */
	public function get_condition_id();

	/**
	 * @return string
	 */
	public function get_name();

	/**
	 * @param ShippingContents $shipping_contents .
	 * @param array            $condition_settings .
	 *
	 * @return ShippingContents
	 */
	public function process_shipping_contents( ShippingContents $shipping_contents, array $condition_settings );

	/**
	 * @param array            $condition_settings .
	 * @param ShippingContents $contents .
	 * @param LoggerInterface  $logger .
	 *
	 * @return bool
	 */
	public function is_condition_matched( array $condition_settings, ShippingContents $contents, LoggerInterface $logger );


	/**
	 * @return Field[]
	 */
	public function get_fields();

	/**
	 * @param array $condition_settings .
	 *
	 * @return array
	 */
	public function prepare_settings( $condition_settings );

}
