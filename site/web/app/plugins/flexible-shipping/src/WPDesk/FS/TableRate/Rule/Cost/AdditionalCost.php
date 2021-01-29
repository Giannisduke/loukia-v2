<?php
/**
 * Interface AdditionalCost
 *
 * @package WPDesk\FS\TableRate\Rule\Cost
 */

namespace WPDesk\FS\TableRate\Rule\Cost;

use FSVendor\WPDesk\Forms\Field;
use FSVendor\WPDesk\Forms\FieldProvider;
use Psr\Log\LoggerInterface;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContents;

/**
 * Additional Costs Interface.
 */
interface AdditionalCost {

	/**
	 * @return string
	 */
	public function get_based_on();

	/**
	 * @return string
	 */
	public function get_name();

	/**
	 * @param ShippingContents $shipping_contents .
	 * @param array            $additional_cost_settings .
	 * @param LoggerInterface  $logger .
	 *
	 * @return float
	 */
	public function calculate_cost( ShippingContents $shipping_contents, array $additional_cost_settings, LoggerInterface $logger );

}
