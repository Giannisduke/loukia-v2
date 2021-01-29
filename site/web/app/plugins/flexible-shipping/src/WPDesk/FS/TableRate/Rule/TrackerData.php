<?php
/**
 * Class TrackerData
 *
 * @package WPDesk\FS\TableRate\Rule
 */

namespace WPDesk\FS\TableRate\Rule;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can append rule data to tracker data.
 */
class TrackerData implements Hookable {

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'flexible-shipping/tracker/method-rule-data', array( $this, 'append_method_rule_data' ), 10, 2 );
	}

	/**
	 * @param array $tracker_data .
	 * @param array $rule_configuration .
	 *
	 * @return array
	 */
	public function append_method_rule_data( $tracker_data, $rule_configuration ) {

		$conditions = isset( $rule_configuration['conditions'] ) ? $rule_configuration['conditions'] : array( array( 'condition_id' => 'none' ) );
		$tracker_data = $this->append_conditions_data( $tracker_data, $conditions );

		$additional_costs = isset( $rule_configuration['additional_costs'] ) ? $rule_configuration['additional_costs'] : array();
		$tracker_data = $this->append_additional_costs_data( $tracker_data, $additional_costs );

		$special_action = ! empty( $rule_configuration['special_action'] ) ? $rule_configuration['special_action'] : 'none';
		$tracker_data = $this->append_special_action_data( $tracker_data, $special_action );

		if ( ! empty( $rule_configuration['cost_per_order'] ) ) {
			$tracker_data['cost_per_order_count'] ++;
		}

		return $tracker_data;
	}

	/**
	 * @param array $tracker_data .
	 * @param array $conditions .
	 */
	private function append_conditions_data( $tracker_data, $conditions ) {
		$conditions_count = count( $conditions );
		if ( empty( $tracker_data['conditions_per_rule'] ) ) {
			$tracker_data['conditions_per_rule'] = array();
		}
		if ( empty( $tracker_data['conditions_per_rule'][ $conditions_count ] ) ) {
			$tracker_data['conditions_per_rule'][ $conditions_count ] = 0;
		}
		$tracker_data['conditions_per_rule'][ $conditions_count ]++;

		foreach ( $conditions as $condition ) {
			$condition_id = isset( $condition['condition_id'] ) ? $condition['condition_id'] : false;
			if ( $condition_id ) {
				if ( empty( $tracker_data['based_on'][ $condition_id ] ) ) {
					$tracker_data['based_on'][ $condition_id ] = 0;
				}
				$tracker_data['based_on'][ $condition_id ] ++;

				if ( ! empty( $condition['min'] ) ) {
					$tracker_data['min_count']++;
				}

				if ( ! empty( $condition['max'] ) ) {
					$tracker_data['max_count']++;
				}

				if ( ! empty( $condition['shipping_class'] ) ) {
					$shipping_class = $condition['shipping_class'];
					if ( ! in_array( $shipping_class, array( 'all', 'any', 'none' ), true ) ) {
						$shipping_class = 'shipping_class';
					}
					if ( empty( $tracker_data['shipping_class_option'][ $shipping_class ] ) ) {
						$tracker_data['shipping_class_option'][ $shipping_class ] = 0;
					}
					$tracker_data['shipping_class_option'][ $shipping_class ] ++;
				}
			}
		}

		return $tracker_data;
	}

	/**
	 * @param array $tracker_data .
	 * @param array $additional_costs .
	 *
	 * @return array
	 */
	private function append_additional_costs_data( $tracker_data, $additional_costs ) {
		$additional_costs_count = count( $additional_costs );
		if ( empty( $tracker_data['additional_costs_per_rule'] ) ) {
			$tracker_data['additional_costs_per_rule'] = array();
		}
		if ( empty( $tracker_data['additional_costs_per_rule'][ $additional_costs_count ] ) ) {
			$tracker_data['additional_costs_per_rule'][ $additional_costs_count ] = 0;
		}
		$tracker_data['additional_costs_per_rule'][ $additional_costs_count ]++;

		$tracker_data['additional_cost_count'] += $additional_costs_count;

		return $tracker_data;
	}

	/**
	 * @param array  $tracker_data .
	 * @param string $special_action .
	 *
	 * @return array
	 */
	private function append_special_action_data( $tracker_data, $special_action ) {
		if ( ! isset( $tracker_data['special_actions'] ) ) {
			$tracker_data['special_actions'] = array();
		}
		if ( ! isset( $tracker_data['special_actions'][ $special_action ] ) ) {
			$tracker_data['special_actions'][ $special_action ] = 0;
		}
		$tracker_data['special_actions'][ $special_action ]++;
		if ( in_array( $special_action, array( 'stop', 'cancel' ), true ) ) {
			$tracker_data[ $special_action . '_count' ]++;
		}

		return $tracker_data;
	}

}
