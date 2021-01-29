<?php
/**
 * Class ConditionsFactory
 *
 * @package WPDesk\FS\TableRate\Rule\Condition
 */

namespace WPDesk\FS\TableRate\Rule\Condition;

use WPDesk\FS\TableRate\Rule\Condition\Condition;
use WPDesk\FS\TableRate\Rule\Condition\None;
use WPDesk\FS\TableRate\Rule\Condition\Price;
use WPDesk\FS\TableRate\Rule\Condition\Weight;

/**
 * Can provide rules conditions.
 */
class ConditionsFactory {

	/**
	 * @return Condition[]
	 */
	public function get_conditions() {
		$none  = new None();
		$price  = new Price();
		$weight = new Weight();

		$conditions = array(
			$none->get_condition_id()   => $none,
			$price->get_condition_id()  => $price,
			$weight->get_condition_id() => $weight,
		);

		$conditions = apply_filters( 'flexible_shipping_rule_conditions', $conditions );

		return array_filter(
			$conditions,
			function ( $condition ) {
				return $condition instanceof Condition;
			}
		);
	}

}
