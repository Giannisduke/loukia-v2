<?php
/**
 * Class None
 *
 * @package WPDesk\FS\TableRate\Rule\Condition
 */

namespace WPDesk\FS\TableRate\Rule\Condition;

use FSVendor\WPDesk\Forms\Field;
use Psr\Log\LoggerInterface;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContents;

/**
 * None Condition.
 */
class None extends AbstractCondition {

	/**
	 * None constructor.
	 */
	public function __construct() {
		$this->condition_id = 'none';
		$this->name         = __( 'Always', 'flexible-shipping' );
	}

	/**
	 * @param array            $condition_settings .
	 * @param ShippingContents $contents .
	 * @param LoggerInterface  $logger .
	 *
	 * @return bool
	 */
	public function is_condition_matched( array $condition_settings, ShippingContents $contents, LoggerInterface $logger ) {
		$logger->debug( $this->format_for_log( $condition_settings, true, '' ) );

		return true;
	}

	/**
	 * @return Field[]
	 */
	public function get_fields() {
		return array();
	}

}
