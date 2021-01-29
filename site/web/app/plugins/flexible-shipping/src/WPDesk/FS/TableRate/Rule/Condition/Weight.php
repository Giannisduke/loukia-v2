<?php
/**
 * Class Weight
 *
 * @package WPDesk\FS\TableRate\Rule\Condition
 */

namespace WPDesk\FS\TableRate\Rule\Condition;

use FSVendor\WPDesk\Forms\Field;
use FSVendor\WPDesk\Forms\Field\InputTextField;
use Psr\Log\LoggerInterface;
use WPDesk\FS\TableRate\Exception\ConditionInvalidNumberValue;
use WPDesk\FS\TableRate\Exception\ConditionRequiredField;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContents;

/**
 * Weight condition.
 */
class Weight extends AbstractCondition {

	const MIN          = 'min';
	const MAX          = 'max';
	const CONDITION_ID = 'weight';

	/**
	 * Weight constructor.
	 */
	public function __construct() {
		$this->condition_id = self::CONDITION_ID;
		$this->name         = __( 'Weight', 'flexible-shipping' );
	}

	/**
	 * @param array            $condition_settings .
	 * @param ShippingContents $contents .
	 * @param LoggerInterface  $logger .
	 *
	 * @return bool
	 */
	public function is_condition_matched( array $condition_settings, ShippingContents $contents, LoggerInterface $logger ) {
		$min = (float) ( $condition_settings[ self::MIN ] ? $condition_settings[ self::MIN ] : 0 );
		$max = (float) ( $condition_settings[ self::MAX ] ? $condition_settings[ self::MAX ] : INF );

		$contents_weight = $contents->get_contents_weight();

		$condition_matched = $contents_weight >= $min && $contents_weight <= $max;

		$logger->debug( $this->format_for_log( $condition_settings, $condition_matched, $contents_weight ) );

		return $condition_matched;
	}

	/**
	 * @return Field[]
	 */
	public function get_fields() {
		return array(
			( new Field\InputNumberField() )
				->set_name( self::MIN )
				->add_class( 'wc_input_decimal' )
				->add_class( 'hs-beacon-search' )
				->add_class( 'parameter_min' )
				->add_data( 'beacon_search', __( 'weight is from', 'flexible-shipping' ) )
				->set_placeholder( __( 'is from', 'flexible-shipping' ) )
				->set_label( __( 'is from', 'flexible-shipping' ) )
				->add_data( 'suffix', get_option( 'woocommerce_weight_unit' ) ),
			( new Field\InputNumberField() )
				->set_name( self::MAX )
				->add_class( 'wc_input_decimal' )
				->add_class( 'hs-beacon-search' )
				->add_class( 'parameter_max' )
				->add_data( 'beacon_search', __( 'weight to', 'flexible-shipping' ) )
				->set_placeholder( __( 'to', 'flexible-shipping' ) )
				->set_label( __( 'to', 'flexible-shipping' ) )
				->add_data( 'suffix', get_option( 'woocommerce_weight_unit' ) ),
		);
	}

}
